<script setup>
import { ref, computed } from 'vue';
import { XMarkIcon, CheckCircleIcon, PhotoIcon, CameraIcon } from '@heroicons/vue/24/solid';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { usePage, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TabItem from '../Profile/Partials/TabItem.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InviteUserModel from './InviteUserModel.vue';
import UserListItem from '@/Components/app/UserListItem.vue';
import TextInput from '@/Components/TextInput.vue';
import GroupForm from '@/Components/app/GroupForm.vue';

const authUser = usePage().props.auth.user; //Auth User Means Its There Account (So They Can Edit Nd What Not)

const isCurrentUserAdmin = computed(() => props.group.role === 'admin')
const isJoinedToGroup = computed(() => !!props.group.role && props.group.status === 'approved')

const props = defineProps({
    errors: Object,
    success: {
        type: String,
    },
    group: {
        type: Object, //user Is The Person Thats Logged In But They Would Be Viewing Someone Elses Acc
    },
    users: Array,
    requests: Array,
});

const showNotification = ref(true);

const imagesForm = useForm({
    cover: null,
    thumbnail: null,
});

const aboutForm = useForm({
    name: props.group.name,
    auto_approval: !!props.group.auto_approval,
    about: props.group.about,
})

const coverImageSrc = ref('');
const thumbnailImageSrc = ref('');
const showInviteUserModal = ref(false);
const searchKeyword = ref('');

function onCoverChange(event) {
    imagesForm.cover = event.target.files[0]
    if (imagesForm.cover) {
        const reader = new FileReader()
        reader.onload = () => {
            coverImageSrc.value = reader.result;
        }
        reader.readAsDataURL(imagesForm.cover)
    }
}

function onThumbnailChange(event) {
    imagesForm.thumbnail = event.target.files[0]
    if (imagesForm.thumbnail) {
        const reader = new FileReader()
        reader.onload = () => {
            thumbnailImageSrc.value = reader.result;
        }
        reader.readAsDataURL(imagesForm.thumbnail)
    }
}

function resetCoverImage() {
    imagesForm.cover = null;
    coverImageSrc.value = null;
}

function resetThumbnailImage() {
    imagesForm.thumbnail = null;
    thumbnailImageSrc.value = null;
}

function submitCoverImage() {
    showNotification.value = true;
        imagesForm.post(route('group.updateImages', props.group.slug), {
        onSuccess: () => {
            showNotification.value = true;
            resetCoverImage()
            setTimeout(() => {
                showNotification.value = false;
            }, 3000)
        },
    });
}

function submitThumbnailImage() {
    showNotification.value = true;
        imagesForm.post(route('group.updateImages', props.group.slug), {
        onSuccess: () => {
            showNotification.value = true;
            resetThumbnailImage()
            setTimeout(() => {
                showNotification.value = false;
            }, 3000)
        },
    });
}

function joinGroup(){
    const form = useForm({})
    form.post(route('group.join', props.group.slug), {
        preserveScroll: true
    })
}

function approveUser(user){
    const form = useForm({
        user_id: user.id,
        action: 'approve',
    })
    form.post(route('group.approveRequest', props.group.slug), {
        preserveScroll: true
    })
}

function rejectUser(user){
    const form = useForm({
        user_id: user.id,
        action: 'reject',
    })
    form.post(route('group.approveRequest', props.group.slug), {
        preserveScroll: true
    })
}

function onRoleChange(user, role){    
    const form = useForm({
        user_id: user.id,
        role: role.toLowerCase()
    })

    form.post(route('group.changeRole', props.group.slug), {
        preserveScroll: true
    })
}

function updateGroup(){
    aboutForm.put(route('group.update', props.group.slug), {
        preserveScroll: true
    })
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-[768px] mx-auto bg-gray-200 h-[100vh] overflow-auto">
            <div v-show="showNotification && success"
                class="my-2 py-2 px-3 font-medium text-md bg-emerald-500 text-white">
                <p>{{ success }}</p>
            </div>
            <div v-if="errors.cover" class="my-2 py-2 px-3 font-medium text-md bg-red-500 text-white">
                <p>{{ errors.cover }}</p>
            </div>
            <div class="relative bg-white">
                <div class="group">
                    <img :src="coverImageSrc || group.cover_url || '/img/default_cover.webp'"
                        class="w-full h-[300px] object-cover" alt="Profile Cover Photo">
                    <div v-if="isCurrentUserAdmin" class="absolute top-2 right-2">
                        <button v-if="!coverImageSrc"
                            class="bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-sm flex items-center gap-x-1 opacity-0 transition-all group-hover:opacity-100 hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                            </svg>
                            Update Cover Image
                            <input type="file" @change="onCoverChange"
                                class="absolute left-0 top-0 right-0 bottom-0 opacity-0 cursor-pointer" />
                        </button>
                        <div v-else class="flex gap-2">
                            <button @click="resetCoverImage"
                                class="text-md cursor-pointer bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 flex items-center gap-x-1 transition-all border-2 border-red-500 hover:scale-105">
                                <XMarkIcon class="size-4" />
                                Cancel
                            </button>
                            <button @click="submitCoverImage"
                                class="text-md cursor-pointer bg-gray-800 hover:bg-gray-900 text-gray-100 py-1 px-2 flex items-center gap-x-1 transition-all border-2 border-green-500 hover:scale-105">
                                <CheckCircleIcon class="size-4" />
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div
                        class="relative group/thumbnail flex items-center justify-center mt-[-75px] ml-[48px] w-[150px] h-[150px]">
                        <img :src="thumbnailImageSrc || group.thumbnail_url || '/img/default_avatar.jpg'"
                            class="w-full h-full object-cover rounded-full" alt="User Profile Picture">
                        <button v-if="isCurrentUserAdmin && !thumbnailImageSrc"
                            class=" absolute left-0 top-0 right-0 bottom-0 bg-black/50 text-gray-200 rounded-full flex items-center justify-center transition-all opacity-0 group-hover/thumbnail:opacity-100">
                            <CameraIcon class="size-8" />
                            <input type="file" @change="onThumbnailChange"
                                class="absolute left-0 top-0 right-0 bottom-0 opacity-0 cursor-pointer" />
                        </button>
                        <div v-else-if="isCurrentUserAdmin" class="absolute top-1 right-1 flex flex-col gap-2">
                            <button @click="resetThumbnailImage"
                                class="w-7 h-7 flex items-center justify-center bg-red-500/80 text-white rounded-full">
                                <XMarkIcon class="size-4" />
                            </button>
                            <button @click="submitThumbnailImage"
                                class="w-7 h-7 flex items-center justify-center bg-emerald-500/80 text-white rounded-full">
                                <CheckCircleIcon class="size-4" />
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center flex-1 p-3">
                        <h2 class="font-bold text-lg">{{ group.name }}</h2>
                        <PrimaryButton v-if="!authUser" :href="route('login')">Login to join this group</PrimaryButton>
                        <PrimaryButton @click="showInviteUserModal = true" v-if="isCurrentUserAdmin">Invite Users
                        </PrimaryButton>
                        <PrimaryButton v-if="authUser && !group.role && group.auto_approval" @click="joinGroup">Join
                            Group</PrimaryButton>
                        <PrimaryButton v-if="authUser && !group.role && !group.auto_approval" @click="joinGroup">Request
                            To Join</PrimaryButton>
                    </div>
                </div>
            </div>
            <div class="w-full sm:px-0">
                <TabGroup>
                    <TabList class="flex bg-white border-t-gray-200 border-2">
                        <Tab v-slot="{ selected }" as="tamplate">
                            <TabItem text="Posts" :selected="selected" />
                        </Tab>
                        <Tab v-if="isJoinedToGroup" v-slot="{ selected }" as="tamplate">
                            <TabItem text="Users" :selected="selected" />
                        </Tab>
                        <Tab v-if="isCurrentUserAdmin" v-slot="{ selected }" as="tamplate">
                            <TabItem text="Requests" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" as="tamplate">
                            <TabItem text="Photos" :selected="selected" />
                        </Tab>
                        <Tab v-if="isCurrentUserAdmin" v-slot="{ selected }" as="tamplate">
                            <TabItem text="About" :selected="selected" />
                        </Tab>
                    </TabList>

                    <TabPanels class="mt-2">
                        <TabPanel class="bg-white p-3 shadow">
                            <pre>
                                {{ users }}
                            </pre>
                        </TabPanel>
                        <TabPanel v-if="isJoinedToGroup" class="bg-white p-3 shadow">
                            <TextInput v-model="searchKeyword" placeholder="Search For a User" class="mt-2 w-full text-black"/>
                            <div class="grid grid-cols-2 gap-3 py-3">
                                <UserListItem v-for="user of users" :user="user" :key="user.id" :show-role-dropdown="isCurrentUserAdmin" :disable-role-dropdown="group.user_id === user.id" @role-change="onRoleChange"/>
                            </div>
                        </TabPanel>
                        <TabPanel v-if="isCurrentUserAdmin" class="bg-white p-3 shadow">
                            <div v-if="requests.length" class="grid grid-cols-2 gap-3 ">
                                <UserListItem v-for="user of requests" :user="user" :key="user.id" :for-approve="true" @approve="approveUser" @reject="rejectUser"/>
                            </div>
                            <div v-else class="py-8 text-center">
                                <p>No pending requests</p>
                            </div>
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            Photos Content
                        </TabPanel>
                        <TabPanel v-if="isCurrentUserAdmin" class="bg-white p-3 shadow">
                            <GroupForm :form="aboutForm" />
                            <PrimaryButton @click="updateGroup"> Submit </PrimaryButton>
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>
    <InviteUserModel v-model="showInviteUserModal" />
</template>