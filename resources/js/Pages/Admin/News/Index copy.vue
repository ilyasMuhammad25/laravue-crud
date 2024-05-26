<!-- In `resources/js/Pages/News/Index.vue` -->
<script setup>
import { ref, computed } from 'vue';
import { useForm,usePage, Link  } from '@inertiajs/vue3';
import Pagination from './Partials/Pagination.vue';

const news = computed(() => usePage().props.value.news);

/* Modal */
const showModal = ref(false);
const selectedItem = ref({});
const handleShowModal = (item) => {
    selectedItem.value = item;
    showModal.value = true;
};

const handleCloseModal = () => {
    showModal.value = false;
    selectedItem.value = {};
};
/* End */
</script>

<template>
    <div>
        <h1>News List</h1>
        <div class="mb-3 p-5 flex items-center justify-between flex-wrap gap-3">
            <input type="text" placeholder="Search ... " v-model="filter" @input="search">
            <button @click="handleShowModal({})">Create News</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in news.data" :key="item.id">
                    <td>{{ item.title }}</td>
                    <td>{{ item.content }}</td>
                    <td>
                        <img :src="`/storage/${item.file_image}`" v-if="item.file_image" alt="News Image" style="width: 50px; height: 50px;">
                    </td>
                    <td>
                        <button @click="handleShowModal(item)">Edit</button>
                        <form :action="`/news/${item.id}`" method="POST" style="display: inline;">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>

        <Pagination :links="news.links" />

        <div v-if="showModal">
            <UserFormModal :item="selectedItem" :show="showModal" @close="handleCloseModal" />
        </div>
    </div>
</template>

<script>
// import UserFormModal from './Partials/UserFormModal.vue';
import Pagination from './Partials/Pagination.vue';

export default {
    components: {
        UserFormModal,
        Pagination,
    },
};
</script>
