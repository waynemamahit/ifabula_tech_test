<script>
export default {
  name: "Transaksi",
  layout: AdminLayoutVue,
}
</script>

<script setup>
import axios from 'axios'
import { exportFile, useQuasar } from 'quasar'
import { onMounted, reactive } from 'vue'
import { useStore } from 'vuex';
import AdminLayoutVue from './AdminLayout.vue'

const $q = useQuasar()
const store = useStore()

const props = defineProps({
  user: {
    type: Object
  }
})

const columns = [
  {
    name: 'tanggal_input',
    required: true,
    label: 'TANGGAL INPUT',
    align: 'left',
    field: "created_at",
    format: val => {
      let tanggal = new Date(val)
      return tanggal.toJSON().split('T')[0].split('-').reverse().join('-')      
    },
    sortable: true,
    sortOrder: "ad"
  },
  {
    name: 'nama_perusahaan',
    required: true,
    label: 'NAMA PERUSAHAAN',
    align: 'left',
    field: "perusahaan",
    format: val => `${val.nama}`,
    sortable: true,
    sortOrder: "ad"
  },
  {
    name: 'nama_barang',
    required: true,
    label: 'NAMA BARANG',
    align: 'left',
    field: "barang",
    format: val => `${val.nama}`,
    sortable: true,
    sortOrder: "ad"
  },
  {
    name: "total_barang",
    required: true,
    label: 'TOTAL BARANG',
    align: 'left',
    field: "total_barang",
    format: val => `${val}`,
    sortable: true,
    sortOrder: "ad"
  },
  {
    name: "harga_barang",
    required: true,
    label: 'HARGA BARANG',
    align: 'left',
    field: "harga_barang",
    format: val => `${val}`,
    sortable: true,
    sortOrder: "ad"
  },
  {
    name: "grand_total",
    required: true,
    label: 'GRAND TOTAL',
    align: 'left',
    field: "grand_total",
    format: val => `${val}`,
    sortable: true,
    sortOrder: "ad"
  },
  {
    name: "sisa_barang",
    required: true,
    label: 'SISA BARANG',
    align: 'left',
    field: "sisa_barang",
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

const state = reactive({
  data: [],
  loading: false,
  filter: "",
  dialog: false,
  dialogTitle: "Tambah",
  perusahaan: [],
  barang: [],
  formItem: {
    total_barang: 1,
    barang: null,
    perusahaan: null
  },
  formLoading: false,
})

const filterFn = (val, update, label) => {
  if (val === '') {
    update(() => {
      state[label] = store.state[label + "Ref"]
    })
    return
  }

  update(() => {
    const needle = val.toLowerCase()
    state[label] = store.state[label + "Ref"].filter(v => v.toLowerCase().indexOf(needle) > -1)
  })
}

const updateBarang = (barangItem) => {
  let newBarang = store.state.barang
  let barangIndex = store.state.barang.findIndex(barangObj => barangObj.id === barangItem.id)
  newBarang[barangIndex] = barangItem
  
  store.commit('setData', {
    field: 'barang',
    value: newBarang
  })

  store.commit('setData', {
    field: 'barangRef',
    value: store.state.barang.map(itemRef => itemRef.nama)
  })
}

const openForm = async (label, item = null) => {
  state.dialog = true
  state.formItem = {
    total_barang: 1,
    barang: null,
    perusahaan: null
  }
}

const submit = async () => {
  state.formLoading = true
  try {
    let perusahaanItem = store.state.perusahaan.find(perusahaanItem => perusahaanItem.nama == state.formItem.perusahaan)
    let barangItem = store.state.barang.find(barangItem => barangItem.nama == state.formItem.barang)
    if (!perusahaanItem || !barangItem) throw {
      message: "Data perusahaan atau barang tidak ditemukan!"
    }

    let response = await axios.post('/transaksi/' + barangItem.id + '/' + perusahaanItem.id, {
      total_barang: state.formItem.total_barang
    })
    response = await response.data

    if (!response.data) throw response  

    let transaksiDatas = state.data

    transaksiDatas.push(response.data) 

    updateBarang(response.data.barang)

    $q.notify({
      message: response.message,
      icon: "check_circle",
      color: "green",
    })
    
    state.dialog = false
    state.data = transaksiDatas

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
    title: 'Konfirmasi Transaksi',
    message: 'Apakah anda yakin ingin menghapus data ini?',
    cancel: true,
    persistent: false,
  }).onOk(async () => {
    try {
      
      let response = await axios.delete('/transaksi/' + rowData.id)
      response = await response.data
      
      if (!response.data) throw response.message

      let newData = state.data
      let dataIndex = state.data.findIndex(dataItem => dataItem.id === rowData.id)
      newData.splice(dataIndex, 1)
      
      state.data = newData

      updateBarang(response.data)

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

    }
  })
}

const getData = async () => {
  state.loading = true
  try {
    let response = await axios.get('/transaksi')
    response = await response.data
    
    if (!response.data) throw response.message
    
    state.data = response.data
    
  } catch (error) {
    
    $q.notify({
      message: error.message || "Something went wrong, try again!",
      icon: "info",
      color: "warning",
      textColor: "black",
      closeBtn: "X",
    })

  } finally {
    state.loading = false
  }
}

const exportTable = () => {
  let content = '"TANGGAL INPUT","NAMA PERUSAHAAN","NAMA BARANG","TOTAL BARANG","HARGA BARANG","GRAND TOTAL","SISA BARANG"\n'
  content = content + state.data.map(tItem => {
    let tanggal_input = new Date(tItem.created_at)
    tanggal_input = tanggal_input.toJSON().split('T')[0].split('-').reverse().join('-')
    return `"${tanggal_input}","${tItem.perusahaan.nama}","${tItem.barang.nama}","${tItem.total_barang}","${tItem.harga_barang}","${tItem.grand_total}","${tItem.sisa_barang}"\n`
  }).join('')
  
  const status = exportFile(
    'ifabula_transaction_result.csv',
    content,
    'text/csv'
  )

  if (status !== true) {
    $q.notify({
      message: 'Browser denied file download...',
      color: 'negative',
      icon: 'warning'
    })
  }
}

onMounted(getData);
</script>

<template>
  <q-table
    :rows="state.data"
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
      <div class="text-h5">Data Transaksi</div>
      <q-space />
      <q-btn
        color="primary"
        icon-right="archive"
        label="Export to csv"
        no-caps
        @click="exportTable"
      />
      <q-btn class="q-mx-md" color="green" :disable="state.loading" icon="add" @click="openForm('Tambah')" />
      <q-input dense debounce="300" color="red" v-model="state.filter" class="q-ml-sm">
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </template>
    
    <template v-slot:body-cell-action="props">
      <q-td :props="props">
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
          <q-input
            outlined
            v-model="state.formItem.total_barang"
            autofocus
            type="number"
            label="Total Barang" color="primary">
          </q-input>

          <q-select
            outlined
            v-model="state.formItem.perusahaan"
            use-input
            input-debounce="0"
            label="Pilih Perusahaan"
            :options="state.perusahaan"
            @filter="(val, update) => filterFn(val, update, 'perusahaan')"
            style="width: 100%;"
          >
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-grey">
                  Data perusahaan tidak ditemukan!
                </q-item-section>
              </q-item>
            </template>
          </q-select>

          <q-select
            outlined
            v-model="state.formItem.barang"
            use-input
            input-debounce="0"
            label="Pilih Barang"
            :options="state.barang"
            @filter="(val, update) => filterFn(val, update, 'barang')"
            style="width: 100%;"
          >
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-grey">
                  Data barang tidak ditemukan!
                </q-item-section>
              </q-item>
            </template>
          </q-select>

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
