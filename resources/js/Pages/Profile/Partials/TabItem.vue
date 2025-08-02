<script setup>

defineProps({
    selected: Boolean,
    text: String
});

</script>

<template>
     <button :class="[
            'px-3 py-2.5 outline-none cursor-pointer focus:outline-none focus:ring-none',
            selected
                ? 'border-b-2 border-white'
                : 'text-[#8E8E8E]',
        ]">
            {{ text }}
        </button>
</template>