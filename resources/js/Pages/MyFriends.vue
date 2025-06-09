<script setup>
import FollowingList from '@/Components/app/FollowingList.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  following: Array,
});

</script>

<template>
<div>
    <AuthenticatedLayout>
        <FollowingList :users="following"/>
    </AuthenticatedLayout>
  </div>
</template>