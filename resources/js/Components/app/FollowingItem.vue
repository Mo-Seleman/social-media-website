<script setup>

    defineProps({
        image: String,
        title: String,
    })
    
</script>

<template>
    <div class="pb-3 transition-transform duration-300 transform hover:scale-105 cursor-pointer">
        <div class="flex items-center gap-3 bg-[#016b83] p-3 rounded-md">
            <img :src="image" alt="" class="w-[38px] rounded-full"/>
            <div>
                <h3 class="font-bold text-lg">{{ title }}</h3>
            </div>
        </div>
    </div>
</template>
