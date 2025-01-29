<script setup>
import { ref, computed, watch } from 'vue'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle, } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/solid'
import TextareaInput from '../TextareaInput.vue';
import PostUserHeader from './PostUserHeader.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    post: {
        type: Object,
        required: true
    },
    modelValue: Boolean
})

const emit = defineEmits(['update:modelValue'])

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
}

function submit() {
    form.put(route('post.update', props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            show.value = false;
        }
    })
}

watch(() => props.post, () => {
        form.id = props.post.id
        form.body = props.post.body
    });

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
                                    <h4>Update Post</h4>
                                    <button @click="show = false" class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                </DialogTitle>
                                <div class="p-4">
                                    <PostUserHeader :post="post" :showTime="false" class="mb-4"/>
                                    <TextareaInput v-model="form.body" class="mb-3 w-full" />                        
                                </div>
                                <div class="py-3 px-4">
                                    <button @click="submit" type="button" class="bg-[#016b83] p-2 text-white font-bold rounded-md hover:bg-[#018aa8] w-full">
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

  