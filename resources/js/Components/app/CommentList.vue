<script setup>
import { ChatBubbleLeftEllipsisIcon,} from '@heroicons/vue/24/solid'
import { HeartIcon } from '@heroicons/vue/24/outline'
import TextareaInput from '../TextareaInput.vue'
import IndigoButton from './indigoButton.vue'
import EditDeleteDropdown from './EditDeleteDropdown.vue'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Disclosure, DisclosurePanel, DisclosureButton } from '@headlessui/vue'
import axiosClient from "@/axiosClient.js"

const authUser = usePage().props.auth.user;

const newCommentText = ref('')
const editingComment = ref({})
const showPostMenu = ref(false);

const props = defineProps({
    post: Object,
    data: Object,
    parentComment: {
        type: [Object, null],
        default: null
    }
})

const emit = defineEmits(['commentCreate', 'commentDelete'])

const modals = ref([]);

const togglePostMenu = (index) => {
  if (modals.value[index] === undefined) {
    modals.value[index] = false;
  }

  modals.value[index] = !modals.value[index];
};

function createComment(){
        axiosClient.post(route('post.comment.create', props.post), {
            comment: newCommentText.value,
            parent_id: props.parentComment?.id || null
        })

        .then(({ data }) => {
            newCommentText.value = ''
            props.data.comments.unshift(data)
            if(props.parentComment){
                props.parentComment.num_of_comments++
            }
            props.post.num_of_comments++; 
            emit('commentCreate', data)           
        })
    }

function deleteComment(comment) {
    if (!window.confirm('Are you sure you want to delete this comment?')) {
        return false;
    }
    axiosClient.delete(route('comment.delete', comment.id))
        .then(({ data }) => {
            const commentIndex = props.data.comments.findIndex(c => c.id === comment.id)
            props.data.comments.splice(commentIndex, 1)
            if (props.parentComment) {
                props.parentComment.num_of_comments--
            }
            props.post.num_of_comments--;
            emit('commentDelete', comment)           
        })
}

    function startCommentEdit(comment){
        console.log(comment);
        editingComment.value = {
            id: comment.id,
            comment: comment.comment.replace(/(<br\s*\/?>\s*)+/gi, '\n')
        }
    }

    function updateComment(){
        axiosClient.put(route('comment.update', editingComment.value.id), {
            comment: editingComment.value.comment
        })

        .then(({ data }) => {
            editingComment.value = null
            props.data.comments = props.data.comments.map((c) => {
                if (c.id === data.id){
                    return data
                }
                return c;
            })
        })
    }

    function sendCommentReaction(comment){
        axiosClient.post(route('comment.reaction', comment.id), {
            reaction: 'like'
        })

        .then(({ data }) => {
           comment.current_user_has_reaction = data.current_user_has_reaction
           comment.num_of_reactions = data.num_of_reactions
        })
    }

    function onCommentCreate(comment){
        if(props.parentComment){
            props.parentComment.num_of_comments++;
        }
        emit('commentCreate', comment)
    }

    function onCommentDelete(comment){
        if(props.parentComment){
            props.parentComment.num_of_comments--;
        }
        emit('commentDelete', comment)
    }

</script>

<template>
      <div class="flex gap-2 mb-3">
        <a href="javascript:void(0)">
            <img :src="authUser.avatar_url" class="w-[52px] rounded-full">
        </a>
        <div class="flex flex-1">
            <TextareaInput v-model="newCommentText" rows="1" maxlength="400" class="w-full overflow-auto resize-none rounded-r-none max-h-[85px]" placeholder="Enter your comment here"/>
            <IndigoButton @click="createComment" class="w-[100px] rounded-l-none">Submit</IndigoButton>
        </div>
    </div>
    <div class="overflow-scroll max-h-[440px]">
        <div v-for="comment of data.comments" :key="comment.id" class="mb-6">
            <div class="flex justify-between gap-2">
                <div class="flex gap-2">
                    <a href="javascript:void(0)">
                        <img :src="comment.user.avatar_url" class="w-[52px] rounded-full">
                    </a>
                    <div>
                        <h4 class="hover:underline">{{ comment.user.name }}</h4>
                        <small class="text-xs text-gray-500">{{ comment.updated_at }}</small>
                    </div>
                </div>
                <EditDeleteDropdown :user="comment.user" @edit="startCommentEdit(comment)" @delete="deleteComment(comment)"/>
            </div>
            <div v-if="editingComment && editingComment.id === comment.id" class="ml-12 max-w-[90%]">
                <TextareaInput v-model="editingComment.comment" rows="1" maxlength="400" class="w-full overflow-auto resize-none rounded-r-none max-h-[85px] px-[0.94rem]" placeholder="Enter your comment here"/>
                <div class="flex gap-2 justify-end ">
                    <button @click="editingComment = {}" class="text-gray-800">cancel</button>
                    <IndigoButton @click="updateComment" class="w-[100px] rounded-l-none">update</IndigoButton>
                </div>
            </div>
            <div v-else class="pl-16">
                <Disclosure>
                    <div class="flex items-center gap-5">
                        <div class="text-md flex flex-1 items-center [line-break:auto] min-h-[41px] max-w-[85%] pb-2" v-html="comment.comment"></div>
                        <div class="flex flex-col items-center">
                            <button @click="sendCommentReaction(comment)" class="hover:scale-[1.1] transition-all">                                
                                <HeartIcon class="size-5 stroke-[2.5]" :class="[comment.current_user_has_reaction ? 'stroke-red-500 fill-red-500' : 'stroke-gray-500']"/>
                            </button>
                            <span v-if="comment.num_of_reactions > 0" class="text-sm">{{ comment.num_of_reactions }}</span>
                        </div>
                    </div>
                    <DisclosureButton class="flex items-center gap-1 text-xs bg-[#ff4f40]/80 px-2 py-1 rounded-md text-white hover:scale-[1.1] transition-all font-bold tracking-wide w-fit">
                        <ChatBubbleLeftEllipsisIcon class="size-4"/>
                        <span class="mr-1">{{ comment.num_of_comments }}</span>
                        Comments
                    </DisclosureButton>
                    <DisclosurePanel class="mt-3">
                        <CommentList :post="post" :data="{comments: comment.comments}" :parentComment="comment" @commentCreate="onCommentCreate" @commentDelete="onCommentDelete"/>
                    </DisclosurePanel>
                </Disclosure>
            </div>
        </div>
    </div>
</template>