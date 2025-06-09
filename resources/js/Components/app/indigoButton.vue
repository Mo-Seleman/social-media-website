<script setup>

defineProps({
    type: {
        type: String,
        default: 'button'
    }
});

</script>

<template>
    <button :type="type" class="flex items-center justify-center bg-[#016b83] mobile:p-2 text-white font-bold rounded-md hover:bg-[#018aa8] w-full relative text-xs mobile:text-md">
      <slot></slot>
    </button>
</template>