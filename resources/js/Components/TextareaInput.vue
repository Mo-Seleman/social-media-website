<script setup>
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    placeholder: String,
    maxInput: Number,
    autoResize: {
        type: Boolean,
        default: true,
    },
    modelValue: {
        type: String,
        required: false,
    },
});

const emit = defineEmits([
    'update:modelValue',
]);

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

watch(() => props.modelValue, () => {
    setTimeout(() => {
        adjustHeight();
    }, 10)
})

defineExpose({ focus: () => input.value.focus() });

function adjustHeight(){
    if(props.autoResize){
        input.value.style.height = 'auto';
        input.value.style.height = (input.value.scrollHeight + 1) + 'px';
    }
}

function onInputChange($event){
    emit('update:modelValue', $event.target.value)
}

onMounted(() => {
    adjustHeight()
});

</script>

<template>
    <textarea
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @input="onInputChange"
        ref="input"
        :placeholder="placeholder"
        :maxlength="maxInput"
    ></textarea>
</template>
