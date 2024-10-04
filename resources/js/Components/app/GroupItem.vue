<script setup>

    defineProps({
        image: String,
        title: String,
        description: String,
    })
    
</script>

<template>
    <div class="pb-3 transition-transform duration-300 transform hover:scale-105 cursor-pointer">
        <div class="flex items-start gap-3 bg-[#016b83] p-3 rounded-md">
            <img :src="image" alt="" class="w-[48px] rounded-full"/>
            <div>
                <h3 class="font-bold uppercase text-md">{{ title }}</h3>
                <p class="text-sm leading-4">
                    {{ description }}
                </p>
            </div>
        </div>
    </div>
</template>
