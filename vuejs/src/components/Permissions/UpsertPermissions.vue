<template>
  <div>
    <!-- Modal -->
      <div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="permissionModalLabel" v-if="!data.id">Add Permission</h1>
            <h1 class="modal-title fs-5" id="permissionModalLabel" v-else>Edit Permission ({{ data.name }})</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm" id="registerForm" novalidate>
              <div v-if="showErrors" class="alert alert-danger" role="alert">
                {{ errors }}
              </div>
              <div class="mb-3">
                <label for="Name" class="form-label">Nombre</label>
                <input 
                  type="text" 
                  class="form-control"
                  v-model="data.name"
                  id="Name"
                  placeholder="Ingresa el permiso"
                  @blur="v$.name.$touch()"
                  ref="inputFocus" 
                >
                <p v-for="error in v$.name.$errors" :key="error.$uid" class="text-danger m-0"> 
                {{ error.$message }}
                </p>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" :disabled="v$.$invalid" v-if="!data.id" @click="add">Add Permission</button>
            <button type="button" class="btn btn-primary" :disabled="v$.$invalid" v-else @click="edit">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, defineProps } from 'vue'
  import useVuelidate from '@vuelidate/core'
  import { required, minLength, helpers } from '@vuelidate/validators'
  import axios from 'axios'
  import { Modal } from 'bootstrap'
  import Swal from 'sweetalert2/dist/sweetalert2'
  import { spaces } from '../../utils/validations'

  const props = defineProps(['data'])
  const data = props.data

  const showErrors = ref(false)
  const errors = ref('')
  const inputFocus = ref (null)
  const token = ref(localStorage.getItem('auth_token'))

  // Se puede cambiar el idioma de los mensajes implementando i18n https://vuelidate-next.netlify.app/advanced_usage.html#i18n-support
  const requiredMessage = 'El campo es obligatorio'

  // Validaciones (este es un ejemplo básico, deberías implementar tu lógica de validación)
  const rules = computed(() => {
    return {
      name: {
        required: helpers.withMessage(requiredMessage, required), 
        minLength: minLength(4),
        spaces: helpers.withMessage('No se permiten espacios', spaces)
      }
    }
  }) 
  const v$ = useVuelidate(rules, data)

  const emit = defineEmits(['result']) 
  // Función para agregar Role
  const add = async () => {
    await v$.value.$validate()
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    axios.post('/api/v1/permissions', data).then(response => {
      //Reset datos del modal y cerrar modal
      resetForm()
      const modalElement = document.getElementById('permissionModal')
      const modalInstance = Modal.getInstance(modalElement) || new Modal(modalElement);
      modalInstance.hide();
      emit('result', "true")
      Swal.fire("Permission added!", "", "success")
    }).catch(function(err){ 
      console.log(err?.response?.data?.errors ?? err)
      showErrors.value = true
      errors.value = err?.response?.data?.message ?? err
      setTimeout(() => showErrors.value = false, 5000)
    });
  }

  // Función para modificar Role
  const edit = async () => {
    await v$.value.$validate()
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    axios.put('/api/v1/permissions/'+data.id, data).then(response => {
      //Reset datos del modal y cerrar modal
      resetForm()
      const modalElement = document.getElementById('permissionModal')
      const modalInstance = Modal.getInstance(modalElement) || new Modal(modalElement);
      modalInstance.hide();
      emit('result', "true")
      Swal.fire("Permission modified!", "", "success")
    }).catch(function(err){ 
      showErrors.value = true
      errors.value = err?.response?.data?.message ?? err
      setTimeout(() => showErrors.value = false, 5000)
    });
  }

  const resetForm = () => {
    data.id = ''
    data.name = ''
    v$.value.$reset() // Restablece el estado de validación
    showErrors.value = false
    errors.value = ''
  }

  onMounted(() => {
    const modalElement = document.getElementById('permissionModal')
    modalElement.addEventListener('hidden.bs.modal', resetForm)

    if (inputFocus.value) {
      inputFocus.value.focus()
    }
  })
</script>