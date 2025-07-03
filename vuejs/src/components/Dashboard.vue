<template>
    <div class="welcome-container d-flex justify-content-center align-items-center">
        <div class="text-center">
            <h1 class="fw-bold text-primary display-4">¡Bienvenido!</h1>
            <p class="lead text-muted">Seguimos trabajando en nuevas mejoras 😊</p>
            
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { definedAbilities } from '../utils/abilities'

onMounted(() => {
    definedAbilities()
    setInterval(definedAbilities, 60000)
})
</script>

<style scoped>
.welcome-container {
    height: 60vh;
    background: linear-gradient(135deg, #f0f4ff);
    padding: 2rem;
}
</style>
