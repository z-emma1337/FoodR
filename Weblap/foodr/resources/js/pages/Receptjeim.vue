<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import ReceptjeimGallery from '@/components/UI/ReceptjeimGallery.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
        <Head title="CreatR" />
  <AppLayout>
    <div class="w-full py-4 sm:py-6 scrollable-none">
      <ReceptjeimGallery />
      
    </div>
  </AppLayout>
</template>