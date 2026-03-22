<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import RecipeGallery from '@/components/UI/RecipeGallery.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
      <Head title="FeedR" />
  <AppLayout>
    <div class="w-full py-4 sm:py-6">
      <RecipeGallery />
      
    </div>
  </AppLayout>
</template>