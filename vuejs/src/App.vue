<template>
  <div>
    <Navbar />
    <Sidebar v-if="!route.meta.hideMenus" />

    <div v-if="!route.meta.hideMenus" class="content">
      <Breadcrumb />
      <router-view></router-view>
    </div>
    <div v-else>
      <router-view></router-view>
    </div>

    <Footer v-if="!route.meta.hideMenus" /> 
  </div>
</template>



<script setup>
  import { provide, ref } from 'vue'
  import { useRoute } from 'vue-router'
  import Navbar     from './components/Layout/Navbar.vue'
  import Breadcrumb from './components/Layout/Breadcrumb.vue'
  import Footer     from './components/Layout/Footer.vue'
  import Sidebar    from './components/Layout/Sidebar.vue'


  const route = useRoute();
  // Funcion para el toggle del sidebar en el boton de navbar
  const isActive = ref(false)
  const toggleSidebar = () => {
    isActive.value = !isActive.value
  };


  provide('isActive', isActive);
  provide('toggleSidebar', toggleSidebar)
</script>


<style scoped>
  .content {
    margin-left: 265px;
    transition: margin-left 0.3s ease;
  }
  @media (max-width: 768px) {
    .content {
      margin-left: 15px;
      padding-right: 20px;
      padding-left: 20px;
    }
  }
</style>
