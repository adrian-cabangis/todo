<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/Modal.vue';
import TaskCard from '@/components/TaskCard.vue';
import { type BreadcrumbItem, Task } from '@/types';
import { ref, computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task',
    },
];

const props = defineProps<{
    tasks: Task[];
    users: {
        id: number;
        name: string;
    }[];
}>();
const addTaskModal = ref(false);
const updateTaskModal = ref(false);
const currentTask = ref<Task | null>(null);

const addForm = useForm({
    title: '',
    description: '',
    deadline: '',
    priority: '',
    user_id: null as number | null,
});

const updateForm = useForm({
    title: '',
    description: '',
    deadline: '',
    priority: '',
    user_id: null as number | null,
    status: 'pending',
});

const submitTask = () => {
    addForm.post('/tasks', {
        onSuccess: () => {
            addTaskModal.value = false;
            addForm.reset();
        },
    });
};

const groupedTasks = computed(() => ({
    pending: props.tasks.filter((t) => t.status === 'pending'),
    ongoing: props.tasks.filter((t) => t.status === 'ongoing'),
    completed: props.tasks.filter((t) => t.status === 'completed'),
    cancelled: props.tasks.filter((t) => t.status === 'cancelled'),
}));

const handleUpdate = (task: Task) => {
    currentTask.value = task;
    updateForm.title = task.title;
    updateForm.description = task.description || '';
    updateForm.deadline = task.deadline ? task.deadline.slice(0, 16) : '';
    updateForm.priority = task.priority || '';
    updateForm.status = task.status;
    updateForm.user_id = task.user_id;

    updateTaskModal.value = true;
};

const submitUpdate = () => {
    if (!currentTask.value) return;

    updateForm.put(`/tasks/${currentTask.value.id}/admin`, {
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
    <Head title="All Tasks" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gray-100 p-8">
            <div class="mb-8 flex justify-between">
                <h1 class="text-3xl font-bold text-gray-800">All Tasks</h1>
                <button
                    @click="addTaskModal = true"
                    class="h-12 cursor-pointer rounded-xl bg-white px-4 font-bold shadow-sm hover:scale-105 hover:shadow-lg"
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
                        {{ status.charAt(0).toUpperCase() + status.slice(1) }}
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
                                    class="cursor-pointer rounded-full p-1 text-xl hover:bg-gray-200"
                                >
                                    ✎
                                </button>
                            </template>
                        </TaskCard>
                    </div>
                </div>
            </div>
        </div>

        <Modal v-model="addTaskModal">
            <template #title> Create new task </template>
            <form @submit.prevent="submitTask" class="space-y-4">
                <!-- Assign To -->
                <div>
                    <label
                        for="user"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Assign To
                    </label>
                    <select
                        id="user"
                        v-model="addForm.user_id"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Select User</option>
                        <option
                            v-for="user in props.users"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }}
                        </option>
                    </select>

                    <p
                        v-if="addForm.errors.user_id"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ addForm.errors.user_id }}
                    </p>
                </div>

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
                        v-model="addForm.title"
                        placeholder="Enter title"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    />
                    <p
                        v-if="addForm.errors.title"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ addForm.errors.title }}
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
                        v-model="addForm.description"
                        placeholder="Enter description"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                        rows="4"
                    ></textarea>
                    <p
                        v-if="addForm.errors.description"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ addForm.errors.description }}
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
                        v-model="addForm.deadline"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    />
                    <p
                        v-if="addForm.errors.deadline"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ addForm.errors.deadline }}
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
                        v-model="addForm.priority"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Select priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <p
                        v-if="addForm.errors.priority"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ addForm.errors.priority }}
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        type="submit"
                        class="cursor-pointer rounded-md bg-blue-700 px-4 py-2 font-medium text-white hover:bg-blue-800"
                    >
                        Add Task
                    </button>
                    <button
                        @click="addTaskModal = false"
                        class="cursor-pointer rounded-md border px-4 py-2 hover:bg-gray-100"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </Modal>
        <Modal v-model="updateTaskModal">
            <template #title> Update Task </template>
            <form @submit.prevent="submitUpdate" class="space-y-4">
                <!-- Assign To -->
                <div>
                    <label
                        for="user"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Assign To
                    </label>
                    <select
                        id="user"
                        v-model="updateForm.user_id"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Select User</option>
                        <option
                            v-for="user in props.users"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }}
                        </option>
                    </select>

                    <p
                        v-if="updateForm.errors.user_id"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ updateForm.errors.user_id }}
                    </p>
                </div>

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
                        v-model="updateForm.title"
                        placeholder="Enter title"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    />
                    <p
                        v-if="updateForm.errors.title"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ updateForm.errors.title }}
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
                        v-model="updateForm.description"
                        placeholder="Enter description"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                        rows="4"
                    ></textarea>
                    <p
                        v-if="updateForm.errors.description"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ updateForm.errors.description }}
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
                        v-model="updateForm.deadline"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    />
                    <p
                        v-if="updateForm.errors.deadline"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ updateForm.errors.deadline }}
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
                        v-model="updateForm.priority"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Select priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <p
                        v-if="updateForm.errors.priority"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ updateForm.errors.priority }}
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
                        v-model="updateForm.status"
                        class="w-full rounded-md border border-gray-300 p-2 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="pending">Pending</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <p
                        v-if="updateForm.errors.status"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ updateForm.errors.status }}
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        type="submit"
                        class="cursor-pointer rounded-md bg-blue-700 px-4 py-2 font-medium text-white hover:bg-blue-800"
                    >
                        Update Task
                    </button>
                    <button
                        @click="
                            () => {
                                updateTaskModal = false;
                                currentTask = null;
                                updateForm.reset();
                            }
                        "
                        class="cursor-pointer rounded-md border px-4 py-2 hover:bg-gray-100"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
