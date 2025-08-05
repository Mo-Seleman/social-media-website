<script setup>
import GroupItem from './GroupItem.vue';
import TextInput from '../TextInput.vue';
import {ref, computed} from 'vue';
import GroupModel from './GroupModel.vue';

const showNewGroupModel = ref(false)
const searchKeyword = ref('');

const props = defineProps({
    groups: Array,
});

function onGroupCreate(group){
    props.groups.unshift(group)
}

const filteredGroups = computed(() => {
    if (!searchKeyword.value) return props.groups;
    return props.groups.filter(group =>
        group.name.toLowerCase().includes(searchKeyword.value.toLowerCase())
    );
});

</script>

<template>
     <div class="col-span-3 text-white py-6 px-4 bg-[#282828] my-4 rounded-xl">
        <div class="flex justify-between mb-2">
            <h2 class="text-2xl font-semibold dark:text-gray-700">My Groups</h2>
            <button @click="showNewGroupModel = true" class="text-sm bg-[#FFFD02] text-[#282828] hover:scale-95 rounded-lg py-1 px-3 transition-all font-medium capitalize">new group</button>
        </div>
            <TextInput v-model="searchKeyword" placeholder="Search For Groups" class="outline-none border-none mt-2 w-full bg-[#1A1A1A] placeholder:text-[#8E8E8E]"/>
            <div class="py-3 max-h-content">
                <div v-if="!groups.length">
                    <p class="flex text-center justify-center bg-[#FFFD02] text-[#282828] dark:bg-[#EAF8FA] p-3 rounded-md font-bold">You're Not A Member Of Any Groups</p>
                </div>
                <div v-else>
                    <GroupItem v-for="group of filteredGroups" :group="group" />
                </div>
            </div>
        </div>
        <GroupModel v-model="showNewGroupModel" @create="onGroupCreate"/>
</template>       