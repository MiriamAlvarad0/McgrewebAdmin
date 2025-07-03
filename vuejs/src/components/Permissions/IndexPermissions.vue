<template>
  <div>
    <h4 class="m-3">Permissions List</h4>
    <div v-if="loading" class="loading">
      <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <div v-else>
      <div v-if="can('permissions:view')" class="container-fluid">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-4 col-sm-12 col-sx-12">
                
                <!-- Search bar -->
                <SearchForm @search="handleSearch" />
              </div>
              <div class="col-md-12 col-sm-12">
                <button v-if="can('permissions:create')" class="btn btn-primary float-end" data-bs-toggle="modal"
                  data-bs-target="#permissionModal">Add Permission</button>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th class="text-end">Options</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in items" :key="item.id" class="border-b">
                    <td>{{ item.id }}</td>
                    <td>{{ item.name }}</td>
                    <td>
                      <div class="d-flex justify-content-end">
                        <div class="d-inline-flex">
                          <button class="btn btn-warning m-1 edit-btn" data-bs-toggle="modal"
                            data-bs-target="#permissionModal" :data-id="item.id" :data-name="item.name"
                            v-if="can('permissions:edit')">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button class="btn btn-danger m-1 destroy-btn" :data-id="item.id"
                            v-if="can('permissions:delete')">
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
        <!-- Componente Modal -->
        <PermissionModal :data="state.role" @result="handleResultModal" />
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
import Pagination from '../Common/Pagination.vue'
import PermissionModal from './UpsertPermissions.vue'
import Swal from 'sweetalert2/dist/sweetalert2'
import Forbidden from '../Errors/Forbidden.vue'

const perPage = 5
const loading = ref(true)
const items = ref([])
const searchFilter = ref('')
const setTimeoutSearch = ref('')
let paginationData = []
const token = ref(localStorage.getItem('auth_token'))

const state = reactive({
  role: {
    id: '',
    name: ''
  },
})

// Obtener los roles de la base de datos
const getData = (page = 1, limit = perPage, search = '') => {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
  axios.get('/api/v1/permissions', {
    params: { page, limit, search }
  }).then(response => {
    loading.value = false
    items.value = response.data.roles.data
    paginationData = response.data.roles
  }).catch(err => {
    loading.value = false
    console.log(err)
  })
}

const handleResultModal = () => {
  getData()
}
// Filtrar usando el input search
const handleSearch = (search) => {
  searchFilter.value = search
  // Funcion para retrazar la busquedas en la base de datos (menos peticiones)
  if (setTimeoutSearch.value) {
    clearTimeout(setTimeoutSearch.value);
  }
  setTimeoutSearch.value = setTimeout(() => {
    getData(1, perPage, searchFilter.value)
  }, 1000)
}

const handlePagination = (page) => {
  getData(page, perPage, searchFilter.value)
}

const handleClick = async (event) => {
  // Manejar clics en botones de borrar
  if (event.target.closest('.destroy-btn')) {
    const destroyBtn = event.target.closest('.destroy-btn')
    const id = destroyBtn.dataset.id
    Swal.fire({
      title: "Do you want to delete the permission?",
      text: "Are you sure? You won't be able to revert this!",
      showCancelButton: true,
      confirmButtonText: "Delete",
      confirmButtonColor: "#d33"
    }).then((result) => {
      if (result.isConfirmed) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        axios.delete('/api/v1/permissions/' + id).then(response => {
          getData()
          Swal.fire("Permission deleted!", "", "success")
        }).catch(function (err) {
          console.log(err?.response?.data?.message ?? err)
          Swal.fire("Error!", err?.response?.data?.message ?? err, "warning")
        });
      }
    });
  }
  // Manejar clics en botones de editar
  if (event.target.closest('.edit-btn')) {
    const editBtn = event.target.closest('.edit-btn')
    state.role.id = editBtn.dataset.id
    state.role.name = editBtn.dataset.name
  }
}

onMounted(async () => {
  getData()
  document.addEventListener('click', handleClick)

})
</script>

<style scoped></style>