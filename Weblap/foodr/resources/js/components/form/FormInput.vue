<script setup>
import { computed } from 'vue'
import { X, Eye, EyeOff } from 'lucide-vue-next'

const props = defineProps({
  modelValue: String,
  type: { type: String, default: 'text' },
  placeholder: String,
  icon: Object,
  error: String,
  showClear: Boolean,
  showToggle: Boolean,         // ← új
  toggleState: Boolean         // ← új
})

const emit = defineEmits(['update:modelValue', 'clear', 'toggle'])
</script>

<template>
  <div class="relative group transition-colors duration-200">
    <div class="relative">
      <!-- Left icon -->
      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-brand-500 z-10 pointer-events-none">
        <component :is="icon" class="h-5 w-5" />
      </span>

      <!-- Input -->
      <input :value="modelValue" @input="emit('update:modelValue', $event.target.value)" :type="type"
        :placeholder="placeholder" class="w-full rounded-xl border border-accent-400/60
           bg-accent-200 px-3 py-2 pl-10 pr-10
           text-slate-900 placeholder-slate-600 outline-none
           transition-all duration-150 focus:border-accent-600
           focus:ring-4 focus:ring-accent-600/30 focus:scale-[1.01]" :class="{ 'border-red-500': error }" />
    </div>



    <!-- Clear button -->
    <button v-if="showClear && modelValue" type="button" @click="emit('clear')" class="absolute right-3 top-1/2 -translate-y-1/2 
             text-slate-600 hover:text-brand-600 
             transition-transform duration-300 
             cursor-pointer hover:scale-110 hover:rotate-90">
      <X class="h-5 w-5" />
    </button>

    <!-- Toggle password visibility -->
    <button v-if="showToggle" type="button" @click="emit('toggle')" class="absolute right-3 top-1/2 -translate-y-1/2
         text-slate-600 hover:text-brand-600 transition-colors duration-200">
      <transition name="fade-scale" mode="out-in">
        <component :is="toggleState ? Eye : EyeOff" class="h-5 w-5" :key="toggleState ? 'eye' : 'eyeoff'" />
      </transition>
    </button>


    <!-- Error -->
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
  </div>
</template>
