// resources/js/composables/usePasswordValidation.js
import { computed } from 'vue'

export function usePasswordValidation(password) {
  const isPasswordLongEnough = computed(() => password.value.length >= 8)
  const hasNumber = computed(() => /\d/.test(password.value))
  const hasUppercase = computed(() => /[A-Z]/.test(password.value))
  const hasSymbol = computed(() => /[^a-zA-Z0-9]/.test(password.value))
  
  const isPasswordValid = computed(() => 
    isPasswordLongEnough.value &&
    hasUppercase.value &&
    hasNumber.value &&
    hasSymbol.value
  )
  
  return {
    isPasswordLongEnough,
    hasNumber,
    hasUppercase,
    hasSymbol,
    isPasswordValid
  }
}
