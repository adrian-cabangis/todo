<script setup>
defineProps ({
    modelValue: Boolean
})

const emit = defineEmits(['update:modelValue'])

function close(){
    emit('update:modelValue', false)
}
</script>

<template>
  <Teleport to="body">
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/50" @click="close"></div>

      <div class="relative z-50 bg-white w-96 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold">
            <slot name="title">
              Default Title
            </slot>
          </h2>

          <button @click="close" class="text-gray-500 hover:text-black text-lg cursor-pointer   ">
            X
          </button>
        </div>

        <div class="text-gray-700">
          <slot>
            Default modal content
          </slot>
        </div>

        <div class="mt-6 text-right">
          <slot name="footer">
            <button @click="close" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              Close
            </button>
          </slot>
        </div>
      </div>
    </div>
  </Teleport>
</template>