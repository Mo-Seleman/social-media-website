<script setup>
import GroupList from '@/Components/app/GroupList.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
</script>

<template>
<div>
    <AuthenticatedLayout>
        <GroupList />
    </AuthenticatedLayout>
  </div>
</template>