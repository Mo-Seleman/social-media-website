<script setup>
import GroupItem from './GroupItem.vue';
import TextInput from '../TextInput.vue';
import {ref} from 'vue';


const searchKeyword = ref('');

</script>

<template>
     <div class="col-span-3 text-white py-6 px-4">
            <h2 class="text-2xl font-bold hover:text-[#ff4f40]">My Groups</h2>
            <TextInput :modal-value="searchKeyword" placeholder="Search For Groups" class="mt-2 w-full text-black"/>
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
                    <!-- <GroupItem image="https://picsum.photos/100" title="Vue.js Developers"
                        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci quisquam laudantium sit." />
                    <GroupItem image="https://picsum.photos/100" title="Laravel Developers"
                        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci quisquam laudantium sit." />
                    <GroupItem image="https://picsum.photos/100" title="Vue.js Developers"
                        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci quisquam laudantium sit." />
                    <GroupItem image="https://picsum.photos/100" title="Laravel Developers"
                        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci quisquam laudantium sit." />
                    <GroupItem image="https://picsum.photos/100" title="Vue.js Developers"
                        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci quisquam laudantium sit." /> -->
                </div>
            </div>
        </div>
</template>       