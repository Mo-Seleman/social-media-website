<script setup>

defineProps({
    type: {
        type: String,
        default: 'button'
    }
});

</script>

<template>
    <button :type="type" class="flex items-center justify-center bg-[#016b83] p-2 text-white font-bold rounded-md hover:bg-[#018aa8] w-full relative">
      <slot></slot>
    </button>
</template>