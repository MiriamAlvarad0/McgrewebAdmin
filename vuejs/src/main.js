import { createApp } from 'vue'
import App from './App.vue'
import { createRouter, createWebHistory } from 'vue-router'
import routes from './routes'
import 'bootstrap/dist/css/bootstrap.min.css'
import '@fortawesome/fontawesome-free/css/all.min.css'
import 'bootstrap'
import axios from 'axios'
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'
import { functions } from './utils/abilities'

// Determinar la URL base en función del hostname
const productionBaseURL = import.meta.env.VITE_productionBaseURL || window.location.origin
const localBaseURL = import.meta.env.VITE_localBaseURL
axios.defaults.baseURL = window.location.hostname === 'localhost' ? localBaseURL : productionBaseURL

const router = createRouter({
    history: createWebHistory(),
    routes
})

function loggedIn() {
    return localStorage.getItem('auth_token')
}

router.beforeEach((to, from, next) => {
    if(to.matched.some(record => record.meta.requiresAuth)) {
        // Esta ruta requiere auth, checa si esta logueado, si no lo redirecciona a login
        if(!loggedIn()){
            next({path: '/login', query: {redirect: to.fullPath}})
        }else{
            next()
        }
    }else{
        next()
    }
})



const app = createApp(App)

app.use(VueSweetalert2)
app.use(functions)
app.use(router)
app.mount('#app')
//createApp(App).mount('#app')
