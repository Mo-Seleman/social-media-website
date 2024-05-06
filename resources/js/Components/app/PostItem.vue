<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'

    defineProps({
        post: Object
    });

    function isImage(attachment){
        const mime = attachment.mime.split('/');
        return mime[0].toLowerCase() === 'image';
    }

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
                        <span class="text-[#016b83] font-bold p-2 rounded-md hover:underline">{{ open ? 'Read Less' : 'Read More' }}</span>
                    </DisclosureButton>
                </div>
            </Disclosure>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div v-for="attachment of post.attachments">
                <img v-if="isImage(attachment)" :src="attachment.url">
                <div v-else>
                    {{ attachment.name }}
                </div>
            </div>
        </div>
        <div class="flex gap-3 py-4">
            <button class="bg-[#016b83] min-w-[100px] hover:bg-[#018aa8] text-white font-bold p-2 rounded-md">Like</button>
            <button class="bg-[#016b83] min-w-[100px] hover:bg-[#018aa8] text-white font-bold p-2 rounded-md">Comment</button>
        </div>
    </div>
</template>