<template>
    <div>
        <div class="modal fade" id="maquinariaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <!-- Header -->
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">
                            <span v-if="!data.id">Add Maquinaria</span>
                            <span v-else>Edit Maquinaria ({{ data.nombre }})</span>
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
                                    <input type="text" class="form-control" v-model="data.nombre"
                                        @blur="v$.nombre.$touch()" ref="inputFocus"
                                        :disabled="data.id ? true : false" />
                                    <p v-for="e in v$.nombre.$errors" :key="e.$uid" class="text-danger m-0">{{
                                        e.$message }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Categoría (ID)</label>
                                    <input type="number" class="form-control" v-model="data.id_categoria"
                                        @blur="v$.id_categoria.$touch()" />
                                    <p v-for="e in v$.id_categoria.$errors" :key="e.$uid" class="text-danger m-0">{{
                                        e.$message }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Subcategoría (ID)</label>
                                    <input type="number" class="form-control" v-model="data.id_subcategoria"
                                        @blur="v$.id_subcategoria.$touch()" />
                                    <p v-for="e in v$.id_subcategoria.$errors" :key="e.$uid" class="text-danger m-0">{{
                                        e.$message }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Marca</label>
                                    <input type="text" class="form-control" v-model="data.marca"
                                        @blur="v$.marca.$touch()" />
                                    <p v-for="e in v$.marca.$errors" :key="e.$uid" class="text-danger m-0">{{ e.$message
                                        }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Modelo</label>
                                    <input type="text" class="form-control" v-model="data.modelo"
                                        @blur="v$.modelo.$touch()" />
                                    <p v-for="e in v$.modelo.$errors" :key="e.$uid" class="text-danger m-0">{{
                                        e.$message }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Año</label>
                                    <input type="number" class="form-control" v-model="data.ano"
                                        @blur="v$.ano.$touch()" />
                                    <p v-for="e in v$.ano.$errors" :key="e.$uid" class="text-danger m-0">{{ e.$message
                                        }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Precio</label>
                                    <input type="number" step="0.01" class="form-control" v-model="data.precio"
                                        @blur="v$.precio.$touch()" />
                                    <p v-for="e in v$.precio.$errors" :key="e.$uid" class="text-danger m-0">{{
                                        e.$message }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Descripción</label>
                                    <input type="text" class="form-control" v-model="data.descripcion"
                                        @blur="v$.descripcion.$touch()" />
                                    <p v-for="e in v$.descripcion.$errors" :key="e.$uid" class="text-danger m-0">{{
                                        e.$message }}</p>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" v-model="data.disponible"
                                    id="Disponible" />
                                <label class="form-check-label" for="Disponible">Disponible</label>
                            </div>
                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary" :disabled="v$.$invalid" v-if="!data.id" @click="addMaquinaria">
                            Add Maquinaria
                        </button>
                        <button class="btn btn-primary" :disabled="v$.$invalid" v-else @click="editMaquinaria">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineProps } from 'vue'
import useVuelidate from '@vuelidate/core'
import { required, minLength, helpers, numeric } from '@vuelidate/validators'
import axios from 'axios'
import { Modal } from 'bootstrap'
import Swal from 'sweetalert2/dist/sweetalert2'

/* Props & Refs */
const props = defineProps(['data'])
const data = props.data

const showErrors = ref(false)
const errors = ref('')
const inputFocus = ref(null)
const token = ref(localStorage.getItem('auth_token'))

/* ---------- VALIDACIONES ---------- */
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
const v$ = useVuelidate(rules, data)

/* ---------- EMITS ---------- */
const emit = defineEmits(['result'])

/* ---------- CRUD ---------- */
const addMaquinaria = async () => {
    await v$.value.$validate()
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    axios.post('/api/v1/maquinaria', data)
        .then(() => handleSuccess('Maquinaria added!'))
        .catch(handleError)
}

const editMaquinaria = async () => {
    await v$.value.$validate()
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    axios.put(`/api/v1/maquinaria/${data.id}`, data)
        .then(() => handleSuccess('Maquinaria modified!'))
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
    errors.value = err?.response?.data?.message ?? err
    setTimeout(() => (showErrors.value = false), 5000)
}

/* ---------- HELPERS ---------- */
const resetForm = () => {
    Object.assign(data, {
        id: '', nombre: '', id_categoria: '', id_subcategoria: '', marca: '',
        modelo: '', ano: '', precio: '', descripcion: '', disponible: false,
    })
    v$.value.$reset()
    showErrors.value = false
    errors.value = ''
}

onMounted(() => {
    const el = document.getElementById('maquinariaModal')
    el.addEventListener('hidden.bs.modal', resetForm)
    if (inputFocus.value) inputFocus.value.focus()
})
</script>
