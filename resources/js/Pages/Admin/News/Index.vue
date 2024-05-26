<script setup>

import NewsFormModal from './Partials/NewsFormModal.vue';


const app = computed(() => usePage().props.app);
const data = computed(() => usePage().props.data);
const items = computed(() => data.value.items);
const columns = computed(() => data.value.columns);

const { form, reset, processing, onSort } = useSearchFilter(route('news.index'));

/* Modal */
const showModal = ref(false);
const selectedItem = ref('');
const handleShowModal = (item) => {
	selectedItem.value = item;
	showModal.value = true;
};

const handleCloseModal = () => {
	showModal.value = false;
	selectedItem.value = {};
};

const handleDelete = async (item) => {
    if (confirm('Are you sure you want to delete this news item?')) {
        try {
            await axios.post(route('news.destroy', item.id));
            // Remove the deleted item from the items list
            items.value = items.value.filter(i => i.id !== item.id);
            alert('News deleted successfully');
        } catch (error) {
            alert('An error occurred while deleting the news');
        }
    }
};
/* End */
</script>

<template>
	<AppContainer>
	
		<SectionCard class="mt-4 h-full overflow-hidden">
			<div class="mb-3 p-5 flex items-center justify-between flex-wrap gap-3">
				<SearchInput placeholder="Search ... " v-model="form.filter.global" @reset="reset"></SearchInput>
				<Button as="button" shadow="primary" class="py-1" @click="handleShowModal({})">
					<Icon name="plus" class="h-6" />
				</Button>
			</div>

			<LoadingProgressBar v-if="processing" />

			<DataTable :items="items.data" :columns="columns" class="rounded-xl" @onSort="onSort" :key="Date.now().toString() + 1">
				
				<template #cell(role)="{ item }">
					<div class="table-cell !text-left !px-1">
						<p v-for="role in item.roles">{{ role.name }}</p>
					</div>
				</template>

				<template #cell(file_image)="{ item,imagePath }">
					
					<img :src="`uploads/news/${item.file_image}`" alt="Image" class="h-10 w-10 object-cover rounded-full">

        </template>
				<template #cell(actions)="{ item }">
					<Button intent="text" siz="xs" as="button" @click="handleShowModal(item)">
						<Icon name="edit" class="h-4" />
					</Button>
					<Button intent="text" siz="xs" as="button" @click="() => handleDelete(item)">
						<Icon name="trash" class="h-4" />
					</Button>
				</template>

			
			</DataTable>

			<div v-if="showModal">
				<NewsFormModal :item="selectedItem" :show="showModal" @close="handleCloseModal()" />
			</div>

		</SectionCard>
	</AppContainer>
</template>

<script>

import Layout from '@/Layouts/AdminLayout.vue';
import { defineComponent } from 'vue';

export default defineComponent({
	props: ['imagePath'], // Define imagePath as a prop
    data() {
        return {
            items: [] // Assuming you have items array
        };
    },
	layout: Layout,
});
</script>
