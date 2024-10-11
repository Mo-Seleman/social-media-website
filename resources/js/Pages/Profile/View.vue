<script setup>
import { ref, computed } from 'vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TabItem from './Partials/TabItem.vue';
import Edit from './Edit.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const authUser = usePage().props.auth.user; //Auth User Means Its There Account (So They Can Edit Nd What Not)

const isMyProfile = computed(() => authUser && authUser.id === props.user.id)

const props = defineProps({
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

</script>

<template>
    <AuthenticatedLayout>
        <div class="w-[768px] mx-auto bg-gray-200 h-[100vh] overflow-auto">
        <div class="relative bg-white">
            <img src="https://marketplace.canva.com/EAEmGBdkt5A/3/0/1600w/canva-blue-pink-photo-summer-facebook-cover-gy8LiIJTTGw.jpg" class="w-full h-[300px] object-co" alt="Profile Cover Photo">
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
                        Posts Content
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