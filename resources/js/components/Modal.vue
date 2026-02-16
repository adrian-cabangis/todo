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
            class="fixed inset-0 z-50 flex items-center justify-center border border-green-200"
        >
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                @click="close"
            ></div>

            <!-- Modal Content -->
            <div
                class="relative z-50 w-full max-w-xl rounded-2xl bg-gray-100 p-7 shadow-2xl"
            >
                <!-- Header -->
                <div
                    class="mb-4 flex items-center justify-between border-b border-gray-400 pb-4"
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

                <!-- Body -->
                <div class="space-y-4 text-gray-900">
                    <slot>
                        <!-- Default content goes here -->
                    </slot>
                </div>
            </div>
        </div>
    </Teleport>
</template>
