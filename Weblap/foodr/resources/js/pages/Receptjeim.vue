<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import ReceptjeimGallery from '@/components/UI/ReceptjeimGallery.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
        <Head title="CreatR" />
  <AppLayout>
    <div class="w-full h-full pt-4 pb-10 sm:py-6">
      <ReceptjeimGallery />
      
    </div>
  </AppLayout>
</template>