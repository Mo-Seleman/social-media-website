<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GroupList from '@/Components/app/GroupList.vue';

const props = defineProps({
  groups: Array
});

</script>

<template>
<div>
    <AuthenticatedLayout>
        <GroupList :groups="groups"/>
    </AuthenticatedLayout>
  </div>
</template>