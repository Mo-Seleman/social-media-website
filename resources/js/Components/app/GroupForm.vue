        
<script setup>
import TextareaInput from '../TextareaInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    form: Object
});

</script>

<template>
    <div class="mb-3">
        <label class="capitalize">Group name</label>
        <TextInput type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
    </div>
    <div class="mb-3">
        <label class="capitalize">
            <Checkbox name="remember" v-model:checked="form.auto_approval" />
            Enable auto approval
        </label>
    </div>
    <div class="mb-3">
        <label class="capitalize">About group</label>
        <TextareaInput v-model="form.about" class="w-full" />
    </div>
</template>
