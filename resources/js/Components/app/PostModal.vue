<script setup>
import { ref, computed, watch } from 'vue'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle, } from '@headlessui/vue'
import { XMarkIcon, PaperClipIcon, PaperAirplaneIcon } from '@heroicons/vue/24/solid'
import PostUserHeader from './PostUserHeader.vue';
import { useForm } from '@inertiajs/vue3';
import Editor from './Editor.vue';
import { isImage } from '@/helpers';

const props = defineProps({
    post: {
        type: Object,
        required: true
    },
    modelValue: Boolean
})

const emit = defineEmits(['update:modelValue'])

const attachmentFiles = ref([])

const form = useForm({
    id: null,
    body: '',
})

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

function closeModal() {
  show.value = false
  emit('update:modelValue', false)
  form.reset()
  attachmentFiles.value = []
}

function submit() {
    if(form.id){
            form.put(route('post.update', props.post.id), {
            preserveScroll: true,
            onSuccess: () => {
                show.value = false;
                form.reset()
            }
        })
    } else {
        form.post(route('post.store'), {
            preserveScroll: true,
            onSuccess: () => {
                show.value = false 
                form.reset()           
            }
        })
    }
}

watch(() => props.post, () => {
        form.id = props.post.id
        form.body = props.post.body
    })

async function onAttachmentChoose($event) {
    $event.target.files = null;
    console.log($event.target.files)
    for (const file of $event.target.files) {
        const myFile = {
            file,
            url: await readFile(file)
        }
        attachmentFiles.value.push(myFile)
    }
    console.log(attachmentFiles.value)
}

async function readFile(file) {
    return new Promise((res, rej) => {
        if (isImage(file)) {
            const reader = new FileReader();
            reader.onload = () => {
                res(reader.result)
            }
            reader.onerror = rej
            reader.readAsDataURL(file)
        } else {
            res(null)
        }
    })
} 

function removeFile(myFile){
    attachmentFiles.value = attachmentFiles.value.filter(f => f !== myFile)
};

</script>

<template>
    <Teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-10">
                <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-black/25" />
                </TransitionChild>
                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel class="w-full max-w-md transform overflow-hidden rounded bg-white text-left align-middle shadow-xl transition-all" >
                                <DialogTitle as="h3" class="flex items-center justify-between py-3 px-4 font-medium bg-gray-100 text-gray-900">
                                    <h4>{{ form.id ? 'Update Post' : 'Create Post' }}</h4>
                                    <button @click="show = false" class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                </DialogTitle>
                                <div class="p-4">
                                    <PostUserHeader :post="post" :showTime="false" class="mb-4"/>
                                    <Editor v-model="form.body"/>
                                    <div class="grid grid-cols-2 desktop:grid-cols-3 gap-3 my-3">
                                        <div v-for="(myFile, index) of attachmentFiles" :key="index" class="relative">
                                            <!-- <button :class="['w-6 h-6 absolute right-3 top-3 cursor-pointer rounded-sm', { 'text-white bg-black': showPostMenu, 'text-black bg-white': !showPostMenu }]">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                                </svg>
                                            </button>
                                            <Transition>
                                                <div v-if="showPostMenu" class="absolute right-3 top-11 cursor-pointer">
                                                    <div class="bg-white rounded-lg text-gray-600">
                                                        <p class="hover:text-black w-[130px] px-3 py-1">Download</p>
                                                        <p class="hover:text-black w-[130px] px-3 py-1">Share</p>
                                                    </div>
                                                </div>
                                            </Transition> -->

                                            <button @click="removeFile(myFile)"
                                                class="absolute z-20 right-3 top-3 w-7 h-7 flex items-center justify-center bg-black/30 hover:bg-black/40 text-white rounded-full">
                                                <XMarkIcon class="size-4" />
                                            </button>

                                            <img v-if="isImage(myFile.file)" :src="myFile.url" class="bg-cover bg-no-repeat object-cover aspect-square rounded-lg">
                                            <div v-else class="aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 hover:text-black active:text-black cursor-pointer rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                </svg>
                                                <small class="text-center px-1"> {{ myFile.file.name }} </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="py-3 px-4 flex gap-2">
                                    <button @click="submit" type="button" class="flex items-center justify-center bg-[#016b83] p-2 text-white font-bold rounded-md hover:bg-[#018aa8] w-full relative">
                                        <PaperClipIcon class="size-5 mr-1"/>
                                        Attach Files
                                        <input @click.stop @change="onAttachmentChoose" type="file" multiple class="absolute left-0 top-0 right-0 bottom-0 opacity-0">
                                    </button>
                                    <button @click="submit" type="button" class="flex items-center justify-center bg-[#016b83] p-2 text-white font-bold rounded-md hover:bg-[#018aa8] w-full">
                                        <PaperAirplaneIcon class="size-5 mr-1"/>
                                        Submit
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </Teleport>
</template>

  