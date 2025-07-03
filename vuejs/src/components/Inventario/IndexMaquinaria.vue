<template>
    <div>
        <h4 class="m-3">Maquinaria List</h4>

        <!-- Spinner de carga -->
        <div v-if="loading" class="loading">
            <div class="spinner-border text-primary" style="width:4rem;height:4rem" role="status">
                <span class="visually-hidden">Loading…</span>
            </div>
        </div>

        <div v-else>
            <div class="container-fluid" v-if="can('maquinaria:view')">
                <div class="card">
                    <div class="card-body">

                        <!-- Fila: buscador, radios y botón Nuevo -->
                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                                <SearchForm @search="handleSearch" />
                            </div>

                            <div class="col-md-6 col-sm-8">
                                <FilterRadios @filter="handleRadioFilter" />
                            </div>

                            <div class="col-md-2 col-sm-4">
                                <button v-if="can('maquinaria:create')" class="btn btn-primary float-end"
                                    data-bs-toggle="modal" data-bs-target="#maquinariaModal">
                                    Add Maquinaria
                                </button>
                            </div>
                        </div>

                        <!-- Tabla -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Subcategoría</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Año</th>
                                        <th>Precio</th>
                                        <th>Disponible</th>
                                        <th class="text-end">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in items" :key="item.id" class="border-b">
                                        <td :class="rowClass(item)">{{ item.id }}</td>
                                        <td :class="rowClass(item)">{{ item.nombre }}</td>
                                        <td :class="rowClass(item)">{{ item.categoria }}</td>
                                        <td :class="rowClass(item)">{{ item.subcategoria }}</td>
                                        <td :class="rowClass(item)">{{ item.marca }}</td>
                                        <td :class="rowClass(item)">{{ item.modelo }}</td>
                                        <td :class="rowClass(item)">{{ item.ano }}</td>
                                        <td :class="rowClass(item)">{{ item.precio }}</td>
                                        <td :class="rowClass(item)">
                                            {{ item.disponible ? 'Sí' : 'No' }}
                                        </td>
                                        <td :class="rowClass(item)" data-label="Options" class="options">
                                            <div class="d-flex justify-content-end">
                                                <div class="d-inline-flex">
                                                    <button class="btn btn-warning m-1 edit-btn" data-bs-toggle="modal"
                                                        data-bs-target="#maquinariaModal" :data-id="item.id"
                                                        :data-nombre="item.nombre" :data-categoria="item.id_categoria"
                                                        :data-subcategoria="item.id_subcategoria"
                                                        :data-marca="item.marca" :data-modelo="item.modelo"
                                                        :data-ano="item.ano" :data-precio="item.precio"
                                                        :data-disponible="item.disponible"
                                                        v-if="can('maquinaria:edit')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-danger m-1 destroy-btn" :data-id="item.id"
                                                        v-if="can('maquinaria:delete')">
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

                <!-- Modal Maquinaria -->
                <MaquinariaModal :data="state.maquinaria" @result="handleResultModal" />
            </div>

            <!-- Sin permiso -->
            <div v-else>
                <Forbidden />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import SearchForm from '../Common/SearchForm.vue'
import FilterRadios from '../Common/FilterRadios.vue'
import Pagination from '../Common/Pagination.vue'
import MaquinariaModal from './UpsertMaquinaria.vue'
import Swal from 'sweetalert2/dist/sweetalert2'
import Forbidden from '../Errors/Forbidden.vue'
import { definedAbilities } from '../../utils/abilities'

/* ------------ STATE ------------ */
const perPage = 5
const loading = ref(true)
const items = ref([])
const disponibleFilter = ref([0, 1])          // 0‑No  1‑Sí
const searchFilter = ref('')
const setTimeoutSearch = ref('')
let paginationData = []
const radioFilter = ref([])
const token = ref(localStorage.getItem('auth_token'))

/* Datos que viajan al modal (para editar) */
const state = reactive({
    maquinaria: {
        id: '',
        nombre: '',
        id_categoria: '',
        id_subcategoria: '',
        marca: '',
        modelo: '',
        ano: '',
        precio: '',
        disponible: false,
    },
})

/* ------------ API CALL ------------ */
const getMaquinaria = (page = 1, limit = perPage, search = '', disponible = []) => {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    axios
        .get('/api/v1/maquinaria', { params: { page, limit, search, disponible } })
        .then(({ data }) => {

            loading.value = false
            items.value = data.maquinaria.data
            paginationData = data.maquinaria
        })
        .catch(err => {
            loading.value = false
            console.error(err)
        })
}

/* ------------ FILTERS ------------ */
const handleRadioFilter = (filter) => {
    radioFilter.value = filter
    switch (filter) {
        case 'all':
            disponibleFilter.value = [0, 1]
            break
        case 'available':
            disponibleFilter.value = [1]
            break
        case 'unavailable':
            disponibleFilter.value = [0]
            break
        default:
            disponibleFilter.value = [0, 1]
    }
    getMaquinaria(1, perPage, searchFilter.value, disponibleFilter.value)
}

const handleSearch = (search) => {
    searchFilter.value = search
    if (setTimeoutSearch.value) clearTimeout(setTimeoutSearch.value)

    setTimeoutSearch.value = setTimeout(() => {
        getMaquinaria(1, perPage, searchFilter.value, disponibleFilter.value)
    }, 1000)
}

const handlePagination = (page) => {
    getMaquinaria(page, perPage, searchFilter.value, disponibleFilter.value)
}

/* ------------ CRUD BUTTONS ------------ */
const handleClick = (event) => {
    /* ELIMINAR */
    if (event.target.closest('.destroy-btn')) {
        const id = event.target.closest('.destroy-btn').dataset.id

        Swal.fire({
            title: 'Eliminar maquinaria?',
            text: '¡No podrás revertir esto!',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            confirmButtonColor: '#d33',
        }).then(result => {
            if (result.isConfirmed) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
                axios.delete(`/api/v1/maquinaria/${id}`)
                    .then(() => {
                        getMaquinaria()
                        Swal.fire('Eliminado!', '', 'success')
                    })
                    .catch(err => console.error(err))
            }
        })
    }

    /* EDITAR   -> cargamos datos al modal */
    if (event.target.closest('.edit-btn')) {
        const el = event.target.closest('.edit-btn')
        state.maquinaria = {
            id: el.dataset.id,
            nombre: el.dataset.nombre,
            id_categoria: el.dataset.categoria,
            id_subcategoria: el.dataset.subcategoria,
            marca: el.dataset.marca,
            modelo: el.dataset.modelo,
            ano: el.dataset.ano,
            precio: el.dataset.precio,
            disponible: el.dataset.disponible == 'true',
        }
    }
}

/* Tras guardar o editar en modal */
const handleResultModal = () => getMaquinaria()

/* Render class fila (disponible/no) */
const rowClass = (item) =>
    item.disponible ? 'bg-active' : 'bg-inactive'

/* ------------ ON MOUNT ------------ */
onMounted(() => {
    getMaquinaria()
    document.addEventListener('click', handleClick)
    definedAbilities()
    setInterval(definedAbilities, 60000)
})
</script>

<style scoped>
.bg-inactive {
    color: #ee0519;
    font-weight: bold
}

.bg-active {
    color: #0f0f0f
}

@media (max-width:900px) {

    /* Tabla responsiva móvil */
    .table-responsive {
        border: 0
    }

    .table-responsive table {
        width: 100%;
        margin-bottom: 15px;
        background: transparent
    }

    .table-responsive table thead,
    .table-responsive table tbody,
    .table-responsive table th,
    .table-responsive table td,
    .table-responsive table tr {
        display: block
    }

    .table-responsive table thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px
    }

    .table-responsive table tr {
        border: 1px solid #ccc
    }

    .table-responsive table td {
        border: none;
        border-bottom: 1px solid #eee;
        position: relative;
        padding-left: 50%;
        text-align: right;
        height: 40px
    }

    .table-responsive table .options {
        height: 60px
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
        content: attr(data-label)
    }
}
</style>
