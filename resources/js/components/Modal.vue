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
            class="fixed inset-0 z-50 flex items-center justify-center"
        >
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                @click="close"
            ></div>

            <!-- Modal Content -->
            <div
                class="relative z-50 w-full max-w-md rounded-xl bg-white p-6 shadow-2xl"
            >
                <!-- Header -->
                <div
                    class="mb-6 flex items-center justify-between border-b pb-2"
                >
                    <h2 class="text-xl font-semibold text-gray-800">
                        <slot name="title">Modal Title</slot>
                    </h2>
                    <button
                        @click="close"
                        class="text-lg font-bold text-gray-400 hover:text-gray-800"
                    >
                        &times;
                    </button>
                </div>

                <!-- Body -->
                <div class="space-y-4 text-gray-700">
                    <slot>
                        <!-- Default content goes here -->
                    </slot>
                </div>
            </div>
        </div>
    </Teleport>
</template>
