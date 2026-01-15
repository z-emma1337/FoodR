// resources/js/composables/useUsernameCheck.js
import { ref, computed, watch } from 'vue'
import axios from 'axios'

export function useUsernameCheck(username) {
  const isUsernameAvailable = ref(null)
  const checkingUsername = ref(false)
  const usernameTouched = ref(false)
  
  // Validációs szabályok
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
  
  // Username ellenőrzés
  watch(username, async (val) => {
    usernameTouched.value = true
    
    // Ha formailag nem jó, ne kérdezzen backendet
    if (!isUsernameFormatValid.value) {
      isUsernameAvailable.value = null
      return
    }
    
    checkingUsername.value = true
    
    try {
      const response = await axios.get('/check-username', {
        params: { username: val }
      })
      isUsernameAvailable.value = response.data.available
    } catch (e) {
      isUsernameAvailable.value = false
    } finally {
      checkingUsername.value = false
    }
  })
  
  // Clear function
  const clearUsername = () => {
    username.value = ''
    isUsernameAvailable.value = null
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
    clearUsername
  }
}