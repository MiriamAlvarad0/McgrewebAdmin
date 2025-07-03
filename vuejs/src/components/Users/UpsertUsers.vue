<template>
  <div>
    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="userModalLabel" v-if="!data.id">Add User</h1>
            <h1 class="modal-title fs-5" id="userModalLabel" v-else>Edit User ({{ data.name }})</h1>
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
                  placeholder="Ingresa nombre"
                  @blur="v$.name.$touch()"
                  ref="inputFocus" 
                  :disabled="data.id ? true : false"
                >
                <p v-for="error in v$.name.$errors" :key="error.$uid" class="text-danger m-0"> 
                  {{ error.$message }}
                </p>
              </div>

              <div class="mb-3">
                <label for="Email" class="form-label">Correo</label>
                <input 
                  type="email" 
                  class="form-control" 
                  v-model="data.email" 
                  id="Email"
                  placeholder="Ingresa correo electrónico"
                  @blur="v$.email.$touch()"
                >
                <p v-for="error in v$.email.$errors" :key="error.$uid" class="text-danger m-0"> 
                  {{ error.$message }}
                </p>
              </div>

              <div class="mb-3">
                <label for="Role" class="form-label">Rol</label>
                <select 
                  class="form-control" 
                  v-model="data.role" 
                  id="Role"
                  @blur="v$.role.$touch()"
                >
                  <option value="" disabled selected>Select a Role</option>
                  <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                </select>
                <p v-for="error in v$.role.$errors" :key="error.$uid" class="text-danger m-0"> 
                  {{ error.$message }} 
                </p>
              </div>

              <div class="mb-3 form-check">
                <input 
                  type="checkbox" 
                  class="form-check-input" 
                  v-model="data.active"
                  id="Status"
                >
                <label class="form-check-label" for="Status">Active</label>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" :disabled="v$.$invalid" v-if="!data.id" @click="addUser">Add User</button>
            <button type="button" class="btn btn-primary" :disabled="v$.$invalid" v-else @click="editUser">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script setup>
  import { ref, computed, onMounted, defineProps } from 'vue'
  import useVuelidate from '@vuelidate/core'
  import { required, minLength, email, helpers } from '@vuelidate/validators'
  import axios from 'axios'
  import { Modal } from 'bootstrap'
  import Swal from 'sweetalert2/dist/sweetalert2'

  const props = defineProps(['data'])
  const data = props.data

  const showErrors = ref(false)
  const errors = ref('')
  const inputFocus = ref (null)
  const roles = ref('')
  const token = ref(localStorage.getItem('auth_token'))

  // Se puede cambiar el idioma de los mensajes implementando i18n https://vuelidate-next.netlify.app/advanced_usage.html#i18n-support
  const requiredMessage = 'El campo es obligatorio'

  // Validaciones (este es un ejemplo básico, deberías implementar tu lógica de validación)
  const rules = computed(() => {
    return {
      name: {
        required: helpers.withMessage(requiredMessage, required), 
        minLength: minLength(4)
      },
      email: { 
        required: helpers.withMessage(requiredMessage, required), 
        email 
      },
      role: { 
        required: helpers.withMessage(requiredMessage, required)
      }
    }
  }) 
  const v$ = useVuelidate(rules, data)

  const emit = defineEmits(['result']) 
  // Función para agregar Usuario
  const addUser = async () => {
    await v$.value.$validate()
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    axios.post('/api/v1/users', data).then(response => {
    //Reset datos del modal y cerrar modal
      resetForm()
      const modalElement = document.getElementById('userModal')
      const modalInstance = Modal.getInstance(modalElement) || new Modal(modalElement);
      modalInstance.hide();
      emit('result', "true")
      Swal.fire("User added!", "", "success")
    }).catch(function(err){ 
      console.log(err?.response?.data?.errors ?? err)
      showErrors.value = true
      errors.value = err?.response?.data?.message ?? err
      setTimeout(() => showErrors.value = false, 5000)
    });
  }

  // Función para modificar Usuario
  const editUser = async () => {
    await v$.value.$validate()
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    axios.put('/api/v1/users/'+data.id, data).then(response => {
    //Reset datos del modal y cerrar modal
      resetForm()
      const modalElement = document.getElementById('userModal')
      const modalInstance = Modal.getInstance(modalElement) || new Modal(modalElement);
      modalInstance.hide();
      emit('result', "true")
      Swal.fire("User modified!", "", "success")
    }).catch(function(err){ 
      showErrors.value = true
      errors.value = err?.response?.data?.message ?? err
      setTimeout(() => showErrors.value = false, 5000)
    });
  }

  const resetForm = () => {
    data.id = ''
    data.name = ''
    data.email = ''
    data.role = ''
    data.active = false
    v$.value.$reset() // Restablece el estado de validación
    showErrors.value = false
    errors.value = ''
  }

  const getRoles = () => {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    axios.get('/api/v1/roles').then(response => {
      roles.value = response.data.roles.data
    }).catch(err => {
      console.log(err)
    })
  }

  onMounted(() => {
    const modalElement = document.getElementById('userModal')
    modalElement.addEventListener('hidden.bs.modal', resetForm)

    getRoles()

    if (inputFocus.value) {
      inputFocus.value.focus()
    }
  })
</script>