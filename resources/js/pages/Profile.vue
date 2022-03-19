<template>
  <div class="row justify-center">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
      <q-card>
        <q-card-section>
          <div class="text-h4">Profile Settings</div>
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="submit()">
            <q-input outlined v-model="name" autofocus type="text" label="Name" color="primary">
              <template v-slot:prepend>
                <q-icon
                  color="primary"
                  name="account_circle" 
                />
              </template>
            </q-input>
            <q-input outlined v-model="email" label="Email" color="primary">
              <template v-slot:prepend>
                <q-icon
                  color="primary"
                  name="email" 
                />
              </template>
            </q-input>
            <q-input v-model="current_password" outlined :type="isPwd ? 'password' : 'text'" label="Current Password" color="primary">
              <template v-slot:prepend>
                <q-icon
                  color="primary"
                  name="key"
                />
              </template>
              <template v-slot:append>
                <q-icon
                  color="primary"
                  :name="isPwd ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="isPwd = !isPwd"
                />
              </template>
            </q-input>
            <q-input v-model="new_password" outlined :type="isNewPwd ? 'password' : 'text'" label="New Password" color="primary">
              <template v-slot:prepend>
                <q-icon
                  color="primary"
                  name="password"
                />
              </template>
              <template v-slot:append>
                <q-icon
                  color="primary"
                  :name="isNewPwd ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="isNewPwd = !isNewPwd"
                />
              </template>
            </q-input>
            <q-btn
              color="primary"
              size="lg"
              label="SUBMIT"
              type="submit"
              style="width: 100%;"
              :loading="loading"
            />
          </q-form>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { ref } from 'vue'
import { useStore } from 'vuex'
import { useQuasar } from 'quasar'
import AdminLayoutVue from '../components/AdminLayout.vue'

export default {
  name: "Setting",
  layout: AdminLayoutVue,
  props: {
    user: {
      type: Object
    }
  },
  setup(props) {
    const store = useStore()
    const $q = useQuasar()
    
    const name = ref(props.user.name)
    const email = ref(props.user.email)
    const current_password = ref("")
    const new_password = ref("")
    const isPwd = ref(true)
    const isNewPwd = ref(true)
    const loading = ref(false)
    
    const submit = async () => {
      loading.value = true

      try {
        let response = await axios.post('/profile', {
          name: name.value,
          email: email.value,
          current_password: current_password.value,
          new_password: new_password.value,
        })
        response = await response.data;
        
        if (!response.data) throw response;
        
        $q.notify({
          message: response.message,
          color: "green",
          textColor: "white",
          icon: "check_circle"
        })
        
        store.commit('changeUser', {
          name: name.value,
          email: email.value,
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
        loading.value = false
      }
    }
    
    return {
      name,
      email,
      current_password,
      new_password,
      isPwd,
      isNewPwd,
      loading,
      submit
    }
  }
}
</script>

<style>

</style>