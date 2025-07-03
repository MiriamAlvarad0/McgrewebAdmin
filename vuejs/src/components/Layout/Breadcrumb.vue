<template>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <!-- <router-link class="breadcrumb-item" to="/home">Inicio</router-link> -->
            <li class="breadcrumb-item active breadcrumb-text" aria-current="page" v-for="item in items"
                :key="item.path">
                {{ item.name }}
            </li>

        </ol>
    </nav>
</template>


<script setup>
import { useRoute } from 'vue-router'
import { computed } from 'vue';
const route = useRoute()
const items = computed(() => {
    return route.matched
})
</script>


<style scoped>
nav {
    margin-top: 40px;
}

.breadcrumb-text {
  font-weight: 600;    /* Semi bold */
  font-size: 1.1rem;   /* Tamaño de letra */
  text-transform: capitalize; /* Capitaliza el texto */
}

</style>