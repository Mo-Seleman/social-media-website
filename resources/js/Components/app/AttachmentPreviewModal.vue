<script setup>
import { computed } from 'vue'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle, } from '@headlessui/vue'
import { XMarkIcon, PaperClipIcon, PaperAirplaneIcon, ArrowUturnLeftIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid'
import { isImage, isVideo } from '@/helpers';

const props = defineProps({
    attachments: {
        type: Array,
        required: true
    },
    index: Number,
    modelValue: Boolean
})

const emit = defineEmits(['update:modelValue', 'update:index', 'hide'])


const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

const currentIndex = computed({
    get: () => props.index,
    set: (value) => emit('update:index', value)
})

const attachment = computed(() => {
    return props.attachments[currentIndex.value]
})

function closeModal() {
  show.value = false
  emit('update:modelValue', false)
  emit('hide')
}

function prev(){
    if(currentIndex.value === 0) return;
    currentIndex.value--;
}

function next(){
    if(currentIndex.value === props.attachments.length - 1 ) return;
    currentIndex.value++
}

</script>

<template>
    <Teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-black/25"></div>
                </TransitionChild>
                <div class="fixed flex inset-0 overflow-y-auto">
                    <div class="flex min-h-full w-full items-center justify-center p-4 text-center">
                        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel class="w-full h-full transform overflow-hidden bg-white text-left align-middle shadow-xl transition-all" >
                                <button @click="closeModal" class="absolute right-3 top-3 z-30 w-8 h-8 rounded-full bg-[#016b83] hover:bg-[#018aa8] transition flex items-center justify-center text-gray-100">
                                        <XMarkIcon class="size-7" />
                                    </button>
                                <div class="relative group h-full">
                                    <div @click="prev" class="absolute opacity-0 group-hover:opacity-100 text-white cursor-pointer flex items-center justify-center w-16 h-full left-0 bg-black/5">
                                        <ChevronLeftIcon class="size-12"/>
                                    </div>
                                    <div @click="next" class="absolute opacity-0 group-hover:opacity-100 text-white cursor-pointer flex items-center justify-center w-16 h-full right-0 bg-black/5">
                                        <ChevronRightIcon class="size-12"/>
                                    </div>
                                   <div class="flex items-center justify-center w-full h-full p-3">
                                        <img v-if="isImage(attachment)" :src="attachment.url" class="bg-cover bg-no-repeat max-w-full max-h-full">
                                        <div v-else-if="isVideo(attachment)" class="flex items-center">
                                            <video :src="attachment.url" class="h-screen" controls></video>
                                        </div>
                                        <div v-else class="p-32 aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-100 hover:text-black active:text-black cursor-pointer rounded-lg">
                                            <PaperClipIcon size="4"/>
                                            <small>{{ attachment.name }}</small>
                                        </div>
                                   </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </Teleport>
</template>

  