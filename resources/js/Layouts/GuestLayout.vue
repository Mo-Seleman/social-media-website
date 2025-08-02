<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen h-screen grid grid-cols-5 bg-[#282828]">
       <div class="p-12 col-span-2">
            <div>
                <Link href="/">
                    <ApplicationLogo class="w-20 h-20 fill-current text-[#FFFD02] mb-12" />
                </Link>
            </div>
            <div class="mt-4 px-6 py-4 overflow-hidden sm:rounded-lg">
                <slot />
            </div>
       </div>
        <div class="col-span-3 overflow-y-hidden">
            <img src="https://media.craiyon.com/2025-04-27/p2cPqYXiQDGpDJq2n5BbSg.webp" alt="Cool Profile Picture" class="rounded-xl py-2 h-full  w-full object-cover">
        </div>
    </div>
</template>
