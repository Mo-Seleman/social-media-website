<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    group: Object,
});

</script>

<template>
    <div class="pb-3 transition-transform duration-300 transform hover:scale-[0.975] cursor-pointer">
        <Link :href="route('group.profile', group.slug)">
            <div class="flex items-start gap-3 bg-[#1A1A1A] dark:bg-white dark:text-gray-700 p-3 rounded-xl">
                <img :src="group.thumbnail_url" alt="" class="size-[45px] rounded-full object-cover" />
                <div class="flex-1">
                    <div class="flex justify-between">
                        <h3 class="uppercase text-sm">{{ group.name }}</h3>
                        <small class="capitalize">{{ group.status === 'approved' ? (group.role === 'admin' ? group.role : '') : 'pending approval' }}</small>
                    </div>
                    <p class="text-sm leading-4 text-[#8E8E8E]" v-html="group.description"></p>
                </div>
            </div>
        </Link>
    </div>
</template>
