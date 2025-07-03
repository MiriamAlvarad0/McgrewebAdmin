<template>
    <!-- Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="roleModalLabel" v-if="!data.id">Add Role</h1>
            <h1 class="modal-title fs-5" id="roleModalLabel" v-else>Edit Role ({{ data.name }})</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm" id="registerForm" novalidate style="margin-bottom: 200px;">
              <div v-if="showErrors" class="alert alert-danger" role="alert">
                {{ errors }}
              </div>
              <div class="mb-3">
                <label for="Name" class="form-label">Name</label>
                <input 
                  type="text" 
                  class="form-control"
                  v-model="data.name"
                  id="Name"
                  placeholder="Ingresa el role"
                  @blur="v$.name.$touch()"
                  ref="inputFocus" 
                >
                <p v-for="error in v$.name.$errors" :key="error.$uid" class="text-danger m-0"> 
                {{ error.$message }}
                </p>
              </div>
              
              <div class="mb-5">
                <label for="Permissions" class="form-label">Permissions</label>
                <Multiselect
                  v-model="data.permissions"
                  mode="tags"
                  :close-on-select="false"
                  :searchable="true"
                  :create-option="false"
                  :options="options"
                />
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" :disabled="v$.$invalid" v-if="!data.id" @click="add">Add Role</button>
            <button type="button" class="btn btn-primary" :disabled="v$.$invalid" v-else @click="edit">Save Changes</button>
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
    import Multiselect from '@vueform/multiselect'
  
    const props = defineProps(['data'])
    const data = props.data
  
    const showErrors = ref(false)
    const errors = ref('')
    const inputFocus = ref (null)
    const options = ref([])
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
        },
      }
    }) 
    const v$ = useVuelidate(rules, data)
  
    const emit = defineEmits(['result']) 
    // Función para agregar Role
    const add = async () => {
      await v$.value.$validate()
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      axios.post('/api/v1/roles', data).then(response => {
        //Reset datos del modal y cerrar modal
        resetForm()
        const modalElement = document.getElementById('roleModal')
        const modalInstance = Modal.getInstance(modalElement) || new Modal(modalElement);
        modalInstance.hide();
        emit('result', "true")
        Swal.fire("Role added!", "", "success")
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
      axios.put('/api/v1/roles/'+data.id, data).then(response => {
        //Reset datos del modal y cerrar modal
        resetForm()
        const modalElement = document.getElementById('roleModal')
        const modalInstance = Modal.getInstance(modalElement) || new Modal(modalElement);
        modalInstance.hide();
        emit('result', "true")
        Swal.fire("Role modified!", "", "success")
      }).catch(function(err){ 
        showErrors.value = true
        errors.value = err?.response?.data?.message ?? err
        setTimeout(() => showErrors.value = false, 5000)
      });
    }
  
    const resetForm = () => {
      data.id = ''
      data.name = ''
      data.permissions = []
      v$.value.$reset() // Restablece el estado de validación
      showErrors.value = false
      errors.value = ''
    }
  
    const getPermissions = () => {
      axios.get('/api/v1/permissions').then(res => { 
        options.value = res.data.roles.data.map(role => ({
            value: role.id,
            label: role.name
        }));
      }).catch(err => {
        console.log(err)
      })
    }
  
    onMounted(() => {
      getPermissions()
      const modalElement = document.getElementById('roleModal')
      modalElement.addEventListener('hidden.bs.modal', resetForm)
  
      if (inputFocus.value) {
        inputFocus.value.focus()
      }
    })
  </script>
  <style scoped>
  
  </style>
  <style src="@vueform/multiselect/themes/default.css"></style>