<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
  title: String
})
</script>

<template>
  <div>

    <Head :title="title" />

    <div class="relative flex min-h-screen items-center justify-center p-4 overflow-hidden
             bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800 animate-gradient">

      <div class="pointer-events-none absolute inset-0
               bg-gradient-to-br from-accent-500/20 via-transparent to-accent-600/20
               blur-3xl animate-gradient-slow"></div>

      <div class="relative w-full max-w-md">
        <div class="rounded-3xl overflow-hidden shadow-2xl transition-shadow duration-300">
          <div class="bg-gradient-to-br from-accent-500/80 to-accent-600/80 p-1">
            <div
              class="rounded-3xl p-8 animate-card-gradient bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 duration-300">
              <slot />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>