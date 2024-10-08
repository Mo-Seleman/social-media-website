<script setup>
import FollowingList from '@/Components/app/FollowingList.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
</script>

<template>
<div>
    <AuthenticatedLayout>
        <FollowingList />
    </AuthenticatedLayout>
  </div>
</template>