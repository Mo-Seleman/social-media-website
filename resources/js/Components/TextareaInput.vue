<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    placeholder: String,
    autoResize: {
        type: Boolean,
        default: true,
    },
    modelValue: {
        type: String,
        required: true,
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

defineExpose({ focus: () => input.value.focus() });

function adjustHeight(){
    if(props.autoResize){
        input.value.style.height = 'auto';
        input.value.style.height = input.value.scrollHeight + 'px';
    }
}

function onInputChange($event){
    emit('update:modelValue', $event.target.value)
    adjustHeight()
}

onMounted(() => {
    adjustHeight()
})

</script>

<template>
    <textarea
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @input="onInputChange"
        ref="input"
        :placeholder="placeholder"
    ></textarea>
</template>
