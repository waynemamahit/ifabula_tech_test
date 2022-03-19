
<script setup>
import { Inertia } from "@inertiajs/inertia";
import { useQuasar } from "quasar";
import { ref } from "vue"

const $q = useQuasar()
const email = ref("")
const password = ref("")
const isPwd = ref(true)
const loading = ref(false);

let login = async () => {
  loading.value = true

  Inertia.post('/', {
    email: email.value,
    password: password.value,
    remember: false
  }, {
    onError: (errors) => {
      errors = Object.values(errors)
      errors = errors.join('\n')

      $q.notify({
        message: errors,
        icon: 'info',
        color: 'warning',
        textColor: 'black'
      })
    },
    onFinish: () => {
      loading.value = false;
    }
  })
}

const goTo = (path) => {
  Inertia.get(path)
};
</script>

<template>
  <div class="row justify-center items-center container">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
      <q-card>
        <q-card-section>
          <q-form @submit.prevent="login()">
            <div class="text-h5 text-center">
              <q-icon color="primary" name="account_circle" />
              <br/>
              Sign In
            </div>
            <q-input outlined v-model="email" type="email" label="Username" color="primary">
              <template v-slot:prepend>
                <q-icon
                  color="primary"
                  name="email" 
                />
              </template>
            </q-input>
            <q-input v-model="password" outlined :type="isPwd ? 'password' : 'text'" label="Password" color="primary">
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
            <q-btn
              color="primary"
              size="lg"
              label="LOGIN"
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

<style scoped>
.container {
  height: 100vh;
  background: #3590e5;  /* fallback for old browsers */
  background: -webkit-linear-gradient(to right, #5ba6e3, #3590e5);  /* Chrome 10-25, Safari 5.1-6 */
  background: linear-gradient(to right, #5bb1e3, #3590e5); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
}
</style>