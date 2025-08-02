<script setup>
    import Checkbox from '@/Components/Checkbox.vue';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';

    defineProps({
        canResetPassword: {
            type: Boolean,
        },
        status: {
            type: String,
        },
    });

    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = () => {
        form.post(route('login'), {
            onFinish: () => form.reset('password'),
        });
    };
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 ">
            {{ status }}
        </div>

        <div>
            <p class="text-white uppercase text-[3.1rem] font-black text-center tracking-wide font-mono mb-6">Welcome Back</p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" class="text-white text-[1rem]"/>

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-inherit text-white"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" class="text-white text-[1rem]"/>

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full bg-inherit text-white"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-white">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-white hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Forgot your password?
                </Link>

            </div>
                <button class="bg-[#000000] text-white w-full rounded-xl py-3 my-4 font-bold" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Log in
                </button>
        </form>
         <div class="w-full flex text-center">
            <Link :href="route('register')" class="bg-[#000000] text-white w-full rounded-xl py-3 flex-1 mb-3 font-bold">
                    Or Register
                </Link>
         </div>
         <div class="border-l-8 border-[#FFFD02] bg-gray-200 p-4 ">
                <h4 class="font-extrabold">Credentials For Testing</h4>
                <p>Email: john@example.com</p>
                <p>Password: Password123!</p>
            </div>
    </GuestLayout>
</template>
