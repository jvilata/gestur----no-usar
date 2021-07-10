<template>
    <div style="height: calc(100vh - 105px)">
      <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-md q-pa-xs bg-blue-grey-1 text-grey-8">
        <!-- cabecera de formulario. Botón de busqueda y cierre de tab -->
        <q-item-section avatar>
          <q-icon name="grading" />
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-h6">
            {{ nomFormulario }}
          </q-item-label>
          <q-item-label>
            <small>{{ Object.keys(filterRecord).length ? filterRecord : 'Pulse para definir filtro' }}</small>
          </q-item-label>
        </q-item-section>
        <q-item-section side>
          <q-btn
            @click="$emit('close')"
            flat
            round
            dense
            icon="close"/>
        </q-item-section>
      </q-item>
      <q-dialog v-model="expanded"  >
        <!-- Componente de filtro -->
        <cuadreCajaFilter
            :value="filterRecord"
            @getRecords="(val) => getRecords(val)"
            @close="expanded = !expanded"
        />
      </q-dialog>
      <!-- formulario tabla de resultados de busqueda -->
      <cuadreCajaGrid
        :value="filterRecord"
        :key="refreshKey"
        />
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { date } from 'quasar'
export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      expanded: false,
      refreshKey: 0,
      visible: '',
      filterRecord: {},
      nomFormulario: 'Cuadre Caja',
      registrosSeleccionados: []
    }
  },
  computed: {
    ...mapState('login', ['user']) // importo state.user desde store-login
  },
  methods: {
    ...mapActions('cuadrecaja', ['findCuadreCaja', 'addGastos', 'borrarGastos']),
    getRecords (filterR) {
      Object.assign(this.filterRecord, filterR) // no haría falta pero así obliga a refrescar el componente para que visulice el filtro
      this.findCuadreCaja(filterR)
        .then(response => {
          this.registrosSeleccionados = response.data
          this.expanded = false
          this.refreshKey++
        })
        .catch(error => {
          this.registrosSeleccionados = {}
          this.$q.dialog({ title: 'Error', message: error })
          this.desconectarLogin()
        })
    }
  },
  mounted () {
    if (this.value.filterRecord) { // FilterRecord tiene valores porque ya hemos hecho una búsqueda
      this.expanded = false // Escondemos clientesFilter
      this.getRecords(this.value.filterRecord) // refresco la lista por si se han hecho cambios
    } else { // no esta inicializado
      this.registrosSeleccionados = []
      this.filterRecord.fechaInicial = date.formatDate(new Date(), 'YYYY-01-01')
      this.getRecords(this.filterRecord)
    }
  },
  destroyed () {
    this.$emit('changeTab', { idTab: this.value.idTab, filterRecord: Object.assign({}, this.filterRecord), registrosSeleccionados: Object.assign({}, this.registrosSeleccionados) })
  },
  components: {
    cuadreCajaFilter: require('components/CuadreCaja/cuadreCajaFilter.vue').default,
    cuadreCajaGrid: require('components/CuadreCaja/cuadreCajaGrid.vue').default
  }
}
</script>
