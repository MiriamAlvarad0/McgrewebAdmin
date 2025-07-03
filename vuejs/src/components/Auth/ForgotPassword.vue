<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 position-relative d-flex justify-content-center align-items-center">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">¿Olvidaste tu contraseña?</h4>
                        <p class="text-center">Ingresa tu correo electrónico y te enviaremos un enlace para restablecer
                            tu contraseña.</p>
                        <form @submit.prevent="submitForm" id="forgotPasswordForm" novalidate>
                            <div class="mb-3">
                                <label for="Email" class="form-label">Correo</label>
                                <input type="email" class="form-control" id="Email"
                                    placeholder="Ingresa tu correo electrónico">
                                <p class="text-danger m-0">
                                    Error message
                                </p>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" type="submit">Enviar enlace</button>
                            </div>
                            <div class="text-center mt-3">
                                <router-link to="/login" class="link-secondary">Regresar al login</router-link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

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
</style>