<script setup>

defineProps({
    group: Object,
});

</script>

<template>
    <div class="pb-3 transition-transform duration-300 transform hover:scale-105 cursor-pointer">
        <div class="flex items-start gap-3 bg-[#016b83] p-3 rounded-md">
            <img :src="group.thumbnail_url" alt="" class="w-[48px] rounded-full" />
            <div class="flex-1">
                <div class="flex justify-between">
                    <h3 class="font-bold uppercase text-md">{{ group.name }}</h3>
                    <small class="capitalize">{{ group.status === 'approved' ? (group.role === 'admin' ? group.role : '') : 'pending approval' }}</small>
                </div>
                <p class="text-sm leading-4">
                    {{ group.description }}
                </p>
            </div>
        </div>
    </div>
</template>
