<template>
    <div style="height: calc(100vh - 105px)">
      <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-md q-pa-xs bg-blue-grey-1 text-grey-8">
        <!-- cabecera de formulario. Botón de busqueda y cierre de tab -->
        <q-item-section avatar>
          <q-icon name="euro" />
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-h6">
            {{ nomFormulario }}
          </q-item-label>
          <q-item-label>
            <!-- poner un campo de fiterRecord que exista en este filtro -->
            <small>{{ Object.keys(filterRecord).length > 0 ? filterRecord : 'Pulse para definir filtro' }}</small>
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
        <gastosCajaFilter
          :value="filterRecord"
          @getRecords="(val) => getRecords(val)"
          @hide="expanded = !expanded"
        />
      </q-dialog>

      <!-- formulario tabla de resultados de busqueda -->
      <gastosCajaGrid
        :value="filterRecord"
        :key="refreshKey"
      />
    </div>
</template>

<script>
import { mapState } from 'vuex'
import { date } from 'quasar'

export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      expanded: false,
      refreshKey: 0,
      visible: '',
      filterRecord: {},
      nomFormulario: 'Gastos Caja/Devoluciones',
      registrosSeleccionados: []
    }
  },
  computed: {
    ...mapState('login', ['user']) // importo state.user desde store-login
  },
  methods: {
    getRecords (filterR) {
      Object.assign(this.filterRecord, filterR)
      this.refreshKey++
      this.expanded = false
    }
  },
  mounted () {
    if (this.value.filterRecord) { // si ya hemos cargado previamente los recargo al volver a este tab
      this.getRecords(this.value.filterRecord) // refresco la lista por si se han hecho cambios
    } else { // es la primera vez que entro, cargo valores po defecto
      // Object.assign(this.filterRecord, { codEmpresa: this.user.codEmpresa, estadoFactura: 'PENDIENTE' })
      this.filterRecord.fechaInicio = date.formatDate(date.subtractFromDate(new Date(), { days: 30 }), 'YYYY-MM-DD')
      this.getRecords(this.filterRecord)
    }
  },
  destroyed () {
    this.$emit('changeTab', { idTab: this.value.idTab, filterRecord: Object.assign({}, this.filterRecord), registrosSeleccionados: Object.assign({}, this.registrosSeleccionados) })
  },
  components: {
    gastosCajaFilter: require('components/GastosCaja/gastosCajaFilter.vue').default,
    gastosCajaGrid: require('components/GastosCaja/gastosCajaGrid.vue').default
  }
}
</script>
