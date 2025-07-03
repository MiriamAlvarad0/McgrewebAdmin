<template>
    <!-- PAGINATION -->
    <div class="pt-2 text-center">
        <span>Showing {{ paginationData.from }} to {{ paginationData.to }} of {{ paginationData.total }} results</span>
        <Bootstrap5Pagination :data="paginationData" @pagination-change-page="paginationChange" class="pt-4" :limit=0
            align="center" :keepLength="true" :show-disabled="true"></Bootstrap5Pagination>
    </div>
</template>

<script setup>
import { Bootstrap5Pagination } from 'laravel-vue-pagination'
// props nos ayuda a recibir la informacion del componente papa
const props = defineProps({
    paginationData: {
        type: Object,
        required: true
    }
})
// El emit nos ayuda a regresar el valor seleccionado al componente papa
const emit = defineEmits(['paginationChange'])

const paginationChange = (page) => {
    emit('paginationChange', page)
}
</script>