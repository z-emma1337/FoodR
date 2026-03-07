<script setup>
import { Check } from 'lucide-vue-next'

defineProps({
  valid: Boolean,
  text: String,
  loading: Boolean
})
</script>

<template>
  <p class="flex items-center gap-2 text-sm transition-colors duration-200"
   :class="valid ? 'text-emerald-700' : 'text-slate-800'">

  <span v-if="loading"
        class="inline-block h-4 w-4 border-2 border-slate-600 
               border-t-transparent rounded-full animate-spin"></span>

  <Check v-else-if="valid" class="h-4 w-4" />

  <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-slate-800"></span>

  {{ text }}
</p>

</template>