import { ref, computed, watch } from 'vue'
import axios from 'axios'

export function useEmailCheck(email) {
  const isEmailAvailable = ref(null)
  const checkingEmail = ref(false)
  const emailTouched = ref(false)

  const hasAtSymbol = computed(() => email.value.includes('@'))

  const isEmailFormatValid = computed(() =>
    /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email.value)
  )

  const isEmailValid = computed(() =>
    isEmailFormatValid.value && isEmailAvailable.value === true
  )

  let debounceTimer = null
  let currentController = null

  watch(email, (val) => {
    if (val.length > 0) emailTouched.value = true

    if (currentController) {
      currentController.abort()
      currentController = null
    }
    clearTimeout(debounceTimer)

    // only start checking once "@" has been typed
    if (!hasAtSymbol.value || !isEmailFormatValid.value) {
      isEmailAvailable.value = null
      checkingEmail.value = false
      return
    }

    checkingEmail.value = true

    debounceTimer = setTimeout(async () => {
      currentController = new AbortController()
      try {
        const response = await axios.get('/check-email', {
          params: { email: val },
          signal: currentController.signal,
        })
        isEmailAvailable.value = response.data.available
      } catch (e) {
        if (!axios.isCancel(e)) {
          isEmailAvailable.value = false
        }
      } finally {
        checkingEmail.value = false
        currentController = null
      }
    }, 400)
  })

  const clearEmail = () => {
    clearTimeout(debounceTimer)
    if (currentController) {
      currentController.abort()
      currentController = null
    }
    email.value = ''
    isEmailAvailable.value = null
    checkingEmail.value = false
    emailTouched.value = false
  }

  return {
    isEmailAvailable,
    checkingEmail,
    emailTouched,
    hasAtSymbol,
    isEmailFormatValid,
    isEmailValid,
    clearEmail,
  }
}
