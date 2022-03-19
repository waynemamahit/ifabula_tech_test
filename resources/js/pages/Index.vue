<script>
import AdminLayoutVue from '../components/AdminLayout.vue'
import Perusahaan from '../components/Perusahaan.vue'
import Barang from '../components/Barang.vue'
import Transaksi from '../components/Transaksi.vue'

export default {
  name: "Index",
  layout: AdminLayoutVue,
  components: { Perusahaan, Barang, Transaksi }
}
</script>

<template>
<q-card>
    <q-list bordered class="rounded-borders">
      <q-expansion-item
        expand-separator
        icon="warehouse"
        label="Barang"
      >
        <barang></barang>
      </q-expansion-item>

      <q-expansion-item
        expand-separator
        icon="business"
        label="Perusahaan"
      >
        <perusahaan></perusahaan>
      </q-expansion-item>
      
      <q-expansion-item
        expand-separator
        icon="payment"
        label="Transaksi"
      >
        <transaksi></transaksi>
      </q-expansion-item>

    </q-list>
  </q-card>
</template>