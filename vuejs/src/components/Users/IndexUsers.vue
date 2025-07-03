<template>
  <div>
    <h4 class="m-3">Users List</h4>
    <div v-if="loading" class="loading">
      <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <div v-else>
      <div class="container-fluid" v-if="can('users:view')">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-4 col-sm-12 col-sx-12">
                <!-- Search bar -->
                <SearchForm @search="handleSearch" />
              </div>
              <div class="col-md-6 col-sm-8">
                <!-- Radio buttons Para validar si esta inactivo el registro -->
                <FilterRadios @filter="handleRadioFilter" />
              </div>
              <div class="col-md-2 col-sm-4">
                <button v-if="can('users:create')" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#userModal">Add User</button>
              </div>
            </div>
            <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Id</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Active</th>
                  <th>ExternalId</th>
                  <th class="text-end">Options</th>
                </tr>
              </thead>
              <tbody> 
                <tr v-for="item in items" :key="item.id" class="border-b">
                  <td :class="item.active == false ? 'bg-inactive': 'bg-active'" data-label="Id">{{ item.id }}</td>
                  <td :class="item.active == 0 ? 'bg-inactive': 'bg-active'" data-label="Name">{{  item.name }}</td>
                  <td :class="item.active == 0 ? 'bg-inactive': 'bg-active'" data-label="Email">{{ item.email }}</td>
                  <td :class="item.active == 0 ? 'bg-inactive': 'bg-active'" data-label="Role">{{ item.role }}</td>
                  <td :class="item.active == 0 ? 'bg-inactive': 'bg-active'" data-label="Active">{{ (item.active == 1) ? true : false }} </td>
                  <td :class="item.active == 0 ? 'bg-inactive': 'bg-active'" data-label="ExternalId">{{ item.external_id }}</td>
                  <td :class="item.active == 0 ? 'bg-inactive': 'bg-active'" data-label="Options" class="options">
                    <div class="d-flex justify-content-end">
                      <div class="d-inline-flex">
                        <button class="btn btn-warning m-1 edit-btn" 
                          data-bs-toggle="modal" 
                          data-bs-target="#userModal"
                          :data-id="item.id"
                          :data-name="item.name"
                          :data-email="item.email"
                          :data-role="item.role"
                          :data-active="item.active"
                          :data-externalid="item.external_id"
                          v-if="can('users:edit')"
                        >
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger m-1 destroy-btn" 
                          :data-id="item.id"
                          v-if="can('users:delete')"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
            <Pagination :paginationData="paginationData" @paginationChange="handlePagination" />
          </div>
        </div>
        <!-- Componente Modal de Usuario -->
        <UserModal 
          :data="state.user"
          @result="handleResultModal"
        />
      </div>
      <div v-else>
        <Forbidden />
      </div>
    </div>
  </div>
</template>

<script setup>
  import { onMounted, ref, reactive } from 'vue'
  import axios from 'axios'
  import SearchForm from '../Common/SearchForm.vue'
  import FilterRadios from '../Common/FilterRadios.vue';
  import Pagination from '../Common/Pagination.vue'
  import UserModal from './UpsertUsers.vue'
  import Swal from 'sweetalert2/dist/sweetalert2'
  import Forbidden from '../Errors/Forbidden.vue'
  import { definedAbilities } from '../../utils/abilities'

  const perPage = 5
  const loading = ref(true)
  const items = ref([])
  const active = ref([])
  const searchFilter = ref('')
  const setTimeoutSearch = ref('')
  let paginationData = []
  const radioFilter = ref([])
  const token = ref(localStorage.getItem('auth_token'))

  const state = reactive({
    user: {
      id: '',
      name: '',
      email: '',
      role: '',
      active: ''
    },
  })

  // Obtener los usuarios de la base de datos
  const getUsers = (page = 1, limit = perPage, search = '', active = '') => {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    axios.get('/api/v1/users',{
      params: { page, limit, search, active }
    }).then(response => {
      loading.value = false
      items.value = response.data.users.data
      paginationData = response.data.users
    }).catch(err => {
      loading.value = false
      console.log(err)
     // error.value = err.response.data.message
    })
  }

  //Filtrar usando los inputs de radios (Activos e Inactivos)
  const handleRadioFilter = (filter) => {
    radioFilter.value = filter
    switch(radioFilter.value){
      case 'all': 
        getUsers(1, perPage, searchFilter.value, active.value = [0, 1])
        break
      case 'active':
        getUsers(1, perPage, searchFilter.value, active.value = [1])
        break
      case 'inactive':
        getUsers(1, perPage, searchFilter.value, active.value = [0])
        break
      default:
        getUsers(1, perPage, searchFilter.value, active.value = [0, 1])
        break
    }
  }

  //Se ejecuta una vez se haya creado o modificado el registro en el modal
  const handleResultModal = () => {
    getUsers()
  }
  // Filtrar usando el input search
  const handleSearch = (search) => {
    searchFilter.value = search
    // Funcion para retrazar la busquedas en la base de datos (menos peticiones)
    if (setTimeoutSearch.value) {
      clearTimeout(setTimeoutSearch.value);
    }
    setTimeoutSearch.value = setTimeout(() => {
      getUsers(1, perPage, searchFilter.value, active.value) // Modificar dependiendo necesidades
    }, 1000)
  }

  const handlePagination = (page) => {
    getUsers(page, perPage, searchFilter.value, active.value)
  }

  const handleClick = async (event) => {
    // Manejar clics en botones de borrar
    if (event.target.closest('.destroy-btn')) {
      const destroyBtn = event.target.closest('.destroy-btn')
      const id = destroyBtn.dataset.id
      Swal.fire({
        title: "Do you want to delete the user?",
        text: "Are you sure? You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonText: "Delete",
        confirmButtonColor: "#d33"
      }).then((result) => {
        if (result.isConfirmed) {
          axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
          axios.delete('/api/v1/users/' + id).then(response => {
            getUsers()
            Swal.fire("User deleted!", "", "success")
          }).catch(function(err){ 
            console.log(err?.response?.data?.message ?? err)
          });
        } 
      });

    }
    // Manejar clics en botones de editar
    if (event.target.closest('.edit-btn')) {
      const editBtn = event.target.closest('.edit-btn')
      state.user.id = editBtn.dataset.id
      state.user.name = editBtn.dataset.name
      state.user.email = editBtn.getAttribute('data-email')
      state.user.role = editBtn.dataset.role
      state.user.active = (editBtn.dataset.active == 1) ? true : false
    }
  }

  onMounted(async() =>{
    getUsers()
    document.addEventListener('click', handleClick)

    definedAbilities()
    
    setInterval(() => {
      definedAbilities()
    }, 60000)
  })
</script>

<style scoped>
  .bg-inactive {
    color: #ee0519; /* Color de fondo para elementos inactivos */
    font-weight: bold; /* Texto en negritas */
  }

  .bg-active {
    color: #0f0f0f; /* Color de fondo para elementos activos */ 
  }

  @media (max-width: 900px) {
    .table-responsive {
      border: 0;
    }

    .table-responsive table {
      width: 100%;
      margin-bottom: 15px;
      background-color: transparent;
    }

    .table-responsive table thead, 
    .table-responsive table tbody, 
    .table-responsive table th, 
    .table-responsive table td, 
    .table-responsive table tr {
      display: block;
    }

    .table-responsive table thead tr {
      position: absolute;
      top: -9999px;
      left: -9999px;
    }

    .table-responsive table tr {
      border: 1px solid #ccc;
    }

    .table-responsive table td {
      border: none;
      border-bottom: 1px solid #eee;
      position: relative;
      padding-left: 50%;
      text-align: right;
      height: 40px;
    }

    .table-responsive table .options {
      height: 60px;
    }

    .table-responsive table td:before {
      position: absolute;
      top: 6px;
      left: 6px;
      width: 45%;
      padding-right: 10px;
      white-space: nowrap;
      text-align: left;
      font-weight: bold;
      content: attr(data-label);
    }
  }
</style>