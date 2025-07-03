<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 position-relative d-flex justify-content-center align-items-center">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cambiar Contraseña</h4>
                        <form @submit.prevent="submitForm" id="resetPasswordForm" novalidate>
                            <div class="mb-3">
                                <label for="Password" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="Password"
                                    placeholder="Ingresa tu nueva contraseña" autocomplete="off" ref="inputFocus">
                                <p class="text-danger m-0">
                                    error message
                                </p>
                            </div>


                            <div class="mb-3">
                                <label for="ConfirmPassword" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="ConfirmPassword"
                                    placeholder="Confirma tu nueva contraseña" autocomplete="off">
                                <p class="text-danger m-0">
                                    error message
                                </p>
                            </div>


                            <div class="d-grid gap-2 mb-3">
                                <button class="btn btn-primary" type="submit">Guardar Contraseña</button>
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
    margin-top: 50px;
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


.text-danger {
    font-size: 0.875rem;
    /* Tamaño de fuente para los mensajes de error */
}
</style>