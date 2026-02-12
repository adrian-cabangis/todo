<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem, Task } from '@/types';
import { computed, ref } from 'vue';
import Modal from '@/components/Modal.vue';
import TaskCard from '@/components/TaskCard.vue'; // make sure this points to your card component

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

const groupedTasks = computed(() => {
    return {
        pending: props.tasks.filter((t) => t.status === 'pending'),
        ongoing: props.tasks.filter((t) => t.status === 'ongoing'),
        completed: props.tasks.filter((t) => t.status === 'completed'),
        cancelled: props.tasks.filter((t) => t.status === 'cancelled'),
    };
});
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
                        All Tasks
                    </h1>
                    <button
                        @click="addTaskModal = true"
                        class="h-12 cursor-pointer rounded-xl bg-white px-4 shadow-sm hover:scale-105 hover:shadow-lg"
                    >
                        Add task +
                    </button>
                </div>

                <div class="space-y-12">
                    <!-- PENDING -->
                    <div>
                        <h2 class="mb-4 text-xl font-bold text-yellow-600">
                            Pending ({{ groupedTasks.pending.length }})
                        </h2>
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <TaskCard
                                v-for="task in groupedTasks.pending"
                                :key="task.id"
                                :task="task"
                            />
                        </div>
                    </div>

                    <!-- ONGOING -->
                    <div>
                        <h2 class="mb-4 text-xl font-bold text-blue-600">
                            Ongoing ({{ groupedTasks.ongoing.length }})
                        </h2>
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <TaskCard
                                v-for="task in groupedTasks.ongoing"
                                :key="task.id"
                                :task="task"
                            />
                        </div>
                    </div>

                    <!-- COMPLETED -->
                    <div>
                        <h2 class="mb-4 text-xl font-bold text-green-600">
                            Completed ({{ groupedTasks.completed.length }})
                        </h2>
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <TaskCard
                                v-for="task in groupedTasks.completed"
                                :key="task.id"
                                :task="task"
                            />
                        </div>
                    </div>

                    <!-- CANCELLED -->
                    <div>
                        <h2 class="mb-4 text-xl font-bold text-red-600">
                            Cancelled ({{ groupedTasks.cancelled.length }})
                        </h2>
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <TaskCard
                                v-for="task in groupedTasks.cancelled"
                                :key="task.id"
                                :task="task"
                            />
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

                <div class="flex justify-end gap-3">
                    <button
                        type="submit"
                        class="rounded-md bg-blue-700 px-4 py-2 font-medium text-white transition-colors hover:bg-blue-700"
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
            </form>
        </Modal>
    </AppLayout>
</template>
