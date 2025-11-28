<template> <!--Botón reutilizable en la lista de horas de TimeTracking.vue evitando código repetido-->
    <div class="flex justify-between items-center mt-4">
      <button @click="$emit('prev')" :disabled="currentPage === 1" class="bg-gray-700 text-white px-4 py-2 rounded disabled:opacity-50">
        Previous
      </button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="$emit('next')" :disabled="currentPage === totalPages" class="bg-gray-700 text-white px-4 py-2 rounded disabled:opacity-50">
        Next
      </button>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      currentPage: {
        type: Number,
        required: true
      },
      totalPages: {
        type: Number,
        required: true
      }
    }
  }
  </script>
  