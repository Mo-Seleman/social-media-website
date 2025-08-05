<script setup>
import UserListItem from './UserListItem.vue';
import TextInput from '../TextInput.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    users: Array,
})

const searchKeyword = ref('');

const filteredUsers = computed(() => {
    if (!searchKeyword.value) return props.users;
    return props.users.filter(user =>
        user.name.toLowerCase().includes(searchKeyword.value.toLowerCase())
    );
});

</script>

<template>
    <div class="col-span-3 bg-[#282828] rounded-xl py-6 px-4 my-4">
        <h2 class="text-2xl font-semibold text-white dark:text-gray-700">My Friends</h2>
        <TextInput v-model="searchKeyword" placeholder="Search For Friends" class="outline-none border-none mt-2 w-full text-black bg-[#1A1A1A] placeholder:text-[#8E8E8E]" />
        <div class="py-3">
            <div v-if="false">
                <p class="flex text-center justify-center bg-[#FFFD02] text-[#282828] p-3 rounded-md font-semibold">Add Some Friends
                </p>
            </div>
            <div v-else class="space-y-3">
                <UserListItem v-for="user of filteredUsers" :user="user" class="hover:scale-[0.975] transition-all" />
            </div>
        </div>
    </div>
</template>