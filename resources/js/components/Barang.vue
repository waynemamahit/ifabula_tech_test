<script>
export default {
  name: "Barang",
  layout: AdminLayoutVue,
}
</script>

<script setup>
import axios from 'axios'
import { useQuasar } from 'quasar'
import { onMounted, reactive } from 'vue'
import AdminLayoutVue from './AdminLayout.vue'
import { useStore } from 'vuex'

const props = defineProps({
  user: {
    type: Object
  }
})

const columns = [
  {
    name: 'id',
    required: true,
    label: 'ID',
    align: 'left',
    field: "id",
    format: val => `${val}`,
    sortable: true,
    sortOrder: "ad"
    
  },
  {
    name: "nama",
    required: true,
    label: 'NAMA',
    align: 'left',
    field: "nama",
    format: val => `${val}`,
    sortable: true,
    sortOrder: "ad"
    
  },
  {
    name: "harga",
    required: true,
    label: 'HARGA',
    align: 'left',
    field: "harga",
    format: val => `${val}`,
    sortable: true,
    sortOrder: "ad" 
  },
  {
    name: "stock",
    required: true,
    label: 'STOCK',
    align: 'left',
    field: "stock",
    format: val => `${val}`,
    sortable: true,
    sortOrder: "ad"
  },
  {
    name: 'action',
    label: 'ACTION',
    align: 'center',
    field: "action",
    sortable: false,
  },
]

const $q = useQuasar()
const store = useStore()

const state = reactive({
  loading: false,
  filter: "",
  dialog: false,
  dialogTitle: "Tambah",
  formItem: {
    nama: "",
    harga: 1,
    stock: 1,
  },
  formLoading: false,
})

const openForm = async (label, item = null) => {
  state.dialog = true
  state.dialogTitle = label
  state.formItem = {
    nama: "",
    harga: 1,
    stock: 1,
  }

  if (label === "Edit" && item) {
    state.dialogTitle = item ? "Edit" : "Tambah"
    state.formItem = {
      id: item.id,
      nama: item.nama,
      harga: item.harga,
      stock: item.stock,
    }
  }
}

const submit = async () => {
  state.formLoading = true
  try {
    
    let method = 'post'
    let url = '/barang'

    if (state.dialogTitle === "Edit") {
      method = 'put'
      url = '/barang/' + state.formItem.id
    }
    
    let formBody = Object.assign({}, state.formItem)
    
    let response = await axios[method](url, formBody)
    response = await response.data

    if (!response.data) throw response.message    

    let barangDatas = store.state.barang
    if (state.dialogTitle === "Edit") {
      console.log(state.formItem.id)
      let findIndex = store.state.barang.findIndex(dataItem => dataItem.id == state.formItem.id)
      barangDatas[findIndex] = response.data
    } else {
      barangDatas.push(response.data) 
    }

    $q.notify({
      message: response.message,
      icon: "check_circle",
      color: "green",
    })
    
    state.dialog = false
    
    store.commit('setData', {
      field: 'barang',
      value: barangDatas
    })

    store.commit('setData', {
      field: 'barangRef',
      value: store.state.barang.map(itemRef => itemRef.nama)
    })

  } catch (error) {
    
    $q.notify({
      message: error.message || "Something went wrong, try again!",
      icon: "info",
      color: "warning",
      textColor: "black",
      closeBtn: "X",
    })

    if (error.errors) {
      let errors = Object.values(error.errors)
      errors = errors.join('\n')
      
      $q.notify({
        message: errors,
        icon: "info",
        color: "warning",
        textColor: "black",
        closeBtn: "X",
      })
    }
    
  } finally {
    state.formLoading = false
  }
}

const confirmDelete = (rowData) => {
  $q.dialog({
    title: 'Konfirmasi Barang',
    message: 'Apakah anda yakin ingin menghapus data ini?',
    cancel: true,
    persistent: false,
  }).onOk(async () => {
    try {
      
      let response = await axios.delete('/barang/' + rowData.id)
      response = await response.data
      
      if (!response.data) throw response.message

      let newData = store.state.barang
      let dataIndex = store.state.barang.findIndex(dataItem => dataItem.id === rowData.id)
      newData.splice(dataIndex, 1)
      
      store.commit('setData', {
        field: 'barang',
        value: newData
      })

      store.commit('setData', {
        field: 'barangRef',
        value: store.state.barang.map(itemRef => itemRef.nama)
      })

      $q.notify({
        message: response.message,
        icon: "check_circle",
        color: "green",
      })

      
    } catch (error) {
      
      $q.notify({
        message: error.message || "Something went wrong, try again!",
        icon: "info",
        color: "warning",
        textColor: "black",
        closeBtn: "X",
      })

      if (error.errors) {
        let errors = Object.values(error.errors)
        errors = errors.join('\n')
        
        $q.notify({
          message: errors,
          icon: "info",
          color: "warning",
          textColor: "black",
          closeBtn: "X",
        })
      }

    }
  })
}

const getData = async () => {
  state.loading = true
  try {
    let response = await axios.get('/barang')
    response = await response.data
    
    if (!response.data) throw response

    
    store.commit('setData', {
      field: 'barang',
      value: response.data
    })

    store.commit('setData', {
      field: 'barangRef',
      value: store.state.barang.map(itemRef => itemRef.nama)
    })
    
  } catch (error) {
    
    $q.notify({
      message: error.message || "Something went wrong, try again!",
      icon: "info",
      color: "warning",
      textColor: "black",
      closeBtn: "X",
    })

    if (error.errors) {
      let errors = Object.values(error.errors)
      errors = errors.join('\n')
      
      $q.notify({
        message: errors,
        icon: "info",
        color: "warning",
        textColor: "black",
        closeBtn: "X",
      })
    }

  } finally {
    state.loading = false
  }
}

onMounted(getData);
</script>

<template>
  <q-table
    :rows="store.state.barang"
    :columns="columns"
    :loading="state.loading"
    :filter="state.filter"
    :pagination="{
      descending: false,
      page: 1,
      rowsPerPage: 3
      // rowsNumber: xx if getting data from a server
    }"
    no-data-label="Data tidak ditemukan!"
    no-results-label="Hasil tidak ditemukan!"
  >
    <template v-slot:top>
      <div class="text-h5">Data Barang</div>
      <q-space />
      <q-btn color="green" :disable="state.loading" icon="add" @click="openForm('Tambah')" />
      <q-input dense debounce="300" color="red" v-model="state.filter" class="q-ml-sm">
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </template>
    
    <template v-slot:body-cell-action="props">
      <q-td :props="props">
        <q-btn size="md" flat round icon="edit" color="info" class="q-mx-sm" @click="openForm('Edit', props.row)" />
        <q-btn size="md" flat round icon="delete" color="warning" class="q-mx-sm" @click="confirmDelete(props.row)"/>
      </q-td>
    </template>

    <template v-slot:loading>
      <q-inner-loading showing color="red-10" />
    </template>
    
    <template v-slot:no-data="{ icon, message, filter }">
      <div class="full-width row flex-center text-accent q-gutter-sm text-yellow-10 cursor-pointer" @click="getData">
        <q-icon size="2em" name="sentiment_dissatisfied" />
        <span>{{ message }}</span>
        <q-icon size="2em" :name="filter ? 'filter_b_and_w' : icon" />
      </div>
    </template>
  </q-table>

  <q-dialog v-model="state.dialog">
    <q-card style="width: 700px; max-width: 80vw;">
      <q-card-section class="row items-center q-pb-none q-mb-md">
        <div class="text-h6">{{ state.dialogTitle }} Form</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>

      <q-card-section class="q-pt-none">
        <q-form @submit.prevent="submit">
          <q-input outlined v-model="state.formItem.nama" autofocus type="text" label="Nama Barang" color="primary">
          </q-input>

          <q-input outlined v-model="state.formItem.harga" type="number" label="Harga Barang" color="primary">
          </q-input>

          <q-input outlined v-model="state.formItem.stock" type="number" label="Stock Barang" color="primary">
          </q-input>

          <q-btn
            color="primary"
            size="lg"
            label="SUBMIT"
            type="submit"
            style="width: 100%;"
            :loading="state.formLoading"
          />
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>
