<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem, Task } from '@/types';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref } from 'vue';
import Modal from '@/components/Modal.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'All Task',
        href: dashboard().url,
    },
];

const props = defineProps<{
    tasks: Task[];
}>();

const addTaskModal = ref(false);

const form = useForm({
    title: '',
    description: '',
    deadline: '',
    priority: '',
    user_id: 0,
});

const submitTask = () => {
    form.post('/tasks', {
        onSuccess: () => (addTaskModal.value = false),
    });
};
</script>

<template>
    <Head title="All Task" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="min-h-screen bg-gray-100 p-8">
                <div class="flex justify-between">
                    <h1 class="mb-8 text-3xl font-bold text-gray-800">
                        My Tasks
                    </h1>
                    <button
                        @click="addTaskModal = true"
                        class="h-12 cursor-pointer rounded-xl bg-white px-4 shadow-sm hover:scale-105 hover:shadow-lg"
                    >
                        Add task +
                    </button>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="task in tasks"
                        :key="task.id"
                        class="transform rounded-xl border-l-8 bg-white p-5 shadow-lg transition hover:-translate-y-1 hover:shadow-2xl"
                        :class="
                            task.priority === 'high'
                                ? 'border-red-500'
                                : task.priority === 'medium'
                                  ? 'border-yellow-500'
                                  : 'border-green-500'
                        "
                    >
                        <div class="mb-3 flex items-center justify-between">
                            <div class="flex-col">
                                <h2 class="text-m font-semibold text-gray-600">
                                    {{ task.user?.email }}
                                </h2>
                                <h2 class="text-xl font-semibold text-gray-800">
                                    {{ task.title }}
                                </h2>
                            </div>
                            <span
                                class="rounded-full px-3 py-1 text-sm font-medium"
                                :class="
                                    task.status === 'completed'
                                        ? 'bg-green-100 text-green-800'
                                        : task.status === 'ongoing'
                                          ? 'bg-blue-100 text-blue-800'
                                          : 'bg-yellow-100 text-yellow-800'
                                "
                            >
                                {{
                                    task.status.charAt(0).toUpperCase() +
                                    task.status.slice(1)
                                }}
                            </span>
                        </div>

                        <p class="mb-4 text-gray-600">{{ task.description }}</p>

                        <div
                            class="flex items-center justify-between text-sm text-gray-500"
                        >
                            <span>
                                📅
                                {{
                                    new Date(task.deadline).toLocaleDateString(
                                        'en-US',
                                        {
                                            month: 'short',
                                            day: 'numeric',
                                            year: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            hour12: true,
                                        },
                                    )
                                }}
                            </span>
                            <span>
                                🚩
                                {{
                                    task.priority.charAt(0).toUpperCase() +
                                    task.priority.slice(1)
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Modal v-model="addTaskModal">
            <template #title> Create new task </template>

            <form @submit.prevent="submitTask" class="space-y-4">
                <!-- Title -->
                <div>
                    <label
                        for="title"
                        class="mb-1 block text-sm font-medium text-gray-700"
                        >Title</label
                    >
                    <input
                        type="text"
                        id="title"
                        v-model="form.title"
                        placeholder="Enter title"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    />
                    <p
                        v-if="form.errors.title"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.title }}
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label
                        for="description"
                        class="mb-1 block text-sm font-medium text-gray-700"
                        >Description</label
                    >
                    <textarea
                        id="description"
                        v-model="form.description"
                        placeholder="Enter description"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                        rows="4"
                    ></textarea>
                    <p
                        v-if="form.errors.description"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.description }}
                    </p>
                </div>

                <!-- Deadline -->
                <div>
                    <label
                        for="deadline"
                        class="mb-1 block text-sm font-medium text-gray-700"
                        >Deadline</label
                    >
                    <input
                        type="datetime-local"
                        id="deadline"
                        v-model="form.deadline"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    />
                    <p
                        v-if="form.errors.deadline"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.deadline }}
                    </p>
                </div>

                <!-- Priority -->
                <div>
                    <label
                        for="priority"
                        class="mb-1 block text-sm font-medium text-gray-700"
                        >Priority</label
                    >
                    <select
                        id="priority"
                        v-model="form.priority"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Select priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <p
                        v-if="form.errors.priority"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.priority }}
                    </p>
                </div>
            </form>

            <template #footer>
                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="rounded-md bg-blue-600 px-4 py-2 font-medium text-white transition-colors hover:bg-blue-700"
                    >
                        Add Task
                    </button>
                    <button
                        @click="addTaskModal = false"
                        class="mr-2 cursor-pointer rounded-md border px-4 py-2"
                    >
                        Cancel
                    </button>
                </div>
            </template>
        </Modal>
    </AppLayout>
</template>
