<template>
  <q-layout view="lHh lpR fFf">
    <q-header elevated >
      <q-toolbar>
        <q-btn flat @click="leftDrawerOpen = !leftDrawerOpen" round dense icon="menu" />
        <div class="items-center no-wrap absolute-center">
          <div class="text-subtitle1">{{ nomAplicacion }}</div>
        </div>
        <div class="q-gutter-sm q-pr-md row items-center no-wrap absolute-right">
          <q-btn round flat class="bg-red-9 text-weight-light">
            <q-avatar size="40px">
              Hola
            </q-avatar>
            <q-tooltip>Account</q-tooltip>
            <q-menu auto-close :offset="[110, 0]">
              <q-card>
                <q-card-section>
                  <div class="row">
                    <div class="col-4">
                      <q-avatar round flat size="80px" class="bg-red-9 text-white text-weight-light">
                        JV
                      </q-avatar>
                    </div>
                    <div class="col">
                      <div class="text-weight-bold">Jose Vilata</div>
                      <div>jvilata</div>
                      <q-btn flat class="text-weight-light" color="primary" @click="desconectar">Desconectar</q-btn>
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </q-menu>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>
    <q-drawer v-model="leftDrawerOpen"
        show-if-above bordered
        content-class="bg-grey-3"
        :mini="miniState"
        @mouseover="miniState = false"
        @mouseout="miniState = true"
        :breakpoint="767"
        :width="220">
      <q-scroll-area style="height: calc(100vh - 170px); margin-top: 90px; border-right: 1px solid #ddd">
        <q-list>
          <div v-for="link in menuItems" :key="link.title">
            <q-item
              clickable
              @click.native="openForm(link.link)"
              exact
              class="text-grey-8"  >
               <!--Todos los campos son visibles por Los usuarios excepto el de APROBACION - mounted-->
              <q-item-section v-if="link.icon" avatar> <!--Iconos del DRAWER -->
                <q-icon :name="link.icon"  v-if="link.title " />
              </q-item-section>

              <q-item-section><!--Títulos del DRAWER -->
                <q-item-label v-ripple clickable v-if="link.title">{{ link.title  }}</q-item-label>
              </q-item-section>
            </q-item>
          </div>
        </q-list>
      </q-scroll-area>

    </q-drawer>
    <q-footer>
    </q-footer>
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'MainLayout',
  data () {
    return {
      nomAplicacion: 'Gestión Camping',
      leftDrawerOpen: false,
      miniState: false,
      menuItems: [
        {
          title: 'Clientes',
          icon: 'group',
          link: {
            name: 'clientesMain',
            label: 'Clientes'
          }
        },
        {
          title: 'Estancias/Reservas',
          icon: 'event_available',
          link: {
            name: 'estanciasReservasMain',
            label: 'Estancias/Reservas'
          }
        },
        {
          title: 'Administración',
          icon: 'chat',
          link: 'https://chat.quasar.dev'
        },
        {
          title: 'Auxiliares',
          icon: 'record_voice_over',
          link: 'https://forum.quasar.dev'
        }
      ]
    }
  },
  methods: {
    ...mapActions('tabs', ['addTab']),
    openForm (link) {
      this.addTab([link.name, link.label, {}, 1])
    },
    desconectar () {
      this.desconectarLogin()
    }
  }
}
</script>

<style lang="scss">
.q-header {
  padding-top: 20px;
}

.q-drawer .q-router-link--exact-active {
    color: white !important;
}
</style>
