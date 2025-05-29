<script setup>
import { ref, computed, watch } from 'vue'
import { XMarkIcon, CheckCircleIcon, PhotoIcon, CameraIcon, UserMinusIcon, UserPlusIcon } from '@heroicons/vue/24/solid'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import { usePage, useForm } from "@inertiajs/vue3"
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TabItem from './Partials/TabItem.vue'
import Edit from './Edit.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import '/resources/css/utilities/slotAnimation.css'
import PostList from '@/Components/app/PostList.vue'
import CreatePost from '@/Components/app/CreatePost.vue'
import UserListItem from '@/Components/app/UserListItem.vue'
import TextInput from '@/Components/TextInput.vue'
import PostAttachments from '@/Components/app/PostAttachments.vue'
import PhotoTimeline from './PhotoTimeline.vue'

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
    success: {
        type: String,
    },
    isCurrentUserFollower: {
        type: Boolean,
    },
    followerCount: {
        type: Number,
    },
    user: {
        type: Object, //user Is The Person Thats Logged In But They Would Be Viewing Someone Elses Acc
    },
    photos: {
        type: Array,
    },
    posts: Object,
    followers: Array,
    following: Array
});

const showNotification = ref(true);

const imagesForm = useForm({
    cover: null,
    avatar: null,
})

const coverImageSrc = ref('')
const avatarImageSrc = ref('')
const searchFollowersKeyword = ref('')
const searchFollowingKeyword = ref('')
const showAttachmentsModal = ref(false)
const previewAttachmentsPost = ref({})

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

function onAvatarChange(event) {
    imagesForm.avatar = event.target.files[0]
    if (imagesForm.avatar) {
        const reader = new FileReader()
        reader.onload = () => {
            avatarImageSrc.value = reader.result;
        }
        reader.readAsDataURL(imagesForm.avatar)
    }
}

function resetCoverImage() {
    imagesForm.cover = null;
    coverImageSrc.value = null;
}

function resetAvatarImage() {
    imagesForm.avatar = null;
    avatarImageSrc.value = null;
}

function submitCoverImage() {
    showNotification.value = true;
        imagesForm.post(route('profile.updateImages'), {
        onSuccess: (user) => {
            showNotification.value = true;
            resetCoverImage()
            setTimeout(() => {
                showNotification.value = false;
            }, 3000)
        },
    });
}

function submitAvatarImage() {
    showNotification.value = true;
        imagesForm.post(route('profile.updateImages'), {
        onSuccess: (user) => {
            showNotification.value = true;
            resetAvatarImage()
            setTimeout(() => {
                showNotification.value = false;
            }, 3000)
        },
    });
}

function toggleFollowUser(){
    const form = useForm({
        follow: !props.isCurrentUserFollower
    })

    form.post(route('user.follow', props.user.id), {
        preserveScroll: true
    })
}

const followerAnimation = ref(false);

watch(() => props.followerCount, () => {
    followerAnimation.value = true;
    setTimeout(() => {
        followerAnimation.value = false;
    }, 500); // Matches the bounce animation duration
})

function openAttachmentPreviewModal(post, index){
    showAttachmentsModal.value = true
    previewAttachmentsPost.value = {
        post,
        index
    }
};

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
                    <img :src="coverImageSrc || user.cover_url || '/img/default_cover.webp'"
                        class="w-full h-[300px] object-cover" alt="Profile Cover Photo">
                    <div class="absolute top-2 right-2">
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
                        class="relative group/avatar flex items-center justify-center mt-[-75px] ml-[48px] w-[150px] h-[150px]">
                        <img :src="avatarImageSrc || user.avatar_url || '/img/default_avatar.jpg'"
                            class="w-full h-full object-cover rounded-full" alt="User Profile Picture">
                        <button v-if="!avatarImageSrc"
                            class=" absolute left-0 top-0 right-0 bottom-0 bg-black/50 text-gray-200 rounded-full flex items-center justify-center transition-all opacity-0 group-hover/avatar:opacity-100">
                            <CameraIcon class="size-8" />
                            <input type="file" @change="onAvatarChange"
                                class="absolute left-0 top-0 right-0 bottom-0 opacity-0 cursor-pointer" />
                        </button>
                        <div v-else class="absolute top-1 right-1 flex flex-col gap-2">
                            <button @click="resetAvatarImage"
                                class="w-7 h-7 flex items-center justify-center bg-red-500/80 text-white rounded-full">
                                <XMarkIcon class="size-4" />
                            </button>
                            <button @click="submitAvatarImage"
                                class="w-7 h-7 flex items-center justify-center bg-emerald-500/80 text-white rounded-full">
                                <CheckCircleIcon class="size-4" />
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center flex-1 p-3">
                        <div>
                            <h2 class="font-bold text-lg">{{ user.name }}</h2>
                            <span class="flex items-center gap-1 text-sm text-gray-500">
                                <p :class="{ 'slot-wheel': followerAnimation }">{{ followerCount }}</p>
                                <p>Followers</p>
                            </span>
                        </div>
                        <div>
                            <PrimaryButton v-show="authUser.id !== user.id" @click="toggleFollowUser">
                                {{ isCurrentUserFollower ? 'Unfollow ' : 'Follow ' }}
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full sm:px-0">
                <TabGroup>
                    <TabList class="flex bg-white border-t-gray-200 border-2">
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
                        <Tab v-if="isMyProfile" v-slot="{ selected }" as="tamplate">
                            <TabItem text="My Profile" :selected="selected" />
                        </Tab>
                    </TabList>

                    <TabPanels class="mt-2 bg-white dark:bg-gray-200">
                        <TabPanel class="p-3 shadow">
                            <template v-if="posts">
                                <CreatePost />
                                <PostList :posts="posts.data" class="flex-1" />
                            </template>
                            <div v-else class="py-8 text-center">
                                <p> You do not have permission to view theses posts </p>
                            </div>
                        </TabPanel>
                        <TabPanel class="p-3">
                            <div v-if="followers">
                                <TextInput v-model="searchFollowersKeyword" placeholder="Search For a Follower"
                                    class="mt-2 w-full text-black" />
                                <div class="grid grid-cols-2 gap-3 py-3">
                                    <UserListItem v-for="user of followers" :user="user" :key="user.id" />
                                </div>
                            </div>
                            <div v-else>
                                <p class="p-8">No followers at the moment</p>
                            </div>
                        </TabPanel>
                        <TabPanel class="p-3 shadow">
                            <div v-if="following">
                                <TextInput v-model="searchFollowingKeyword" placeholder="Search For a User"
                                    class="mt-2 w-full text-black" />
                                <div class="grid grid-cols-2 gap-3 py-3">
                                    <UserListItem v-for="user of following" :user="user" :key="user.id" />
                                </div>
                            </div>
                            <div v-else>
                                <p class="p-8">You are not following anybody</p>
                            </div>
                        </TabPanel>
                        <TabPanel class="p-3 shadow">
                            <PhotoTimeline :photos="photos"/>
                        </TabPanel>
                        <TabPanel v-if="isMyProfile" key="posts">
                            <Edit :must-verify-email="mustVerifyEmail" :status="status" />
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
