<script setup>
import { ref, computed } from 'vue';
import { ClassicEditor, Essentials, Paragraph, Bold, Italic, Heading, List, Indent, Link, BlockQuote, MediaEmbed } from 'ckeditor5';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import axiosClient from "@/axiosClient.js"


import 'ckeditor5/ckeditor5.css';

const data = ref( '<p>Hello world!</p>' );

const config = computed( () => {
    return {
        licenseKey: 'GPL',
        plugins: [ Essentials, Paragraph, Bold, Italic, Heading, List, Indent, Link, BlockQuote ],
        toolbar: [ 'heading', '|', 'undo', 'redo', '|', 'bold', 'italic', '|', 'numberedList', 'bulletedList', 'link', '|', 'outdent', 'indent', 'blockQuote' ],
        mediaEmbed: {
            removeProviders: [ 'instagram', 'twitter', 'googleMaps', 'flickr', 'facebook', 'dailymotion', 'spotify', 'youtube', 'vimeo']
        },
    };
} );

</script>

<template>
    <ckeditor
        v-model="data"
        :editor="ClassicEditor"
        :config="config"
    />
</template>
