<script setup>
import { Slide } from 'vue3-burger-menu';

const menuOptions = [
  { id: 'my-groups', label: 'My Groups', href: '/groups' },
  { id: 'my-friends', label: 'My Friends', href: '/friends' }
];
</script>

<template>
    <nav class="burger-menu tablet:hidden bg-[#112231] h-[85px] sticky top-0 overflow-hidden z-[999] flex flex-col items-center justify-center">
                <Slide>
                    <span v-for="option in menuOptions">
                        <a :id="option.id" :href="option.href">
                            <span>{{ option.label }}</span>
                        </a>
                    </span>
                </Slide>
                <div>
                    <h1 class="text-[1.4rem] text-[#f2f8f3] font-semibold">Home</h1>
                </div>
            </nav>
</template>


<style>

    .bm-burger-bars {
      background-color: rgb(242 248 243);
    }

    .bm-burger-button {
        top: 25px;
    }

</style>
