<script setup>
import { ref, computed, watch } from 'vue'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle, DialogDescription, } from '@headlessui/vue'
import { XMarkIcon, PaperClipIcon, PaperAirplaneIcon, ArrowUturnLeftIcon, SparklesIcon } from '@heroicons/vue/24/solid'
import PostUserHeader from './PostUserHeader.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Editor from './Editor.vue';
import { isImage } from '@/helpers';
import axiosClient from "@/axiosClient.js"
import SkeletonLoader from '../common/SkeletonLoader.vue'
import UrlPreview from '../common/UrlPreview.vue'

const props = defineProps({
    post: {
        type: Object,
        required: true
    },
    group: {
        type: Object,
        default: null
    },
    modelValue: Boolean,
})

const attachmentExtentions = usePage().props.attachmentExtentions

const emit = defineEmits(['update:modelValue', 'hide'])

const attachmentFiles = ref([])
const attachmentErrors = ref([])
const formErrors = ref({})
const aiButtonLoading = ref(false)
const previewLoading = ref(false)

let previewUrl = ref(null)

const form = useForm({
    body: '',
    attachments: [],
    group_id: null,
    deleted_files_ids: [],
    preview: {},
    _method: 'POST',
})

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

const computedAttachments = computed(() => {
    return [...attachmentFiles.value, ...(props.post.attachments || [])]     
})

const showExtentionsText = computed(() => {
    for (let myFile of attachmentFiles.value){
        const file = myFile.file
        let parts = file.name.split('.')
        let ext = parts.pop().toLowerCase()
        if(!attachmentExtentions.includes(ext)){
            showExtentionsText.value = true;
            return true
        }
    }
    return false;
} )
 
function closeModal() {
  show.value = false
  emit('update:modelValue', false)
  emit('hide')
  resetModal()
}

function resetModal() {
    form.reset()
    formErrors.value = {}
    attachmentFiles.value = []
    showExtentionsText.value = false
    attachmentErrors.value = []
    if(props.post.attachments){
        props.post.attachments.forEach(file => file.deleted = false);
    }
}

function submit() {  
    if(props.group){
        form.group_id = props.group.id;
    }
    
    form.attachments = attachmentFiles.value.map(myFile => myFile.file)
    if(props.post.id){    
            form._method = 'PUT'
            form.post(route('post.update', props.post.id), {
            preserveScroll: true,
            onSuccess: () => {                
                show.value = false;
                closeModal()
            },
            onError: (errors) => {
             processErrors(errors)
            },
        })
    } else {
        form.post(route('post.store'), {
            preserveScroll: true,
            onSuccess: () => {
                show.value = false; 
                closeModal()           
            },
            onError: (errors) => {
                processErrors(errors)
            },
        })
    }
}

watch(() => props.post, () => {
    form.body = props.post.body || ''
})

async function onAttachmentChoose($event) {
    for (const file of $event.target.files) {
        const myFile = {
            file,
            url: await readFile(file)
        }
        attachmentFiles.value.push(myFile)
    }
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
    if(myFile.file){
        attachmentFiles.value = attachmentFiles.value.filter(f => f !== myFile);
    } else {
        form.deleted_files_ids.push(myFile.id);
        myFile.deleted = true;
    }
}

function undoDelete(myFile){
    myFile.deleted = false;
    form.deleted_files_ids = form.deleted_files_ids.filter(id => myFile.id != id)
}

function processErrors(errors){
    formErrors.value = errors
    for (const key in errors){
        if(key.includes('.')){
            const [, index] = key.split('.')
            attachmentErrors.value[index] = errors[key]
        } 
    }
}

function getAiFunction(){
    if(!form.body){
        return
    }

    aiButtonLoading.value = true;
    axiosClient.post(route('post.aiContent'), {
        prompt: form.body
    })
    .then(({data}) => {
        form.body = data.content
        aiButtonLoading.value = false;

    })
    .catch(err => {
        console.log(err)
        aiButtonLoading.value = false;

    })
}

function onInputChange($event) {

    const stripedUrl = $event.replace(/<[^>]*>/g, '').trim();

    if (!stripedUrl.startsWith('http')) {
        previewUrl.value = null;
        form.preview = {};
        previewLoading.value = false;
        return;
    }

    if (stripedUrl === previewUrl.value) return

    previewUrl.value = stripedUrl;
    form.preview = {}
    previewLoading.value = true;

        axiosClient.post(route('post.fetchUrlPreview'), {
            url: stripedUrl,
        })
            .then(({ data }) => {
                previewLoading.value = false
                form.preview = {
                    title: data['og:title'],
                    description: data['og:description'],
                    image: data['og:image']
                }
            })
            .catch(err => {
                console.log("ERROR", err);
            })
}

</script>

<template>
    <Teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-black/25" />
                </TransitionChild>
                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel class="w-full max-w-md transform overflow-hidden rounded bg-white text-left align-middle shadow-xl transition-all" >
                                <DialogTitle as="h3" class="flex items-center justify-between py-3 px-4 font-medium bg-gray-100 text-gray-900">
                                    <h4>{{ post.id ? 'Update Post' : 'Create Post' }}</h4>
                                    <button @click="closeModal" class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                </DialogTitle>
                                <div class="p-4">
                                    <PostUserHeader :post="post" :showTime="false" class="mb-4"/>

                                    <div v-if="formErrors.group_id" class="bg-red-200 border-l-[15px] border-l-red-600 text-gray-600 py-2 px-3 my-1 text-sm">
                                        <p>{{ formErrors.group_id }}</p>
                                    </div>

                                    <div class="relative group">
                                        <Editor v-model="form.body" @input="onInputChange"/>
                                        <div class="mt-2.5 bg-gray-100 rounded-lg">
                                            <SkeletonLoader v-if="previewLoading"/>
                                            <UrlPreview :preview="form.preview" :url="previewUrl"/>
                                        </div>
                                        <button @click="getAiFunction" :disabled="aiButtonLoading" class="absolute right-1 top-12 opacity-0 group-hover:opacity-100 transition-all bg-[#19c37d] hover:bg-[#10a37f] text-white p-2 rounded-lg disabled:cursor-not-allowed">
                                            <SparklesIcon v-if="!aiButtonLoading" class="size-5"/>
                                            <svg v-else class="size-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </button>
                                    </div>
                                    <div v-if="showExtentionsText" class="border-l-4 border-amber-500 bg-amber-100 py-2 px-3 mt-3 text-gray-800">
                                        Files must be one of the following extentions: <br>
                                        <small>{{ attachmentExtentions.join(', ') }}</small>
                                    </div>
                                    <div v-if="formErrors.attachments" class="border-l-4 border-red-500 bg-red-100 py-2 px-3 mt-3 text-gray-800">
                                         {{ formErrors.attachments }}
                                    </div>
                                    <div class="grid gap-3 my-3" :class="[ computedAttachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2' ]">
                                        <div v-for="(myFile, index) of computedAttachments" :key="index" class="relative">
                                        <div :class="attachmentErrors[index] ? 'border-2 border-red-500' : ''">
                                            <div v-if="myFile.deleted" class="absolute left-0 bottom-0 right-0 py-2 px-3 bg-black text-white flex justify-between items-center z-10">
                                                <p>To Be Deleted</p>
                                                <ArrowUturnLeftIcon @click="undoDelete(myFile)" class="size-4 cursor-pointer"/>
                                            </div>

                                            <button @click="removeFile(myFile)"
                                                class="absolute z-20 right-3 top-3 w-7 h-7 flex items-center justify-center bg-black/30 hover:bg-black/40 text-white rounded-full">
                                                <XMarkIcon class="size-4" />
                                            </button>

                                            <img v-if="isImage(myFile.file || myFile)" :src="myFile.url" class="bg-cover bg-no-repeat object-contain aspect-square" :class="[myFile.deleted ? 'opacity-50' : '']">
                                            <div v-else class="aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 hover:text-black active:text-black cursor-pointer px-1" :class="[myFile.deleted ? 'opacity-50' : '']" >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                </svg>
                                                <small class="text-center px-1"> {{ (myFile.file || myFile).name }} </small>
                                            </div>
                                        </div>
                                            <small class="text-red-700">{{ attachmentErrors[index] }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="py-3 px-4 flex gap-2">
                                    <button @click="submit" type="button" class="flex items-center justify-center bg-[#016b83] p-2 text-white font-medium rounded-md hover:bg-[#018aa8] w-full relative">
                                        <PaperClipIcon class="size-5 mr-1"/>
                                        Attach Files
                                        <input @click.stop @change="onAttachmentChoose" type="file" multiple class="absolute left-0 top-0 right-0 bottom-0 opacity-0">
                                    </button>
                                    <button @click="submit" type="button" class="flex items-center justify-center bg-[#016b83] p-2 text-white font-medium rounded-md hover:bg-[#018aa8] w-full">
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
