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
const updateTaskModal = ref(false);
const currentTask = ref<Task | null>(null);

const form = useForm({
    title: '',
    description: '',
    deadline: '',
    priority: '',
    user_id: 0,
    status: 'pending',
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

const handleUpdate = (task: Task) => {
    currentTask.value = task;
    form.title = task.title;
    form.description = task.description || '';
    form.deadline = task.deadline ? task.deadline.slice(0, 16) : '';
    form.priority = task.priority || '';
    form.status = task.status;
    form.user_id = task.user_id;

    updateTaskModal.value = true;
};

const submitUpdate = () => {
    if (!currentTask.value) return;

    form.put(`/tasks/${currentTask.value.id}`, {
        onSuccess: () => {
            updateTaskModal.value = false;
            currentTask.value = null;
        },
        onError: (errors) => {
            console.log(errors);
        },
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
                <div class="mb-8 flex justify-between">
                    <h1 class="text-3xl font-bold text-gray-800">My Tasks</h1>
                    <button
                        @click="addTaskModal = true"
                        class="h-12 cursor-pointer rounded-xl bg-white px-4 shadow-sm hover:scale-105 hover:shadow-lg"
                    >
                        Add task +
                    </button>
                </div>

                <div class="space-y-12">
                    <!-- Loop through statuses -->
                    <div v-for="(tasks, status) in groupedTasks" :key="status">
                        <h2
                            class="mb-4 text-xl font-bold"
                            :class="{
                                'text-yellow-600': status === 'pending',
                                'text-blue-600': status === 'ongoing',
                                'text-green-600': status === 'completed',
                                'text-red-600': status === 'cancelled',
                            }"
                        >
                            {{
                                status.charAt(0).toUpperCase() + status.slice(1)
                            }}
                            ({{ tasks.length }})
                        </h2>

                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <TaskCard
                                v-for="task in tasks"
                                :key="task.id"
                                :task="task"
                            >
                                <template #actions>
                                    <button
                                        @click="handleUpdate(task)"
                                        class="rounded-full p-1 text-xl hover:bg-gray-200"
                                    >
                                        ✎
                                    </button>
                                </template>
                            </TaskCard>
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
        <Modal v-model="updateTaskModal">
            <template #title> Update Task </template>
            <form @submit.prevent="submitUpdate" class="space-y-4">
                <!-- Title -->
                <div>
                    <label
                        for="title"
                        class="mb-1 block text-sm font-medium text-gray-700"
                        >Title</label
                    >
                    <input
                        id="title"
                        type="text"
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
                        id="deadline"
                        type="datetime-local"
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

                <!-- Status -->
                <div>
                    <label
                        for="status"
                        class="mb-1 block text-sm font-medium text-gray-700"
                        >Status</label
                    >
                    <select
                        id="status"
                        v-model="form.status"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="pending">Pending</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <p
                        v-if="form.errors.status"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.status }}
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        type="submit"
                        class="rounded-md bg-blue-700 px-4 py-2 font-medium text-white hover:bg-blue-800"
                    >
                        Update Task
                    </button>
                    <button
                        @click="
                            () => {
                                updateTaskModal = false;
                                currentTask = null;
                                form.reset();
                            }
                        "
                        class="rounded-md border px-4 py-2"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
