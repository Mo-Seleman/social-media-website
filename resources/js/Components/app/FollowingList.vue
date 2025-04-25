<script setup>
import UserListItem from './UserListItem.vue';
import TextInput from '../TextInput.vue';
import {ref} from 'vue';


const user = {
    name: 'Mo Seleman',
    avatar_url: '/img/default_avatar.jpg',
    username: 'MohammedSeleman',
}


const searchKeyword = ref('');

</script>

<template>
     <div class="col-span-3 text-white py-6 px-4">
            <h2 class="text-2xl font-bold hover:text-[#ff4f40]">My Friends</h2>
            <TextInput v-model="searchKeyword" placeholder="Search For Friends" class="mt-2 w-full text-black"/>
            <div class="py-3">
                <div v-if="false">
                    <p class="flex text-center justify-center bg-[#016b83] p-3 rounded-md font-bold">Add Some Friends</p>
                </div>
                <div v-else class="space-y-3">
                    <UserListItem :user="user" class="hover:scale-[0.975] transition-all"/>
                </div>
            </div>
        </div>
</template>