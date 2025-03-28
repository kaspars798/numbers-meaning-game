<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Play from '@/pages/game/Play.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{
    question?: string;
    questionNo?: string;
    correctAnswer?: string;
    allAnswers?: Array<string>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <Play :correctAnswer="correctAnswer" :allAnswers="allAnswers">
                    <p v-if="questionNo=='1'" style="background-color: orange;">Starting a new game :)</p>
                    <p v-if="questionNo=='11'" style="background-color: green;">You won the game :)</p>
                    <p v-if="question">{{ questionNo }}. {{ question }}?</p>
                </Play>
            </div>
        </div>
    </AppLayout>
</template>
