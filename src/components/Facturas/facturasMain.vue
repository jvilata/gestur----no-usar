<template>
    <div style="height: calc(100vh - 105px)">
      <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-md q-pa-xs bg-blue-grey-1 text-grey-8">
        <!-- cabecera de formulario. Botón de busqueda y cierre de tab -->
        <q-item-section avatar>
          <q-icon name="fas fa-filter" />
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-h6">
            {{ nomFormulario }}
          </q-item-label>
          <q-item-label>
            <!-- poner un campo de fiterRecord que exista en este filtro -->
            <small>{{ Object.keys(filterRecord).length > 1 ? filterRecord : 'Pulse para definir filtro' }}</small>
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
        <!-- formulario con campos de filtro -->
        <facturasFilter
          :value="filterRecord"
          @getRecords="(val) => getRecords(val)"
          @hide="expanded = !expanded"
        />
      </q-dialog>

      <!-- formulario tabla de resultados de busqueda -->
      <facturasGrid
        v-model="registrosSeleccionados"
        />
    </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      expanded: false,
      refreshKey: 0,
      visible: '',
      filterRecord: {},
      nomFormulario: 'Facturas',
      registrosSeleccionados: []
    }
  },
  computed: {
    ...mapState('login', ['user']) // importo state.user desde store-login
  },
  methods: {
    getRecords (filterR) {
      if (filterR.nombre === 'jose') {
        Object.assign(this.registrosSeleccionados, this.filterAux)
        this.refreshKey++
        this.expanded = false
      }
    }
  },
  mounted () {
    if (this.value.filterRecord) { // si ya hemos cargado previamente los recargo al volver a este tab
      this.getRecords(this.value.filterRecord) // refresco la lista por si se han hecho cambios
    } else { // es la primera vez que entro, cargo valores po defecto
      // Object.assign(this.filterRecord, { codEmpresa: this.user.codEmpresa, estadoFactura: 'PENDIENTE' })
      this.getRecords({ idEstancia: '1' })
    }
  },
  destroyed () {
    this.$emit('changeTab', { idTab: this.value.idTab, filterRecord: Object.assign({}, this.filterRecord), registrosSeleccionados: Object.assign({}, this.registrosSeleccionados) })
  },
  components: {
    facturasFilter: require('components/Facturas/facturasFilter.vue').default,
    facturasGrid: require('components/Facturas/facturasGrid.vue').default
  }
}
</script>
