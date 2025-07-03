<template>
    <div>
        <nav class="navbar navbar-dark bg-dark">
            <div class="container-fluid">
                <button v-if="!route.meta.hideMenus" class="navbar-toggler d-md-none" type="button"
                    @click="toggleSidebar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <router-link to="/" class="navbar-brand ms-auto">
                    <span class="navbar-title">{{ dispayTitle }}</span>
                </router-link>
                <ul class="navbar-nav ms-auto ">
                    <li v-if="route.meta.showLogin" class="nav-item">
                        <router-link to="/login" class="nav-link">Login</router-link>
                    </li>
                </ul>
            </div>
            <!--<div class="text-white border">
        {{ text }}
    </div>-->
        </nav>
    </div>
</template>



<script setup>
import { inject, ref, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
const route = useRoute();

// Texto que muestra las dimensiones de la ventana
const text = ref(`width: ${window.innerWidth}, height: ${window.innerHeight}`);
// Título que deseas mostrar en la barra de navegación
const title = '';
const dispayTitle = ref(title)

// Función para actualizar las dimensiones de la ventana
const updateWindowDimensions = () => {
    text.value = `width: ${window.innerWidth}, height: ${window.innerHeight}`;
    if (window.innerWidth <= 768) { // Ajusta este valor según tus necesidades
        if (title.length > 20) {
            dispayTitle.value = `${title.slice(0, 20)}...`;
        } else {
            dispayTitle.value = title;
        }
    } else if ((window.innerWidth > 768)) {
        dispayTitle.value = title;
    }
};

// Agregar el listener del evento 'resize' cuando el componente se monta
onMounted(() => {
    window.addEventListener('resize', updateWindowDimensions);
    if (window.innerWidth <= 768) { // Ajusta este valor según tus necesidades
        if (title.length > 20) {
            dispayTitle.value = `${title.slice(0, 20)}...`;
        } else {
            dispayTitle.value = title;
        }
    }
});

// Eliminar el listener del evento 'resize' cuando el componente se desmonta
onUnmounted(() => {
    window.removeEventListener('resize', updateWindowDimensions);
});

const toggleSidebar = inject('toggleSidebar');
</script>

<style scoped>
.navbar {
    height: 55px;
}
</style>
