<script setup>
defineProps({
    modelValue: Boolean,
});
const emit = defineEmits(['update:modelValue']);

function close() {
    emit('update:modelValue', false);
}
</script>

<template>
    <Teleport to="body">
        <div
            v-if="modelValue"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4"
        >
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                @click="close"
            ></div>

            <!-- Modal Content -->
            <div
                class="relative z-50 flex max-h-[90vh] w-full max-w-xl flex-col rounded-2xl bg-gray-100 shadow-2xl"
            >
                <!-- Header -->
                <div
                    class="flex items-center justify-between border-b border-gray-400 p-6"
                >
                    <h2 class="text-xl font-semibold text-gray-900">
                        <slot name="title">Modal Title</slot>
                    </h2>
                    <button
                        @click="close"
                        class="cursor-pointer text-lg font-bold text-gray-600 hover:text-gray-800"
                    >
                        &times;
                    </button>
                </div>

                <!-- Scrollable Body -->
                <div class="flex-1 space-y-4 overflow-y-auto p-6 text-gray-900">
                    <slot />
                </div>
            </div>
        </div>
    </Teleport>
</template>
