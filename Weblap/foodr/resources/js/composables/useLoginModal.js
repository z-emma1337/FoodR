import { ref } from 'vue'

const loginModalOpen = ref(false)
const onSuccessCallback = ref(null)

export const useLoginModal = () => {
    const openLoginModal = (onSuccess = null) => {
        onSuccessCallback.value = onSuccess
        loginModalOpen.value = true
    }

    const closeLoginModal = () => {
        loginModalOpen.value = false
    }

    const resolveLoginSuccess = () => {
        closeLoginModal()
        if (onSuccessCallback.value) {
            onSuccessCallback.value()
            onSuccessCallback.value = null
        }
    }

    return {
        loginModalOpen,
        openLoginModal,
        closeLoginModal,
        resolveLoginSuccess,
    }
}