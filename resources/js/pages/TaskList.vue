<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task List',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="All Tasks" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="max-w-sm mx-auto mt-6">
            <!-- Task Card -->
            <div class="bg-white shadow-md rounded-lg p-4 hover:shadow-xl transition-shadow duration-300">
                <!-- Header: Task Title -->
                <div class="flex justify-between items-start">
                <h3 class="text-lg font-semibold text-gray-800">Finish Laravel Inertia Feature</h3>
                <span class="text-sm text-gray-500">Due: Feb 15</span>
                </div>

                <!-- Description -->
                <p class="mt-2 text-gray-600 text-sm">
                Implement task cards with edit and delete functionality for the dashboard.
                </p>

                <!-- Tags / Priority -->
                <div class="flex mt-3 gap-2 flex-wrap">
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">High Priority</span>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">Frontend</span>
                </div>

                <!-- Footer: Actions -->
                <div class="flex justify-end mt-4 gap-3">
                <button class="px-3 py-1 text-sm text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition">Edit</button>
                <button class="px-3 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600 transition">Delete</button>
                </div>
            </div>
            </div>

        </div>
    </AppLayout>
</template>
