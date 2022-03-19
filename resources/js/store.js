import { createStore } from 'vuex'

// Create a new store instance.
export default createStore({
  state() {
    return {
      barang: [],
      perusahaan: [],
      barangRef: [],
      perusahaanRef: []
    }
  },
  mutations: {
    setData(state, item) {
      state[item.field] = item.value
    }
  },
})
