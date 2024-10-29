<script setup>
import { ref, computed } from 'vue';
import { XMarkIcon, CheckCircleIcon } from '@heroicons/vue/24/solid';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { usePage, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TabItem from './Partials/TabItem.vue';
import Edit from './Edit.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const authUser = usePage().props.auth.user; //Auth User Means Its There Account (So They Can Edit Nd What Not)

const isMyProfile = computed(() => authUser && authUser.id === props.user.id)

const props = defineProps({
    errors: Object,
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    user: {
        type: Object, //user Is The Person Thats Logged In But They Would Be Viewing Someone Elses Acc
    },
});

const showNotification = ref(true);

const imagesForm = useForm({
    cover: null,
    avatar: null,
});

const coverImageSrc = ref('');

function onCoverChange(event){
    imagesForm.cover = event.target.files[0]
    if( imagesForm.cover ){
        const reader = new FileReader()
        reader.onload = () => {
            coverImageSrc.value = reader.result;
        }
        reader.readAsDataURL(imagesForm.cover)
    }
}

function cancelCoverImage(){
    imagesForm.cover = null;
    coverImageSrc.value = null;
}

function submitCoverImage(){
    console.log(imagesForm.cover)
    imagesForm.post(route('profile.updateImage'), {
        onSuccess: (user) => {
                cancelCoverImage()
                setTimeout(() => {
                    showNotification.value = false;
                }, 3000)
        },
    });
}

</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-[768px] mx-auto bg-gray-200 h-[100vh] overflow-auto">
        <div v-show="showNotification && status === 'cover-image-updated'"
             class="my-2 py-2 px-3 font-medium text-md bg-emerald-500 text-white">
            <p>Cover Image Has Been Successfully Updated</p>
        </div>
        <div v-if="errors.cover"
                class="my-2 py-2 px-3 font-medium text-md bg-red-500 text-white">
                <p>{{ errors.cover }}</p>
            </div>
        <div class="relative bg-white">
            <div class="group">
                <img :src="coverImageSrc || user.cover_url || '/img/default_cover.webp'" class="w-full h-[300px] object-cover" alt="Profile Cover Photo">
                <div class="absolute top-2 right-2">
                    <button v-if="!coverImageSrc || status === 'cover-image-updated'" class="bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-sm flex items-center gap-x-1 opacity-0 transition-all group-hover:opacity-100 hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>
                    Update Cover Image
                    <input type="file" @change="onCoverChange"
                        class="absolute left-0 top-0 right-0 bottom-0 opacity-0 cursor-pointer" />
                </button>
                <div v-else class="flex gap-2">
                    <button
                        @click="cancelCoverImage" 
                        class="text-md cursor-pointer bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 flex items-center gap-x-1 transition-all border-2 border-red-500 hover:scale-105">
                        <XMarkIcon class="size-4"/>
                        Cancel
                    </button>
                    <button 
                        @click="submitCoverImage" 
                        class="text-md cursor-pointer bg-gray-800 hover:bg-gray-900 text-gray-100 py-1 px-2 flex items-center gap-x-1 transition-all border-2 border-green-500 hover:scale-105">
                        <CheckCircleIcon class="size-4"/>
                        Submit
                    </button>
                </div>
                </div>
            </div>
            <div class="flex">
                <img src="https://cdn-icons-png.flaticon.com/512/6858/6858504.png" class="ml-[48px] w-[150px] h-[150px] mt-[-75px]" alt="User Profile Picture">
                <div class="flex justify-between items-center flex-1 p-3">
                    <h2 class="font-bold text-lg">{{ user.name }}</h2>
                    <PrimaryButton v-if="isMyProfile">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                                Edit Profile
                    </PrimaryButton>
                </div>
            </div>
        </div>
        <div class="w-full sm:px-0">
            <TabGroup>
                <TabList class="flex bg-white border-t-gray-200 border-2">
                    <Tab v-if="isMyProfile" v-slot="{ selected }" as="tamplate">
                       <TabItem text="About" :selected="selected" />
                    </Tab>
                    <Tab v-slot="{ selected }" as="tamplate">
                       <TabItem text="Posts" :selected="selected" />
                    </Tab>
                    <Tab v-slot="{ selected }" as="tamplate">
                        <TabItem text="Followers" :selected="selected" />
                    </Tab>
                    <Tab v-slot="{ selected }" as="tamplate">
                        <TabItem text="Following" :selected="selected" />
                    </Tab>
                    <Tab v-slot="{ selected }" as="tamplate">
                        <TabItem text="Photos" :selected="selected" />
                    </Tab>
                </TabList>

                <TabPanels class="mt-2">
                    <TabPanel v-if="isMyProfile" key="posts">
                        <Edit :must-verify-email="mustVerifyEmail" :status="status"/>
                    </TabPanel>
                    <TabPanel class="bg-white p-3 shadow">
                        <pre>
                            {{ user }}
                        </pre>
                    </TabPanel>
                    <TabPanel class="bg-white p-3 shadow">
                        Followers Content
                    </TabPanel>
                    <TabPanel class="bg-white p-3 shadow">
                        Following Content
                    </TabPanel>
                    <TabPanel class="bg-white p-3 shadow">
                        Photos Content
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>
    </div>
    </AuthenticatedLayout>
</template>