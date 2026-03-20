import { ref, computed, watch } from 'vue'
import axios from 'axios'

export function useUsernameCheck(username) {
  const isUsernameAvailable = ref(null)
  const checkingUsername = ref(false)
  const usernameTouched = ref(false)

  const isUsernameLongEnough = computed(() => username.value.length >= 4)

  const hasOnlyValidChars = computed(() =>
    /^[a-zA-Z0-9._-]+$/.test(username.value)
  )

  const isUsernameFormatValid = computed(() =>
    isUsernameLongEnough.value && hasOnlyValidChars.value
  )

  const isUsernameValid = computed(() =>
    isUsernameFormatValid.value && isUsernameAvailable.value === true
  )

  let debounceTimer = null
  let currentController = null

  watch(username, (val) => {
    usernameTouched.value = true

    if (currentController) {
      currentController.abort()
      currentController = null
    }

    clearTimeout(debounceTimer)

    if (!isUsernameFormatValid.value) {
      isUsernameAvailable.value = null
      checkingUsername.value = false
      return
    }

    checkingUsername.value = true

    debounceTimer = setTimeout(async () => {
      currentController = new AbortController()

      try {
        const response = await axios.get('/check-username', {
          params: { username: val },
          signal: currentController.signal,
        })
        isUsernameAvailable.value = response.data.available
      } catch (e) {
        if (!axios.isCancel(e)) {
          isUsernameAvailable.value = false
        }
      } finally {
        checkingUsername.value = false
        currentController = null
      }
    }, 400)
  })

  const clearUsername = () => {
    clearTimeout(debounceTimer)
    if (currentController) {
      currentController.abort()
      currentController = null
    }
    username.value = ''
    isUsernameAvailable.value = null
    checkingUsername.value = false
    usernameTouched.value = false
  }

  return {
    isUsernameAvailable,
    checkingUsername,
    usernameTouched,
    isUsernameLongEnough,
    hasOnlyValidChars,
    isUsernameFormatValid,
    isUsernameValid,
    clearUsername,
  }
}