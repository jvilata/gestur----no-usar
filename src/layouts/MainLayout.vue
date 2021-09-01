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
              {{ user.login ? user.login.slice(0,2).toUpperCase() : '' }}
            </q-avatar>
            <q-tooltip>Cuenta</q-tooltip>
            <q-menu auto-close :offset="[110, 0]">
              <q-card>
                <q-card-section>
                  <div class="row">
                    <div class="col-4">
                      <q-avatar round flat size="80px" class="bg-red-9 text-white text-weight-light">
                        {{ user.login ? user.login.slice(0,2).toUpperCase() : 'MAL' }}
                      </q-avatar>
                    </div>
                    <div class="col-1"> </div>
                    <div class="col-7">
                      <div class="text-weight-bold">
                        {{ user.pers.nombre ? user.pers.nombre : 'MAL' }}
                      </div>
                      <div>
                      {{ user.login ? user.login : 'MAL' }}
                      </div>
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
      <q-scroll-area style="height: calc(100vh - 170px); margin-top: 210px; border-right: 1px solid #ddd">
        <q-list>
          <div v-for="link in menuItems" :key="link.title">
            <q-item
              v-if="link.title !== 'Otros'"
              clickable
              @click.native="openForm(link.link)"
              exact
              class="text-grey-8"  >
              <q-item-section v-if="link.icon" avatar> <!--Iconos del DRAWER -->
                <q-icon :name="link.icon"  v-if="link.title " />
              </q-item-section>
              <q-item-section><!--Títulos del DRAWER -->
                <q-item-label v-ripple clickable v-if="link.title">{{ link.title  }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-expansion-item
              class="text-grey-8"
              v-else
              icon="settings"
              label="Otros" >
              <div v-for="link in otros" :key="link.title">
                <q-item clickable @click.native="openForm(link.link)" exact>
                  <!--ICONOS del DRAWER -->
                  <!-- <q-item-section>
                    <q-icon :name="link.icon"  v-if="link.title " />
                  </q-item-section> -->
                  <q-item-section><!--Títulos del DRAWER -->
                    <q-item-label
                      v-ripple
                      clickable
                      v-if="link.title" >
                      {{ link.title  }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </div>
            </q-expansion-item>
          </div>
        </q-list>
      </q-scroll-area>
      <q-img class="absolute-top max-height: 10px" src="~assets/LOGO_CAMPING1240.png" />
    </q-drawer>
    <q-footer>
    </q-footer>
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { mapActions, mapState } from 'vuex'
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
          title: 'Estancias/Facturas',
          icon: 'event_available',
          link: {
            name: 'estanciasMain',
            label: 'Estancias/Facturas'
          }
        },
        {
          title: 'Gastos de Caja',
          icon: 'euro',
          link: {
            name: 'gastosCajaMain',
            label: 'Gastos de Caja'
          }
        },
        {
          title: 'Cuadre de Caja',
          icon: 'grading',
          link: {
            name: 'cuadreCajaMain',
            label: 'Cuadre de Caja'
          }
        },
        {
          title: 'Disco Guardia Civil',
          icon: 'shield',
          link: {
            name: 'discoGCMain',
            label: 'Disco Guardia Civil'
          }
        },
        {
          title: 'Dashboard',
          icon: 'dashboard',
          link: {
            name: 'dashboard',
            label: 'Dashboard'
          }
        },
        {
          title: 'Otros',
          icon: 'settings',
          link: {
            name: 'otros',
            label: 'Otros'
          }
        }
      ],
      otros: [
        {
          title: 'Tablas Auxiliares',
          icon: 'chat',
          link: {
            name: 'tablasMain',
            label: 'Tablas Auxiliares'
          }
        },
        {
          title: 'Lista de Servicios',
          icon: 'chat',
          link: {
            name: 'serviciosMain',
            label: 'Lista de Servicios'
          }
        },
        {
          title: 'Tarifas de Servicios',
          icon: 'chat',
          link: {
            name: 'tarifasMain',
            label: 'Tarifas de Servicios'
          }
        },
        {
          title: 'Usuarios',
          icon: 'shield',
          link: {
            name: 'usuariosMain',
            label: 'Usuarios'
          }
        }
      ]
    }
  },
  methods: {
    ...mapActions('tablasAux', ['loadTablasAux']),
    ...mapActions('tabs', ['addTab']),
    ...mapActions('clientes', ['comboListaClientes']),
    ...mapActions('login', ['desconectarLogin']),
    openForm (link) {
      this.addTab([link.name, link.label, {}, 1])
    },
    desconectar () {
      this.desconectarLogin()
    }
  },
  computed: {
    ...mapState('login', ['user'])
  },
  mounted () {
    this.loadTablasAux()
    this.comboListaClientes()
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
