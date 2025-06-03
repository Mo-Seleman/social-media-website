<script setup>
import { ref, computed } from 'vue'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle, } from '@headlessui/vue'
import { BookmarkIcon, XMarkIcon } from '@heroicons/vue/24/solid'
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: Boolean,
})

const page = usePage();

const emit = defineEmits(['update:modelValue', 'hide', 'create'])

const formErrors = ref({})

const form = useForm({
    email: '',
    auto_approval: true,
    about: '',
})

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})
 
function closeModal() {
  show.value = false
  emit('update:modelValue', false)
  emit('hide')
  resetModal()
}

function resetModal() {
    form.reset()
    formErrors.value = []
}

function submit() {     
   form.post(route('group.inviteUsers' , page.props.group.slug), {
        onSuccess(res){
            console.log(res);
            closeModal();
        },
        onError(res){
            console.log(res);
        }
   });
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
                                    <h4 class="capitalize">Invite users</h4>
                                    <button @click="closeModal" class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                </DialogTitle>
                                <div class="p-4">
                                    <div class="mb-3">
                                        <label class="capitalize">Username or email</label>
                                        <TextInput type="text" class="mt-1 block w-full" :class="page.props.errors.email ? 'border-2 border-red-500 focus:border-red-500 focus:ring-red-500' : '' " v-model="form.email" required autofocus />
                                        <div class="text-red-500">{{ page.props.errors.email }}</div>
                                    </div>
                                </div>
                                <div class="py-3 px-4 flex justify-end gap-2">
                                    <button class="bg-[#016b83] min-w-[100px] hover:scale-[0.96] transition-all text-white font-bold p-2 rounded-md flex justify-center items-center gap-1">
                                        <XMarkIcon class="size-4"/>
                                        Cancel
                                    </button>
                                    <button @click="submit" type="button" class="min-w-[100px] flex items-center justify-center bg-[#016b83] p-2 text-white font-bold rounded-md hover:scale-[0.96] transition-all">
                                        <BookmarkIcon class="size-4 mr-1"/>
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

  