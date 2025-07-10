<template>
  <div>
    <div class="modal fade" id="maquinariaModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
          <!-- Header -->
          <div class="modal-header">
            <h1 class="modal-title fs-5">
              <span v-if="!formData.id">Add Maquinaria</span>
              <span v-else>Edit Maquinaria ({{ formData.nombre }})</span>
            </h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <!-- Body -->
          <div class="modal-body">
            <form @submit.prevent id="maquinariaForm" novalidate>
              <div v-if="showErrors" class="alert alert-danger">{{ errors }}</div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre</label>
                  <input type="text" class="form-control" v-model="formData.nombre" @blur="v$.nombre.$touch()"
                    ref="inputFocus" />
                  <p v-for="e in v$.nombre.$errors" :key="e.$uid" class="text-danger m-0">{{ e.$message }}</p>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Categoría</label>
                  <select class="form-select" v-model="formData.id_categoria" @blur="v$.id_categoria.$touch()">
                    <option value="" disabled>Selecciona categoría</option>
                    <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
                  </select>
                  <p v-for="e in v$.id_categoria.$errors" :key="e.$uid" class="text-danger m-0">{{ e.$message }}</p>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Subcategoría</label>
                  <select class="form-select" v-model="formData.id_subcategoria" @blur="v$.id_subcategoria.$touch()">
                    <option value="" disabled>Selecciona subcategoría</option>
                    <option v-for="sub in subcategorias" :key="sub.id" :value="sub.id">{{ sub.nombre }}</option>
                  </select>
                  <p v-for="e in v$.id_subcategoria.$errors" :key="e.$uid" class="text-danger m-0">{{ e.$message }}</p>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Marca</label>
                  <input type="text" class="form-control" v-model="formData.marca" @blur="v$.marca.$touch()" />
                  <p v-for="e in v$.marca.$errors" :key="e.$uid" class="text-danger m-0">{{ e.$message }}</p>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Modelo</label>
                  <input type="text" class="form-control" v-model="formData.modelo" @blur="v$.modelo.$touch()" />
                  <p v-for="e in v$.modelo.$errors" :key="e.$uid" class="text-danger m-0">{{ e.$message }}</p>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Año</label>
                  <input type="number" class="form-control" v-model="formData.ano" @blur="v$.ano.$touch()" />
                  <p v-for="e in v$.ano.$errors" :key="e.$uid" class="text-danger m-0">{{ e.$message }}</p>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Precio</label>
                  <input type="number" step="0.01" class="form-control" v-model="formData.precio"
                    @blur="v$.precio.$touch()" />
                  <p v-for="e in v$.precio.$errors" :key="e.$uid" class="text-danger m-0">{{ e.$message }}</p>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Descripción</label>
                  <input type="text" class="form-control" v-model="formData.descripcion"
                    @blur="v$.descripcion.$touch()" />
                  <p v-for="e in v$.descripcion.$errors" :key="e.$uid" class="text-danger m-0">{{ e.$message }}</p>
                </div>
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" v-model="formData.disponible" id="Disponible" />
                <label class="form-check-label" for="Disponible">Disponible</label>
              </div>

              <div class="mb-3">
  <label class="form-label">Imagen</label>
  <input type="file" name="imagen" @change="onFileChange" accept="image/*" />

</div>

<!-- Vista previa de la imagen actual -->
<div v-if="formData.imagen && !selectedFile" class="mt-3">
  <label class="form-label">Imagen actual:</label><br />
  <img
    :src="getImagenUrl(formData.imagen)"
    alt="Imagen actual"
    class="img-thumbnail"
    style="max-height: 200px;"
  />
</div>


            </form>
          </div>

          <!-- Footer -->
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button class="btn btn-primary" :disabled="v$.$invalid" v-if="!formData.id" @click="addMaquinaria">
              Add Maquinaria
            </button>
            <button class="btn btn-primary" :disabled="v$.$invalid" v-else @click="editMaquinaria">
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, defineProps } from 'vue'
import useVuelidate from '@vuelidate/core'
import { required, minLength, helpers, numeric } from '@vuelidate/validators'
import axios from 'axios'
import { Modal } from 'bootstrap'
import Swal from 'sweetalert2/dist/sweetalert2'

const props = defineProps(['data'])
const showErrors = ref(false)
const errors = ref('')
const inputFocus = ref(null)
const token = localStorage.getItem('auth_token')
const selectedFile = ref(null)

axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
axios.defaults.headers.common['Accept'] = 'application/json'


// Devuelve la URL completa de la imagen desde el backend
const getImagenUrl = (filename) => {
  return `http://localhost:8000/storage/maquinaria/${filename}`
}



function onFileChange(event) {
  selectedFile.value = event.target.files[0] || null
} 
// Formulario reactivo local
const formData = ref({
  id: '',
  nombre: '',
  id_categoria: '',
  id_subcategoria: '',
  marca: '',
  modelo: '',
  ano: '',
  precio: '',
  descripcion: '',
  disponible: false,
  imagen: ''
})

// Actualizar formData cuando cambia el prop data
watch(() => props.data, (newData) => {
  if (newData) {
    formData.value = { ...newData }
    selectedFile.value = null // 🔥 importante para que no sobrescribas sin querer
  } else {
    resetForm()
  }
}, { immediate: true })


// Validaciones
const requiredMsg = 'El campo es obligatorio'
const rules = computed(() => ({
  nombre: { required: helpers.withMessage(requiredMsg, required), minLength: minLength(3) },
  id_categoria: { required: helpers.withMessage(requiredMsg, required), numeric },
  id_subcategoria: { required: helpers.withMessage(requiredMsg, required), numeric },
  marca: { required: helpers.withMessage(requiredMsg, required) },
  modelo: { required: helpers.withMessage(requiredMsg, required) },
  ano: { required: helpers.withMessage(requiredMsg, required), numeric },
  precio: { required: helpers.withMessage(requiredMsg, required), numeric },
  descripcion: { required: helpers.withMessage(requiredMsg, required), minLength: minLength(3) },
}))
const v$ = useVuelidate(rules, formData)

// Emits
const emit = defineEmits(['result'])

// CRUD
const addMaquinaria = async () => {
  await v$.value.$validate()
  if (v$.value.$invalid) return

  const form = new FormData()

  for (const key in formData.value) {
  if (key === 'imagen') continue

  let val = formData.value[key]

  if (key === 'disponible') {
    form.append(key, val ? '1' : '0') // ✅ obligatorio para Laravel
  } else {
    form.append(key, val)
  }
}

  // Añadir la imagen si hay
  if (selectedFile.value) {
    form.append('imagen', selectedFile.value)
  }

  console.log('FormData entries:')
  for (let pair of form.entries()) {
    console.log(pair[0]+ ': '+ pair[1])
  }

  try {
    const res = await axios.post('/api/v1/maquinaria', form, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    handleSuccess('Maquinaria agregada!')
  } catch (err) {
    handleError(err)
  }
}



const editMaquinaria = async () => {
  await v$.value.$validate()
  if (v$.value.$invalid) return

  const form = new FormData()
  for (const key in formData.value) {
    if (key === 'id') continue
    let val = formData.value[key]
    if (val !== null && val !== undefined && val !== '') {
      if (key === 'disponible') {
        form.append(key, val ? '1' : '0')
      } else {
        form.append(key, val)
      }
    }
  }
  if (selectedFile.value) {
    form.append('imagen', selectedFile.value)
  }
  form.append('_method', 'PUT') // Esto es clave para Laravel

  axios.post(`/api/v1/maquinaria/${formData.value.id}`, form, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
    .then(() => handleSuccess('Maquinaria modificada!'))
    .catch(handleError)
}



const handleSuccess = (msg) => {
  resetForm()
  Modal.getInstance(document.getElementById('maquinariaModal')).hide()
  emit('result', true)
  Swal.fire(msg, '', 'success')
}

const handleError = (err) => {
  showErrors.value = true
  errors.value = err?.response?.data?.message ?? err.message ?? err
  setTimeout(() => (showErrors.value = false), 5000)
}

// Reset form
const resetForm = () => {
  formData.value = {
    id: '',
    nombre: '',
    id_categoria: '',
    id_subcategoria: '',
    marca: '',
    modelo: '',
    ano: '',
    precio: '',
    descripcion: '',
    disponible: false,
    imagen:'',
  }
  v$.value.$reset()
  showErrors.value = false
  errors.value = ''
}

const categorias = ref([])
const subcategorias = ref([])

const loadCategorias = async () => {
  try {
    const res = await axios.get('/api/v1/categorias')
    categorias.value = res.data.data
  } catch (error) {
    console.error('Error al cargar categorías:', error.response || error)
  }
}

const loadSubcategorias = async () => {
  try {
    const res = await axios.get('/api/v1/subcategorias')
    subcategorias.value = res.data.data
  } catch (error) {
    console.error('Error al cargar subcategorías:', error.response || error)
  }
}

onMounted(() => {
  loadCategorias()
  loadSubcategorias()

  const el = document.getElementById('maquinariaModal')
  el.addEventListener('hidden.bs.modal', resetForm)
  if (inputFocus.value) inputFocus.value.focus()
})
</script>
