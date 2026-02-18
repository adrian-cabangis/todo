<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/Modal.vue';
import TaskCard from '@/components/TaskCard.vue';
import { Attachment, type BreadcrumbItem, Task } from '@/types';
import { ref, computed, watch } from 'vue';

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
    attachments: Attachment[];
}>();
const addTaskModal = ref(false);
const updateTaskModal = ref(false);
const openTaskModal = ref(false);
const isExpanded = ref(false);
const currentTask = ref<Task | null>(null);

const addForm = useForm<{
    title: string;
    description: string;
    deadline: string;
    priority: string;
    user_id: number | null;
    attachments: File[];
}>({
    title: '',
    description: '',
    deadline: '',
    priority: '',
    user_id: null,
    attachments: [],
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

    updateForm.put(`/tasks/${currentTask.value.id}`, {
        onSuccess: () => {
            updateTaskModal.value = false;
            currentTask.value = null;
        },
        onError: (errors) => {
            console.log(errors);
        },
    });
};

const openTask = (task: Task) => {
    currentTask.value = task;
    openTaskModal.value = true;
};

watch(openTaskModal, (val) => {
    if (!val) isExpanded.value = false;
});

function capitalize(str?: string) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
}

function handleFiles(e: Event) {
    const input = e.currentTarget as HTMLInputElement;

    if (!input.files) return;

    addForm.attachments = Array.from(input.files);
}

function formatSize(size: number) {
    if (size < 1024) return size + ' B';
    else if (size < 1048576) return (size / 1024).toFixed(1) + ' KB';
    else return (size / 1048576).toFixed(1) + ' MB';
}
</script>

<template>
    <Head title="My Tasks" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gray-100 p-8">
            <div class="mb-8 flex justify-between">
                <h1 class="text-3xl font-bold text-green-900">My Tasks</h1>
                <button
                    @click="addTaskModal = true"
                    class="cursor-pointer rounded-xl border border-green-200 bg-green-500 p-2 px-4 font-bold text-green-50 shadow-sm hover:scale-105 hover:shadow-lg"
                >
                    Add task +
                </button>
            </div>

            <div class="flex grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <h2 class="mb-4 text-xl font-bold text-green-700">
                        Pending ({{ groupedTasks.pending.length || 0 }})
                    </h2>

                    <div class="space-y-6">
                        <TaskCard
                            v-for="task in groupedTasks.pending"
                            :key="task.id"
                            :task="task"
                            @click="openTask(task)"
                        >
                            <template #actions>
                                <button
                                    @click.stop="handleUpdate(task)"
                                    class="cursor-pointer rounded-full p-1 text-xl hover:bg-green-200"
                                >
                                    ✎
                                </button>
                            </template>
                        </TaskCard>
                    </div>
                </div>
                <div>
                    <h2 class="mb-4 text-xl font-bold text-green-800">
                        Ongoing ({{ groupedTasks.ongoing.length || 0 }})
                    </h2>

                    <div class="space-y-6">
                        <TaskCard
                            v-for="task in groupedTasks.ongoing"
                            :key="task.id"
                            :task="task"
                            @click="openTask(task)"
                        >
                            <template #actions>
                                <button
                                    @click.stop="handleUpdate(task)"
                                    class="cursor-pointer rounded-full p-1 text-xl hover:bg-green-200"
                                >
                                    ✎
                                </button>
                            </template>
                        </TaskCard>
                    </div>
                </div>
                <div>
                    <h2 class="mb-4 text-xl font-bold text-green-900">
                        Completed ({{ groupedTasks.completed.length || 0 }})
                    </h2>

                    <div class="space-y-6">
                        <TaskCard
                            v-for="task in groupedTasks.completed"
                            :key="task.id"
                            :task="task"
                            @click="openTask(task)"
                        >
                            <template #actions>
                                <button
                                    @click="
                                        (e) => {
                                            e.stopPropagation();
                                            handleUpdate(task);
                                        }
                                    "
                                    class="cursor-pointer rounded-full p-1 text-xl hover:bg-green-200"
                                >
                                    ✎
                                </button>
                            </template>
                        </TaskCard>
                    </div>
                </div>
                <div>
                    <h2 class="mb-4 text-xl font-bold text-gray-700">
                        Cancelled ({{ groupedTasks.cancelled.length || 0 }})
                    </h2>

                    <div class="space-y-6">
                        <TaskCard
                            v-for="task in groupedTasks.cancelled"
                            :key="task.id"
                            :task="task"
                            @click="openTask(task)"
                        >
                            <template #actions>
                                <button
                                    @click.stop="handleUpdate(task)"
                                    class="cursor-pointer rounded-full p-1 text-xl hover:bg-green-200"
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
                <!-- Title -->
                <div>
                    <label
                        for="title"
                        class="mb-1 block text-sm font-medium text-green-900"
                        >Title</label
                    >
                    <input
                        id="title"
                        type="text"
                        v-model="addForm.title"
                        placeholder="Enter title"
                        class="w-full rounded-md border border-green-500 p-2 hover:border-green-700 focus:border-green-700 focus:ring-green-800"
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
                        class="mb-1 block text-sm font-medium text-green-900"
                        >Description</label
                    >
                    <textarea
                        id="description"
                        v-model="addForm.description"
                        placeholder="Enter description"
                        class="w-full rounded-md border border-green-500 p-2 hover:border-green-700 focus:border-green-700 focus:ring-green-800"
                        rows="4"
                    ></textarea>
                    <p
                        v-if="addForm.errors.description"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ addForm.errors.description }}
                    </p>
                </div>

                <!-- Attachments -->
                <div>
                    <label
                        for="attachments"
                        class="mb-1 block text-sm font-medium text-green-900"
                    >
                        Attach Files
                    </label>
                    <input
                        id="attachments"
                        type="file"
                        @change="handleFiles"
                        multiple
                        class="w-full rounded-md border border-green-500 p-2 hover:border-green-700 focus:border-green-700 focus:ring-green-800"
                    />
                    <p
                        v-if="addForm.errors.attachments"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ addForm.errors.attachments }}
                    </p>

                    <!-- Preview selected files -->
                    <ul
                        v-if="addForm.attachments.length"
                        class="mt-2 list-inside list-disc text-sm text-green-800"
                    >
                        <li
                            v-for="(file, index) in addForm.attachments"
                            :key="index"
                        >
                            {{ file.name }}
                        </li>
                    </ul>
                </div>

                <!-- Deadline -->
                <div>
                    <label
                        for="deadline"
                        class="mb-1 block text-sm font-medium text-green-900"
                        >Deadline</label
                    >
                    <input
                        id="deadline"
                        type="datetime-local"
                        v-model="addForm.deadline"
                        class="w-full rounded-md border border-green-500 p-2 hover:border-green-700 focus:border-green-700 focus:ring-green-800"
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
                        class="mb-1 block text-sm font-medium text-green-900"
                        >Priority</label
                    >
                    <select
                        id="priority"
                        v-model="addForm.priority"
                        class="w-full rounded-md border border-green-500 p-2 hover:border-green-700 focus:border-green-700 focus:ring-green-800"
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
                        class="cursor-pointer rounded-md border-green-500 bg-green-600 px-4 py-2 font-medium text-green-50 hover:scale-102"
                    >
                        Add Task
                    </button>
                    <button
                        @click="addTaskModal = false"
                        class="cursor-pointer rounded-md border border-gray-300 bg-gray-50 px-4 py-2 hover:scale-102 hover:bg-gray-200"
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
                        class="mb-1 block text-sm font-medium text-green-900"
                        >Title</label
                    >
                    <input
                        id="title"
                        type="text"
                        v-model="updateForm.title"
                        placeholder="Enter title"
                        class="w-full rounded-md border border-green-500 p-2 hover:border-green-700 focus:border-green-700 focus:ring-green-800"
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
                        class="mb-1 block text-sm font-medium text-green-900"
                        >Description</label
                    >
                    <textarea
                        id="description"
                        v-model="updateForm.description"
                        placeholder="Enter description"
                        class="w-full rounded-md border border-green-500 p-2 hover:border-green-700 focus:border-green-700 focus:ring-green-800"
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
                        class="mb-1 block text-sm font-medium text-green-900"
                        >Deadline</label
                    >
                    <input
                        id="deadline"
                        type="datetime-local"
                        v-model="updateForm.deadline"
                        class="w-full rounded-md border border-green-500 p-2 hover:border-green-700 focus:border-green-700 focus:ring-green-800"
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
                        class="mb-1 block text-sm font-medium text-green-900"
                        >Priority</label
                    >
                    <select
                        id="priority"
                        v-model="updateForm.priority"
                        class="w-full rounded-md border border-green-500 p-2 hover:border-green-700 focus:border-green-700 focus:ring-green-800"
                    >
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
                        class="mb-1 block text-sm font-medium text-green-900"
                        >Status</label
                    >
                    <select
                        id="status"
                        v-model="updateForm.status"
                        class="w-full rounded-md border border-green-500 p-2 hover:border-green-700 focus:border-green-700 focus:ring-green-800"
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
                        class="cursor-pointer rounded-md border-green-500 bg-green-600 px-4 py-2 font-medium text-green-50 hover:scale-102"
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
                        class="cursor-pointer rounded-md border border-gray-300 bg-gray-50 px-4 py-2 hover:scale-102 hover:bg-gray-200"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </Modal>
        <Modal v-model="openTaskModal">
            <template #title> {{ currentTask?.title }} </template>
            <h3 class="-mb-px text-lg font-bold">Assigned to:</h3>
            <p>{{ currentTask?.user?.name }}</p>
            <h3 class="-mb-px text-lg font-bold">Description:</h3>
            <div
                :class="[
                    'text-justify transition-all duration-300',
                    isExpanded
                        ? 'max-h-72 overflow-y-auto pr-2'
                        : 'overflow-hidden',
                ]"
            >
                <p
                    :class="isExpanded ? '' : 'line-clamp-3'"
                    class="whitespace-pre-wrap text-green-900"
                >
                    {{ currentTask?.description }}
                </p>
                <div class="flex justify-center">
                    <button
                        v-if="(currentTask?.description?.length ?? 0) > 120"
                        @click="isExpanded = !isExpanded"
                        class="mt-2 font-light"
                    >
                        {{ isExpanded ? 'See less ▲' : 'See more ▼' }}
                    </button>
                </div>
            </div>
            <h3 class="-mb-px text-lg font-bold">Date created:</h3>
            <p>
                {{
                    currentTask?.deadline
                        ? new Date(currentTask.deadline).toLocaleString(
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
                        : ''
                }}
            </p>
            <h3 class="-mb-px text-lg font-bold">Deadline:</h3>
            <p>
                {{
                    currentTask?.created_at
                        ? new Date(currentTask.created_at).toLocaleString(
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
                        : ''
                }}
            </p>
            <h3 class="-mb-px text-lg font-bold">Priority:</h3>
            <p>
                {{ capitalize(currentTask?.priority) }}
            </p>
            <h3 class="-mb-px text-lg font-bold">Status:</h3>
            <p>
                {{ capitalize(currentTask?.status) }}
            </p>

            <div v-if="currentTask?.attachments?.length" class="mt-4">
                <h3 class="text-lg font-semibold">Attachments:</h3>
                <ul class="mt-1 list-inside list-disc text-sm text-green-900">
                    <li v-for="att in currentTask.attachments" :key="att.id">
                        <a
                            :href="att.filepath"
                            target="_blank"
                            class="hover:underline"
                        >
                            {{ att.filename }}
                        </a>
                        <span class="text-xs text-gray-400"
                            >({{ formatSize(att.size) }})</span
                        >
                    </li>
                </ul>
            </div>
            <p v-else class="mt-2 text-sm text-gray-500">No attachments.</p>

            <div class="flex justify-end">
                <button
                    @click="
                        () => {
                            openTaskModal = false;
                        }
                    "
                    class="cursor-pointer rounded-md border border-gray-300 bg-gray-50 px-4 py-2 hover:scale-102 hover:bg-gray-200"
                >
                    Close
                </button>
            </div>
        </Modal>
    </AppLayout>
</template>
