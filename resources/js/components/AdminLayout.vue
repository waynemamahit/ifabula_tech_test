<template>
  <q-layout view="lHh lpR lFf">

    <q-header elevated class="bg-primary text-white">
      <q-toolbar class="bg-primary text-white">
        
        <q-toolbar-title @click="goTo('/home')" class="bg-primary text-white cursor-pointer">
          IFABULA | TECH TEST
        </q-toolbar-title>

        <q-btn color="danger" icon="more_vert" flat>
          <q-menu>
            <q-list style="min-width: 150px">
              <q-item clickable @click="goTo('/profile')">
                <q-item-section>
                  <span class="text-primary">
                    <q-icon name="person" color="primary" size="sm" />
                    Profile
                  </span>
                </q-item-section>
              </q-item>
              <q-item clickable @click="logout()">
                <q-item-section>
                  <span class="text-primary">
                    <q-icon name="logout" color="primary" size="sm" />
                    Logout
                  </span>
                </q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>        
      </q-toolbar>
    </q-header>

    <q-page-container>
      <slot />
    </q-page-container>

    <q-footer elevated class="bg-grey-9 text-white">
      <q-toolbar>
        <q-toolbar-title>
          <div class="text-center">
            Copyright 
            <span class="text-bold">{{ new Date().getFullYear() }}</span> 
            by Nusantara IT Developer
          </div>
        </q-toolbar-title>
      </q-toolbar>
    </q-footer>

  </q-layout>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import { useQuasar } from 'quasar'

export default {
  name: "AdminLayout",
  props: {
    user: {
      type: Object
    },
  },
  setup (props) {
    const $q = useQuasar()

    return {
      goTo(path, method = 'get') {
        Inertia[method](path, {}, {
          onError() {
            $q.notify({
              message: "Terjadi kesalahan coba lagi!",
              color: "warning",
              textColor: "black"
            })
          }
        })
      },
      logout() {
        $q.dialog({
          title: 'Logout Confirm',
          message: 'Would you like to logout?',
          cancel: true,
          persistent: false
        }).onOk(() => {
          window.localStorage.removeItem('menu')
          Inertia.get('/logout', {}, {
            onError: errors => {
              errors = Object.values(errors)
              errors = errors.join('\n')
              
              $q.notify({
                message: errors,
                color: 'danger',
              })
            }
          });
        })
      }
    }
  }
}
</script>

<style scoped>
.q-layout {
  padding: 20px;
  background-color: rgb(236, 236, 236);
}
</style>