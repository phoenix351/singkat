<template>
  <Head title="Aplikasi" />
  <AppLayout>
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0">
          Daftar Aplikasi
        </div>
        <div class="flex space-x-2 items-center w-full md:w-full lg:w-auto">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText v-model.trim="searchField" placeholder="Cari Tim Kerja" />
          </IconField>
          <Button
            icon="pi pi-download"
            rounded
            aria-label="Download"
            severity="success"
            class="mr-2 mb-2 lg:mb-0"
          />
          <!-- @click="showToast" -->
          <Button
            @click="createDialog = true"
            severity="info"
            rounded
            class="mb-2 lg:mb-0"
          >
            <i class="pi pi-plus"></i>
            Tambah Aplikasi Baru
          </Button>
        </div>
      </div>
    </div>
    <Dialog
      v-model:visible="createDialog"
      modal
      header="Tambah Aplikasi Baru"
      class="min-w-[30vw]"
    >
      <div class="flex flex-col gap-6">
        <div>
          <label class="block font-bold mb-3">Nama Aplikasi</label>
          <InputText
            v-model="form.label"
            placeholder="Isi nama aplikasi"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-bold mb-3">Deskripsi</label>
          <InputText
            v-model="form.deskripsi"
            placeholder="Isi deskripsi"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-bold mb-3">Link Aplikasi</label>
          <InputText
            v-model="form.route_link"
            placeholder="Isi link aplikasi"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-bold mb-3">Navigation</label>
          <Select
            :options="[
              { label: 'react', value: 'react' },
              { label: 'reload', value: 'reload' },
            ]"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="form.navigation"
            placeholder="Isi navigation"
          />
        </div>
        <div>
          <label class="block font-bold mb-3">Sedang maintenance</label>
          <div class="flex flex-wrap gap-4">
            <div class="flex items-center gap-2">
              <RadioButton
                v-model="form.maintenance"
                input-id="maintenance_true"
                name="maintenance"
                :value="1"
              />
              <label for="maintenance_true">Ya</label>
            </div>
            <div class="flex items-center gap-2">
              <RadioButton
                v-model="form.maintenance"
                input-id="maintenance_false"
                name="maintenance"
                :value="0"
              />
              <label for="maintenance_false">Tidak</label>
            </div>
          </div>
        </div>
        <div v-if="form.maintenance == true">
          <label class="block font-bold mb-3">Pesan Maintenance</label>
          <InputText
            v-model="form.maintenance_message"
            placeholder="Isi pesan maintenance"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-bold mb-3">Upload Logo Aplikasi</label>
          <FileUpload
            ref="fileupload"
            mode="basic"
            name="file"
            accept="image/*"
            :max-file-size="1000000"
            @select="onSelectFile"
          />
        </div>
      </div>
      <template #footer>
        <Button
          label="Cancel"
          @click="createDialog = false"
          size="small"
          severity="danger"
          autofocus
        />
        <Button
          @click="submit({ update: false })"
          label="Simpan"
          size="small"
          severity="success"
          autofocus
        />
      </template>
    </Dialog>
  </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/ManManagement/AppLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const searchField = ref(null);
const createDialog = ref(false);
const form = useForm({
  _token: null,
  label: null,
  deskripsi: null,
  route_link: null,
  navigation: null,
  maintenance: 0,
  maintenance_message: null,
  image: null,
});

watch(
  () => form.maintenance,
  () => {
    if (!form.maintenance) form.maintenance_message = null;
  }
);

const onSelectFile = (event) => {
  form.image = event?.files?.[0] ?? null;
};

//submit and update
const submit = async ({ update = false }) => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    form._token = tokens;
    form.post(route("man-management.app-management.store"), {
      preserveScroll: false,
      preserveState: false,
    });
  } catch (error) {
    console.error(error);
  }
};
</script>

<style scoped></style>
