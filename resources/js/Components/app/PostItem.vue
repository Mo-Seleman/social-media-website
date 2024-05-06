<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'

    defineProps({
        post: Object
    });

</script>

<template>
    <div class="px-6 pt-6 mb-4 bg-[#f2f8f3] rounded-md shadow">
        <div>
            <a href="javascript:void(0)" class="flex items-center gap-2">
                <img :src="post.user.avatar" class="w-[52px] rounded-full transition-all hover:animate-pulse hover:scale-105">
                <div>
                    <div class="flex flex-row gap-2 font-bold text-lg">
                        <h4 class="hover:underline">{{ post.user.name }}</h4>
                        <template v-if="post.group">
                            >
                            <a href="javascript:void(0)" class="hover:text-[#ff4f40] hover:animate-bounce">{{ post.group.name }}</a>
                        </template>
                    </div>
                    <small>{{ post.created_at }}</small>
                </div>
            </a>
        </div>
        <div>
            <Disclosure v-slot="{ open }">
                <div v-if="!open" v-html="post.body.substring(0, 150)" class="py-3"/>
                <DisclosurePanel>
                    <div v-html="post.body" class="py-3"/>
                </DisclosurePanel>
                <div class="flex justify-end">
                    <DisclosureButton class="pb-6">
                        <span class="bg-[#016b83] hover:bg-[#018aa8] text-white font-bold p-2 rounded-md">{{ open ? 'Read Less' : 'Read More' }}</span>
                    </DisclosureButton>
                </div>
            </Disclosure>
        </div>
    </div>
</template>