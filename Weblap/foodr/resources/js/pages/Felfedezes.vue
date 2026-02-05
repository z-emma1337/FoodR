<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import RecipeGallery from '@/components/UI/RecipeGallery.vue'
</script>

<template>
  <AppLayout>
    <div class="w-full py-4 sm:py-6">
      
      <!-- Page Header -->
      <div class="mb-6 sm:mb-8">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-accent-200 mb-2">
          Receptek Felfedezése
        </h1>
        <p class="text-sm sm:text-base text-accent-300">
          Böngészd át az összes elérhető receptet és találd meg a kedvenceidet
        </p>
      </div>

      <!-- Recipe Gallery -->
      <RecipeGallery />
      
    </div>
  </AppLayout>
</template>