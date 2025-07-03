import Home from "./components/Home.vue";

import Forbidden from "./components/Errors/Forbidden.vue";
import NotFound from "./components/Errors/NotFound.vue";
import Maintenance from "./components/Errors/Maintenance.vue";

import Login from "./components/Auth/Login.vue";
import Register from "./components/Auth/Register.vue";
import ResetPassword from "./components/Auth/ResetPassword.vue";
import ForgotPassword from "./components/Auth/ForgotPassword.vue";
/* import Profile from "./components/Auth/Profile.vue"; */

import Dashboard from "./components/Dashboard.vue";
import Roles from "./components/Roles/IndexRoles.vue";
import Permissions from "./components/Permissions/IndexPermissions.vue";
import Users from './components/Users/IndexUsers.vue';
import Customers from "./components/Customers/IndexCustomers.vue";
import Inventario from "./components/Inventario/IndexMaquinaria.vue";

const routes = [
    { path: "/home", name: "Home", component: Home, meta: { hideMenus: true, showLogin: true } },
    { path: "/", redirect: "/login" },

    { path: "/:pathMatch(.*)*", name: "NotFound", component: NotFound },
    { path: "/forbidden", name: "Forbidden", component: Forbidden },
    { path: "/maintenance", name: "Maintenance", component: Maintenance, meta: { hideMenus: true } },

    { path: "/login", name: "Login", component: Login, meta: { hideMenus: true } },
    { path: "/register", name: "Register", component: Register, meta: { hideMenus: true } },
    { path: "/forgot-password", name: "ForgotPassword", component: ForgotPassword, meta: { hideMenus: true, showLogin: true } },
    { path: "/reset-password", name: "ResetPassword", component: ResetPassword, meta: { hideMenus: true, showLogin: true } },

    { path: "/dashboard", name: "Dashboard", component: Dashboard },
    { path: "/roles", name: "Roles", component: Roles, meta: { requiresAuth: true } },
    { path: "/permissions", name: "Permissions", component: Permissions, meta: { requiresAuth: true } },
    { path: "/users", name: "Users", component: Users, meta: { requiresAuth: true } },
    { path: "/customers", name: "Customers", component: Customers, meta: { requiresAuth: true } },

    { path: "/inventario", name: "Inventario", component: Inventario, meta: { requiresAuth: true } }, //ruta de inventario nueva


]


export default routes
