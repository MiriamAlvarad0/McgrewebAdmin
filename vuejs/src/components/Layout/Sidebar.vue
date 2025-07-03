<template>
    <div :class="['sidebar', { active: isActive }]">
        <div class="row">
            <div class="dropdown mt-6 col-8 company-logo">
                <a href="#" class="d-flex align-items-center mb-md-0 me-md-auto text-white text-decoration-none">
                    <img :src="logo" alt="Logo" class="me-2" style="height: 50px;" />
                </a>
            </div>

            <div class="col-3">
                <button @click="toggleSidebar" class="btn btn-sm btn-dark mt-6 toggleDark" aria-label="Toggle sidebar ">
                    <i class="fas fa-chevron-left"></i> <!-- Icono de flecha a la izquierda -->
                </button>
            </div>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li v-if="can('dashboard:view')">
                <router-link to="/dashboard" class="nav-link">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </router-link>
            </li>
            <li v-if="can('customers:view')">
                <router-link to="/customers" class="nav-link">
                    <i class="fas fa-users me-2"></i>
                    Customers
                </router-link>
            </li>

            <!-- INVENTARIO aqui va -->
            <li v-if="can('maquinaria:view')">
                <router-link to="/inventario" class="nav-link">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Inventario
                </router-link>
            </li>

            

            <li class="mb-1" v-if="can('users:view') || can('roles:view') || can('permissions:view')">
                <button class="btn btn-toggle align-items-center rounded collapsed nav-link" data-bs-toggle="collapse"
                    data-bs-target="#home-collapse" aria-expanded="false">
                    Users & Roles
                </button>
                <div class="collapse" id="home-collapse" style="">
                    <ul class="nav flex-column ms-3 text-extra-small">
                        <li v-if="can('users:view')">
                            <router-link to="/users" class="nav-link">
                                <i class="fas fa-user me-2"></i>
                                Users
                            </router-link>
                        </li>
                        <li v-if="can('roles:view')">
                            <router-link to="/roles" class="nav-link">
                                <i class="fas fa-user-shield me-2"></i>
                                Roles
                            </router-link>
                        </li>
                        <li v-if="can('permissions:view')">
                            <router-link to="/permissions" class="nav-link">
                                <i class="fas fa-lock me-2"></i>
                                Permissions
                            </router-link>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
        <hr>
        <div class="dropdown mt-6 user-profile">
            <a href="#" class="d-flex align-items-center nav-link text-decoration-none dropdown-toggle"
                id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                    style="width: 32px; height: 32px;">
                    <span class="text-uppercase">{{ userProfile.initials }}</span>
                </div>
                <div class="d-flex flex-column">
                    <strong>{{ userProfile.name }}</strong>
                    <small>{{ userProfile.email }}</small>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="#" @click="logout">Sign out</a></li>
            </ul>
        </div>

    </div>
</template>

<script setup>
import { inject, ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import logo from '../../assets/logo.png' // ajusta según tu ubicación real



const isActive = inject('isActive')
const toggleSidebar = inject('toggleSidebar')
const router = useRouter()
const token = ref(localStorage.getItem('auth_token'))
const userProfile = ref('')

const logout = async () => {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    axios.post('/api/v1/auth/logout').then(response => {
        localStorage.setItem('abilities', null);
        localStorage.setItem('auth_token', null);
        router.push({ name: 'Login' })
    }).catch(function (err) {
        console.log(err)
        localStorage.setItem('abilities', null);
        localStorage.setItem('auth_token', null);
        router.push({ name: 'Login' })
    })
}

const getUserProfile = () => {
    if (token.value) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        axios.get('api/v1/auth/user-profile').then((response) => {
            userProfile.value = response.data.user

            //Tomar las dos primeras iniciales del nombre y ponerlo en el circulo
            const names = response.data.user.name.split(' ');
            const firstTwoNames = names.slice(0, 2);
            userProfile.value.initials = firstTwoNames.map(name => name[0]).join(' ');

            // Poner puntos suspensivos si el nombre es mayor a 17 caracteres
            if (response.data.user.name.length > 17) {
                response.data.user.name = `${response.data.user.name.slice(0, 17)}...`;
            }

            // Poner puntos suspensivos si el email es mayor a 20 caracteres
            if (response.data.user.email.length > 20) {
                response.data.user.email = `${response.data.user.email.slice(0, 20)}...`;
            }
        }).catch(function (err) {
            console.log(err)
        })
    }
}


onMounted(() => {
    getUserProfile()
})


</script>

<style scoped>
.sidebar {
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    background-color: #0b1928;
    padding-top: 1rem;
    z-index: 1000;
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar .nav-link {
    color: #fff;
    /* Color de texto normal */
    transition: color 0.3s;
    /* Transición para el cambio de color */
    display: flex;
    align-items: center;
    /* Centra verticalmente el contenido del enlace */
}

.sidebar .nav-link:hover {
    color: rgb(106, 111, 113);
    /* Color al hacer hover */
    text-decoration: none;
    /* Opcional: quitar subrayado al hacer hover */
}

.sidebar .nav-link i {
    width: 20px;
    padding-right: 10px;
    /* Ajusta el margen derecho si es necesario */
}

.toggleDark {
    display: none;
}


@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-250px);
    }

    .content {
        margin-left: 0;
        padding-right: 20px;
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .content.active {
        margin-left: 250px;
    }

    #sidebarToggle {
        display: block;
    }

    .toggleDark {
        display: block;
    }
}

#sidebarToggle {
    display: none;
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1100;
    background-color: #343a40;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    cursor: pointer;
}

.sidebar hr {
    border-color: #fff;
}

.user-profile {
    margin-bottom: 30px;
    margin-left: 20px;
}

.company-logo {
    margin-left: 20px;
}

.text-extra-small {
    font-size: 0.80rem;
    /* Puedes ajustar el valor según tus necesidades */
}

.btn-toggle::before {
    content: '\f078';
    /* Unicode para la flecha hacia abajo (FontAwesome) */
    font-family: 'FontAwesome';
    display: inline-block;
    margin-right: 20px;
    transition: transform 0.3s ease;
}

.btn-toggle.collapsed::before {
    content: '\f054';
    /* Unicode para la flecha hacia la derecha (FontAwesome) */
    transform: rotate(0);
}

.btn-toggle[aria-expanded="true"]::before {
    transform: rotate(0deg);
}

.btn-toggle.collapsed[aria-expanded="true"]::before {
    content: '\f078';
    /* Asegura que la flecha hacia abajo se mantenga */
    transform: rotate(0);
}
</style>
