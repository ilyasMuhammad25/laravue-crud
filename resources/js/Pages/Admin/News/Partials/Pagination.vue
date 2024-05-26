<!-- In `Partials/Pagination.vue` -->
<template>
    <div>
        <ul class="pagination">
            <li v-for="link in links" :key="link.label" :class="['page-item', { 'active': link.active }]">
                <inertia-link v-if="link.url" :href="link.url" class="page-link" v-html="link.label"></inertia-link>
                <span v-else class="page-link" v-html="link.label"></span>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        links: Array,
    },
};
</script>

<style>
.pagination {
    display: flex;
    list-style: none;
    padding: 0;
}
.page-item {
    margin: 0 5px;
}
.page-link {
    padding: 0.5rem 0.75rem;
    border: 1px solid #ddd;
    color: #007bff;
    text-decoration: none;
    cursor: pointer;
}
.page-item.active .page-link {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
</style>
