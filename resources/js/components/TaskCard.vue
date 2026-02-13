<script setup lang="ts">
import { Task } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    task: Task;
}>();

const priorityMap: Record<string, number> = {
    high: 1,
    medium: 2,
    low: 3,
};

const isOverdue = computed(() => {
    if (!props.task.deadline) return false;
    const deadline = new Date(props.task.deadline).getTime();
    const now = new Date().getTime();
    return (
        now > deadline &&
        props.task.status !== 'completed' &&
        props.task.status !== 'cancelled'
    );
});
</script>

<template>
    <div
        :class="[
            'flex h-full transform flex-col justify-between rounded-xl border-l-8 p-5 shadow-lg transition hover:-translate-y-1 hover:shadow-2xl',
            task.status === 'pending' ? 'border-green-600' : '',
            task.status === 'ongoing' ? 'border-green-700' : '',
            task.status === 'completed' ? 'border-green-800' : '',
            task.status === 'cancelled' ? 'border-gray-900' : '',
            isOverdue ? 'border-red-200 bg-red-50/50' : 'bg-white',
        ]"
    >
        <div class="mb-3 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-500">
                    {{ task.user?.email }}
                </h2>
                <h2 class="text-lg font-bold text-gray-800">
                    {{ task.title }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="rounded-full px-3 py-1 text-xs font-semibold capitalize"
                    :class="{
                        'bg-green-100 text-green-700':
                            task.status === 'pending',
                        'bg-green-100 text-green-800':
                            task.status === 'ongoing',
                        'bg-green-100 text-green-900':
                            task.status === 'completed',
                        'bg-green-100 text-gray-900':
                            task.status === 'cancelled',
                    }"
                >
                    {{ task.status }}
                </span>
                <span
                    v-if="isOverdue"
                    class="rounded-full bg-red-200 px-2 py-1 text-xs font-semibold text-red-800"
                >
                    Overdue
                </span>
            </div>
        </div>

        <div class="mb-4 text-sm text-gray-600">
            {{ task.description }}
        </div>

        <div
            class="mt-auto flex items-center justify-between text-sm text-gray-500"
        >
            <span>
                📅
                {{
                    task.deadline
                        ? new Date(task.deadline).toLocaleString('en-US', {
                              month: 'short',
                              day: 'numeric',
                              year: 'numeric',
                              hour: '2-digit',
                              minute: '2-digit',
                              hour12: true,
                          })
                        : 'No deadline'
                }}
            </span>

            <span class="flex items-center gap-2">
                <span
                    class="flex h-6 w-6 items-center justify-center rounded-full font-bold text-white"
                    :class="{
                        'bg-green-900': task.priority === 'high',
                        'bg-green-600': task.priority === 'medium',
                        'bg-green-400': task.priority === 'low',
                    }"
                >
                    {{ priorityMap[task.priority] }}
                </span>
                <span class="capitalize">{{ task.priority }}</span>
            </span>

            <slot name="actions"></slot>
        </div>
    </div>
</template>
