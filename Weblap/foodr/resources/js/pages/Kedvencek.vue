<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import KedvencGallery from '@/components/UI/KedvencGallery.vue'
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'

</script>

<template>
      <Head title="FavoR" />
  <AppLayout>
    <div class="w-full h-full pt-4 pb-10 sm:py-6">
      <KedvencGallery />
      
    </div>
  </AppLayout>
</template>