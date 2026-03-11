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
  

  watch(username, async (val) => {
    usernameTouched.value = true
    

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