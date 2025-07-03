<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 position-relative d-flex justify-content-center align-items-center">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Registro</h4>
                        <form @submit.prevent="submitForm" id="registerForm" novalidate>
                            <div v-if="showErrors" class="alert alert-danger" role="alert">
                                {{ errors }}
                            </div>
                            <div class="mb-3">
                                <label for="Name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" v-model="formData.name" id="Name"
                                    placeholder="Ingresa tu nombre" @blur="v$.name.$touch()" ref="inputFocus">
                                <p v-for="error in v$.name.$errors" :key="error.$uid" class="text-danger m-0">
                                    {{ error.$message }}
                                </p>
                            </div>

                            <div class="mb-3">
                                <label for="Email" class="form-label">Correo</label>
                                <input type="email" class="form-control" v-model="formData.email" id="Email"
                                    placeholder="Ingresa tu correo electrónico" @blur="v$.email.$touch()">
                                <p v-for="error in v$.email.$errors" :key="error.$uid" class="text-danger m-0">
                                    {{ error.$message }}
                                </p>
                            </div>

                            <div class="mb-3">
                                <label for="Password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" v-model="formData.password" id="Password"
                                    placeholder="Ingresa tu contraseña" autocomplete="off" @blur="v$.password.$touch()">
                                <p v-for="error in v$.password.$errors" :key="error.$uid" class="text-danger m-0">
                                    {{ error.$message }}
                                </p>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" v-model="formData.password_confirmation"
                                    id="password_confirmation" placeholder="Confirma tu contraseña" autocomplete="off"
                                    @blur="v$.password_confirmation.$touch()">
                                <p v-for="error in v$.password_confirmation.$errors" :key="error.$uid"
                                    class="text-danger m-0">
                                    {{ error.$message }}
                                </p>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" v-model="formData.terms"
                                    @blur="v$.terms.$touch()">
                                <label class="form-check-label" for="terms">
                                    Acepto los <router-link to="/terms" class="link-secondary">Términos de
                                        Servicio</router-link> y la <router-link to="/privacy"
                                        class="link-secondary">Política de Privacidad</router-link>
                                </label>
                                <p v-for="error in v$.terms.$errors" :key="error.$uid" class="text-danger m-0">
                                    {{ error.$message }}
                                </p>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button class="btn btn-primary" type="submit"
                                    :disabled="v$.$invalid">Registrarse</button>
                            </div>

                            <!-- <div class="text-center mb-3">
                                <span class="or-line">O</span>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button class="btn btn-outline-danger" type="button">
                                    <i class="fab fa-google me-2"></i> Google
                                </button>
                            </div> -->

                            <div class="text-center">
                                <p class="mb-0">¿Ya tienes una cuenta? <router-link to="/login"
                                        class="link-primary">Login</router-link></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>



<script setup>

import { ref, reactive, computed, onMounted } from 'vue';
import useVuelidate from '@vuelidate/core'
import { required, minLength, email, sameAs, helpers } from '@vuelidate/validators'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { containsUser, upperCase, specialCharacter, numberCharacter, spaces } from '../../utils/validations'

const router = useRouter()
const showErrors = ref(false)
const loading = ref(false)
const showPassword = ref(false)
const errors = ref('')
const inputFocus = ref(null)

// Datos del formulario
const formData = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false
});
const requiredMessage = 'El campo es obligatorio'

// Validaciones (este es un ejemplo básico, deberías implementar tu lógica de validación)
const rules = computed(() => {
    return {
        name: {
            required: helpers.withMessage(requiredMessage, required),
            minLength: minLength(8),
            //containsUser: helpers.withMessage('El campo debe tener la palabra bsl', containsUser) ,
            //spaces: helpers.withMessage('No se permiten espacios', spaces)
        },
        email: {
            required: helpers.withMessage(requiredMessage, required),
            email
        },
        password: {
            required: helpers.withMessage(requiredMessage, required),
            upperCase: helpers.withMessage('Debe contener al menos una Mayúscula', upperCase),
            specialCharacter: helpers.withMessage('Debe contener un carácter especial', specialCharacter),
            numberCharacter: helpers.withMessage('Debe contener un carácter numérico', numberCharacter),
            spaces: helpers.withMessage('No se permiten espacios', spaces)
        },
        password_confirmation: {
            required: helpers.withMessage(requiredMessage, required),
            sameAs: helpers.withMessage('Las contraseñas no coinciden', sameAs(formData.password))
        },
        terms: {
            required: helpers.withMessage(requiredMessage, required),
            sameAs: helpers.withMessage('Debes de aceptar los Términos y Política de Privacidad', sameAs(true))
        }
    }
})
const v$ = useVuelidate(rules, formData)

// Función para manejar el envío del formulario
const submitForm = async () => {
    await v$.value.$validate()
    //v$.value.$touch();
    loading.value = true
    axios.post('/api/v1/auth/register', formData).then(response => {
        loading.value = false
        localStorage.setItem('auth_token', response.data.access_token)
        localStorage.removeItem('abilities');
        console.log(response)
        router.push({ name: 'Dashboard' })
    }).catch(function (err) {
        loading.value = false
        showErrors.value = true
        errors.value = err?.response?.data?.message ?? err
        setTimeout(() => showErrors.value = false, 5000)
    })
}

onMounted(() => {
    if (inputFocus.value) {
        inputFocus.value.focus();
    }
});


</script>


<style scoped>
.card {
    width: 100%;
    max-width: 500px;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    margin-top: 80px;
    margin-bottom: 25px;
}

.card-title {
    margin-bottom: 20px;
}


.form-label {
    font-weight: bold;
}


.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}


.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}


.link-secondary {
    color: #6c757d;
    text-decoration: none;
}


.link-secondary:hover {
    color: #495057;
    text-decoration: underline;
}


.or-line {
    display: inline-block;
    position: relative;
    padding: 0 10px;
    font-weight: bold;
}


.or-line::before,
.or-line::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 100px;
    /* Aumenta este valor para hacer las líneas más largas */
    height: 2px;
    /* Aumenta el grosor de la línea */
    background-color: #ccc;
}


.or-line::before {
    left: -110px;
    /* Ajusta la posición para centrar correctamente */
}


.or-line::after {
    right: -110px;
    /* Ajusta la posición para centrar correctamente */
}
</style>