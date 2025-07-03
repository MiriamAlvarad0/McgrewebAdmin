<template>
    <div>
        <h4 class="m-3">Customers List</h4>
        <div v-if="loading" class="loading">
            <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div v-else>
            <div class="container-fluid" >
                <div class="card">
                <div class="card-body">
                    <div class="row">
                    <div class="col-md-4 col-sm-12 col-sx-12">
                        <!-- Search bar -->
                        <SearchForm />
                    </div>
                    <div class="col-md-6 col-sm-8">
                        <!-- Radio buttons Para validar si esta inactivo el registro -->
                        <FilterRadios />
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#userModal">Add Customer</button>
                    </div>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Active</th>
                        <th>ExternalId</th>
                        <th class="text-end">Options</th>
                        </tr>
                    </thead>
                    <tbody> 

                    </tbody>
                    </table>
                </div>
                    <!-- Pagination -->
                </div>
                </div>
                <!-- Componente Modal de Usuario -->
                <!--
                <CustomerModal 
                :data="state.user"
                @result="handleResultModal"
                />
                -->
            </div>
            
        <!--  <div >
                <Forbidden />
            </div> -->
        
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue'
    import SearchForm from '../Common/SearchForm.vue'
    import FilterRadios from '../Common/FilterRadios.vue';
    //import CustomerModal from './UpsertCustomers.vue'
    import Forbidden from '../Errors/Forbidden.vue'

    const loading = ref(true)

    // Only test
    setInterval(() => {
        loading.value = false
    }, 500)
</script>

<style scoped>
</style>