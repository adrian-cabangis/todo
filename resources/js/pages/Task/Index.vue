<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/Modal.vue';
import TaskCard from '@/components/TaskCard.vue';
import { Attachment, type BreadcrumbItem, Task } from '@/types';
import { ref, computed, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [];

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
const updateAttachments = ref<
    {
        file: File;
        url: string;
        isImage: boolean;
    }[]
>([]);

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

const updateForm = useForm<{
    title: string;
    description: string;
    deadline: string;
    priority: string;
    user_id: number | null;
    status: string;
    attachments: File[];
    deleted_attachments: number[];
}>({
    title: '',
    description: '',
    deadline: '',
    priority: '',
    user_id: null,
    status: 'pending',
    attachments: [],
    deleted_attachments: [],
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

function submitUpdate() {
    if (!currentTask.value) return;

    console.log(updateForm);

    updateForm.post(`/tasks/${currentTask.value.id}/admin`, {
        forceFormData: true, // required for files
        onSuccess: () => {
            updateTaskModal.value = false;
            currentTask.value = null;
            updateForm.reset();
            updateAttachments.value = [];
        },
    });
}

const openTask = (task: Task) => {
    currentTask.value = task;
    openTaskModal.value = true;
};

watch(openTaskModal, (val) => {
    if (!val) isExpanded.value = false;
});

watch(addTaskModal, (val) => {
    if (!val) {
        newAddAttachments.value.forEach((att) => URL.revokeObjectURL(att.url));
        newAddAttachments.value = [];
        addForm.reset();
    }
});

watch(updateTaskModal, (val) => {
    if (!val) {
        updateAttachments.value.forEach((att) => URL.revokeObjectURL(att.url));
        updateAttachments.value = [];
        currentTask.value = null;
        updateForm.reset();
    }
});

function capitalize(str?: string) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
}

const newAddAttachments = ref<{ file: File; url: string; isImage: boolean }[]>(
    [],
);

function handleFiles(e: Event) {
    const input = e.currentTarget as HTMLInputElement;
    if (!input.files) return;

    const filesArray = Array.from(input.files);

    filesArray.forEach((file) => {
        newAddAttachments.value.push({
            file,
            url: URL.createObjectURL(file),
            isImage: file.type.startsWith('image/'),
        });
    });

    addForm.attachments = [...addForm.attachments, ...filesArray];

    input.value = '';
}

function removeAddAttachment(index: number) {
    const att = newAddAttachments.value[index];
    if (!att) return;

    URL.revokeObjectURL(att.url);
    newAddAttachments.value.splice(index, 1);

    addForm.attachments = addForm.attachments.filter((f) => f !== att.file);
}

function handleUpdateFiles(e: Event) {
    const input = e.currentTarget as HTMLInputElement;
    if (!input.files) return;

    const filesArray = Array.from(input.files);

    filesArray.forEach((file) => {
        updateAttachments.value.push({
            file,
            url: URL.createObjectURL(file),
            isImage: file.type.startsWith('image/'),
        });
    });

    updateForm.attachments = [...updateForm.attachments, ...filesArray];

    input.value = '';
}

function removeNewAttachment(index: number) {
    const att = updateAttachments.value[index];
    if (!att) return;

    URL.revokeObjectURL(att.url);
    updateAttachments.value.splice(index, 1);

    updateForm.attachments = updateForm.attachments.filter(
        (f) => f !== att.file,
    );
}

function deleteExistingAttachment(id: number) {
    if (!currentTask.value) return;

    if (!updateForm.deleted_attachments.includes(id)) {
        updateForm.deleted_attachments.push(id);
    }

    currentTask.value.attachments = currentTask.value.attachments.filter(
        (a) => a.id !== id,
    );
}

function formatSize(size: number) {
    if (size < 1024) return size + ' B';
    else if (size < 1048576) return (size / 1024).toFixed(1) + ' KB';
    else return (size / 1048576).toFixed(1) + ' MB';
}
</script>

<template>
    <Head title="All Tasks" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gray-100 p-8">
            <div class="mb-8 flex justify-between">
                <h1 class="text-3xl font-bold text-green-900">All Tasks</h1>
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
                <!-- Assign To -->
                <div>
                    <label
                        for="user"
                        class="mb-1 block text-sm font-medium text-gray-900"
                    >
                        Assign To
                    </label>
                    <select
                        id="user"
                        v-model="addForm.user_id"
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
                    >
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
                        class="mb-1 block text-sm font-medium text-gray-900"
                        >Title</label
                    >
                    <input
                        id="title"
                        type="text"
                        v-model="addForm.title"
                        placeholder="Enter title"
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
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
                        class="mb-1 block text-sm font-medium text-gray-900"
                        >Description</label
                    >
                    <textarea
                        id="description"
                        v-model="addForm.description"
                        placeholder="Enter description"
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
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
                        class="mb-1 block text-sm font-medium text-gray-900"
                    >
                        Attach Files
                    </label>
                    <input
                        id="attachments"
                        type="file"
                        @change="handleFiles"
                        multiple
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
                    />
                    <p
                        v-if="addForm.errors.attachments"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ addForm.errors.attachments }}
                    </p>

                    <!-- Preview selected files -->
                    <div
                        v-if="newAddAttachments.length"
                        class="mt-2 grid grid-cols-3 gap-2"
                    >
                        <div
                            v-for="(att, i) in newAddAttachments"
                            :key="i"
                            class="relative rounded border border-gray-300 p-1"
                        >
                            <img
                                v-if="att.isImage"
                                :src="att.url"
                                class="h-24 w-full rounded object-cover"
                            />
                            <span
                                v-else
                                class="block h-24 w-full overflow-hidden p-1 text-xs wrap-break-word"
                            >
                                {{ att.file.name }}
                            </span>
                            <button
                                type="button"
                                @click="removeAddAttachment(i)"
                                class="absolute top-1 right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs text-white"
                            >
                                ×
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Deadline -->
                <div>
                    <label
                        for="deadline"
                        class="mb-1 block text-sm font-medium text-gray-900"
                        >Deadline</label
                    >
                    <input
                        id="deadline"
                        type="datetime-local"
                        v-model="addForm.deadline"
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
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
                        class="mb-1 block text-sm font-medium text-gray-900"
                        >Priority</label
                    >
                    <select
                        id="priority"
                        v-model="addForm.priority"
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
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
                        class="cursor-pointer rounded-md border-gray-500 bg-green-600 px-4 py-2 font-medium text-gray-50 hover:scale-102"
                    >
                        Add Task
                    </button>
                    <button
                        @click="addTaskModal = false"
                        class="cursor-pointer rounded-md border border-gray-300 bg-gray-50 px-4 py-2 hover:bg-gray-200"
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
                        class="mb-1 block text-sm font-medium text-gray-900"
                    >
                        Assign To
                    </label>
                    <select
                        id="user"
                        v-model="updateForm.user_id"
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
                    >
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
                        class="mb-1 block text-sm font-medium text-gray-900"
                        >Title</label
                    >
                    <input
                        id="title"
                        type="text"
                        v-model="updateForm.title"
                        placeholder="Enter title"
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
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
                        class="mb-1 block text-sm font-medium text-gray-900"
                        >Description</label
                    >
                    <textarea
                        id="description"
                        v-model="updateForm.description"
                        placeholder="Enter description"
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
                        rows="4"
                    ></textarea>
                    <p
                        v-if="updateForm.errors.description"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ updateForm.errors.description }}
                    </p>
                </div>

                <!-- Attachments -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-900"
                        >Attachments</label
                    >
                    <input
                        type="file"
                        multiple
                        accept=".jpg,.jpeg,.png,.pdf,.docx"
                        @change="handleUpdateFiles"
                        class="w-full rounded-md border border-gray-500 p-2"
                    />
                    <h3
                        v-if="currentTask?.attachments?.length"
                        class="mt-3 text-sm font-medium"
                    >
                        Existing Attachments
                    </h3>
                    <div
                        v-if="currentTask?.attachments?.length"
                        class="mt-2 grid grid-cols-3 gap-2"
                    >
                        <div
                            v-for="att in currentTask.attachments"
                            :key="att.id"
                            class="relative rounded border border-gray-300 p-1"
                        >
                            <img
                                v-if="att.mimetype.startsWith('image/')"
                                :src="att.filepath"
                                class="h-24 w-full rounded object-cover"
                            />
                            <span
                                v-else
                                class="block w-full truncate text-sm"
                                >{{ att.filename }}</span
                            >
                            <button
                                class="absolute top-1 right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs text-white"
                                type="button"
                                @click="deleteExistingAttachment(att.id)"
                            >
                                x
                            </button>
                        </div>
                    </div>
                    <h3
                        v-if="updateAttachments.length"
                        class="mt-3 text-sm font-medium"
                    >
                        New Attachments
                    </h3>
                    <div
                        v-if="updateAttachments.length"
                        class="mt-2 grid grid-cols-3 gap-2"
                    >
                        <div
                            v-for="(att, i) in updateAttachments"
                            :key="i"
                            class="relative rounded border border-gray-300 p-1"
                        >
                            <img
                                v-if="att.isImage"
                                :src="att.url"
                                class="h-24 w-full rounded object-cover"
                            />
                            <span
                                v-else
                                class="block w-full truncate text-sm"
                                >{{ att.file.name }}</span
                            >
                            <button
                                type="button"
                                class="absolute top-1 right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs text-white"
                                @click="removeNewAttachment(i)"
                            >
                                x
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Deadline -->
                <div>
                    <label
                        for="deadline"
                        class="mb-1 block text-sm font-medium text-gray-900"
                        >Deadline</label
                    >
                    <input
                        id="deadline"
                        type="datetime-local"
                        v-model="updateForm.deadline"
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
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
                        class="mb-1 block text-sm font-medium text-gray-900"
                        >Priority</label
                    >
                    <select
                        id="priority"
                        v-model="updateForm.priority"
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
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
                        class="mb-1 block text-sm font-medium text-gray-900"
                        >Status</label
                    >
                    <select
                        id="status"
                        v-model="updateForm.status"
                        class="w-full rounded-md border border-gray-500 p-2 hover:border-gray-700 focus:border-gray-700 focus:ring-gray-800"
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
                        class="cursor-pointer rounded-md border-gray-500 bg-green-600 px-4 py-2 font-medium text-gray-50 hover:scale-102"
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
                        class="cursor-pointer rounded-md border border-gray-300 bg-gray-50 px-4 py-2 hover:bg-gray-200"
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
                    class="whitespace-pre-wrap text-gray-900"
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
                <ul class="mt-1 list-inside list-none text-sm text-blue-900">
                    <li v-for="att in currentTask.attachments" :key="att.id">
                        <img
                            v-if="att.mimetype === 'image/jpeg'"
                            :src="att.filepath"
                            :alt="att.filename"
                            class="h-auto w-full"
                        />
                        <iframe
                            v-else-if="att.mimetype === 'application/pdf'"
                            :src="att.filepath"
                            frameborder="0"
                            class="w-full"
                        ></iframe>
                        <a v-else :href="att.filepath"> {{ att.filename }}</a>
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
                    class="cursor-pointer rounded-md border border-gray-300 bg-gray-50 px-4 py-2 hover:bg-gray-200"
                >
                    Close
                </button>
            </div>
        </Modal>
    </AppLayout>
</template>
