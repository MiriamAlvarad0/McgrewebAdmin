<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 position-relative d-flex justify-content-center align-items-center">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img :src="logo" alt="MCGRE Logo" class="logo-img" />
                        </div>
                        <h4 class="card-title text-center"></h4>
                        <form @submit.prevent="submitForm" id="userForm" novalidate>
                            <div v-if="showErrors" class="alert alert-danger" role="alert">
                                {{ errors }}
                            </div>
                            <div class="mb-1">
                                <label for="Email" class="form-label">Correo</label>
                                <input type="email" class="form-control" v-model="formData.email" id="Email"
                                    placeholder="Ingresa tu correo electrónico" ref="inputFocus">
                                <p v-for="error in v$.email.$errors" :key="error.$uid" class="text-danger m-0">
                                    {{ error.$message }}
                                </p>
                            </div>

                            <div class="mb-3">
                                <label for="Password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" v-model="formData.password" id="Password"
                                    placeholder="Ingresa tu contraseña" autocomplete="off">
                                <p v-for="error in v$.password.$errors" :key="error.$uid" class="text-danger m-0">
                                    {{ error.$message }}
                                </p>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Recordarme</label>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button class="btn btn-primary" type="submit" :disabled="v$.$invalid">Login</button>
                            </div>

                            <!-- <div class="text-center mb-3">
                                <span class="or-line">O</span>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button class="btn btn-outline-danger" type="button">
                                    <i class="fab fa-google me-2"></i> Google
                                </button>
                            </div>

                            <div class="mt-3 text-center mb-3">
                                <router-link to="/forgot-password" class="link-secondary">Olvidé mi
                                    contraseña</router-link>
                            </div> -->
                            <div class="text-center">
                                <p class="mb-0">¿No tienes una cuenta? <router-link to="/register"
                                        class="link-primary">Regístrate</router-link></p>
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
import { required, email, helpers } from '@vuelidate/validators'
import axios from 'axios'
import { useRouter, useRoute } from 'vue-router'
import logo from '../../assets/mcgre.png'

const inputFocus = ref(null)
const router = useRouter()
const route = useRoute();
const showErrors = ref(false)
const loading = ref(false)
const showPassword = ref(false)
const errors = ref('')

const formData = reactive({
    email: '',
    password: ''
})

// Se puede cambiar el idioma de los mensajes implementando i18n https://vuelidate-next.netlify.app/advanced_usage.html#i18n-support
const requiredMessage = 'El campo es obligatorio'
const emailValid = 'El valor no es una dirección de correo electrónico válida'
const rules = computed(() => {
    return {
        email: {
            required: helpers.withMessage(requiredMessage, required),
            email: helpers.withMessage(emailValid, email)
        },
        password: {
            required: helpers.withMessage(requiredMessage, required)
        }
    }
})

const v$ = useVuelidate(rules, formData)

const submitForm = async () => {
    await v$.value.$validate()
    loading.value = true
    axios.post('/api/v1/auth/login', formData).then(response => {
        loading.value = false
        localStorage.setItem('auth_token', response.data.access_token)
        localStorage.removeItem('abilities');
        // Redirige a la ruta origen o a una ruta por defecto
        const redirectTo = route.query.redirect || '/dashboard';
        router.push(redirectTo);
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
.logo-img {
    max-width: 200px;
    height: auto;
}

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