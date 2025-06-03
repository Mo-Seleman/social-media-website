<script setup>
import UserListItem from './UserListItem.vue';
import TextInput from '../TextInput.vue';
import { ref } from 'vue';

defineProps({
    users: Array,
})

const searchKeyword = ref('');

</script>

<template>
    <div class="col-span-3 text-white py-6 px-4">
        <h2 class="text-2xl font-semibold hover:text-[#ff4f40] dark:text-gray-700">My Friends</h2>
        <TextInput v-model="searchKeyword" placeholder="Search For Friends" class="mt-2 w-full text-black" />
        <div class="py-3">
            <div v-if="false">
                <p class="flex text-center justify-center bg-[#016b83] p-3 rounded-md font-semibold">Add Some Friends
                </p>
            </div>
            <div v-else class="space-y-3">
                <UserListItem v-for="user of users" :user="user" class="hover:scale-[0.975] transition-all" />
            </div>
        </div>
    </div>
</template>