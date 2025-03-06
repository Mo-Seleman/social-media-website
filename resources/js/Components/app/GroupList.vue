<script setup>
import GroupItem from './GroupItem.vue';
import TextInput from '../TextInput.vue';
import {ref} from 'vue';
import GroupModel from './GroupModel.vue';

const showNewGroupModel = ref(false)
const searchKeyword = ref('');

</script>

<template>
     <div class="col-span-3 text-white py-6 px-4">
        <div class="flex justify-between">
            <h2 class="text-2xl font-bold">My Groups</h2>
            <button @click="showNewGroupModel = true" class="text-sm bg-[#ff4f40] hover:scale-95 rounded-lg py-1 px-3 transition-all font-medium capitalize">new group</button>
        </div
            <TextInput v-model="searchKeyword" placeholder="Search For Groups" class="mt-2 w-full text-black" />
            <div class="py-3 h-[85vh]">
                <div v-if="false">
                    <p class="flex text-center justify-center bg-[#016b83] p-3 rounded-md font-bold">You're Not A Member Of Any Groups</p>
                </div>
                <div v-else>
                    <GroupItem image="https://picsum.photos/100" title="Laravel Developers"
                        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci quisquam laudantium sit." />
                    <GroupItem image="https://picsum.photos/100" title="Vue.js Developers"
                        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci quisquam laudantium sit." />
                    <GroupItem image="https://picsum.photos/100" title="Laravel Developers"
                        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci quisquam laudantium sit." />
                </div>
            </div>
        </div>
        <GroupModel v-model="showNewGroupModel"/>
</template>       