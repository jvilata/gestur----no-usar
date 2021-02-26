<template>
    <div style="height: calc(100vh - 105px)">
      <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-md q-pa-xs bg-blue-grey-1 text-grey-8">
        <!-- cabecera de formulario. Botón de busqueda y cierre de tab -->
        <q-item-section avatar>
          <q-icon name="event_available" />
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
        <estanciasFilter
          :value="filterRecord"
          @input="(value) => Object.assign(filterRecord, value)"
          @getRecords="getRecords"
          @hide="expanded = !expanded"
        />
      </q-dialog>

      <!-- formulario tabla de resultados de busqueda -->
      <estanciasGrid
        :value="filterRecord"
        fromEstanciasMain=true
        :key="refreshKey"
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
      nomFormulario: 'Estancias y Reservas',
      registrosSeleccionados: []
    }
  },
  computed: {
    ...mapState('login', ['user']) // importo state.user desde store-login
  },
  methods: {
    getRecords (filter) {
      // hago la busqueda de registros segun condiciones del formulario Filter que ha lanzado el evento getRecords
      Object.assign(this.filterRecord, filter)
      this.refreshKey++
      this.expanded = false
    }
  },
  mounted () {
    if (this.value.filterRecord) { // si ya hemos cargado previamente los recargo al volver a este tab
      this.getRecords(this.filterRecord)
    } else { // es la primera vez que entro, cargo valores po defecto
      this.filterRecord = {}
    }
  },
  destroyed () {
    this.$emit('changeTab', { idTab: this.value.idTab, filterRecord: Object.assign({}, this.filterRecord), registrosSeleccionados: Object.assign({}, this.registrosSeleccionados) })
  },
  components: {
    estanciasFilter: require('components/EstanciasReservas/estanciasFilter.vue').default,
    estanciasGrid: require('components/EstanciasReservas/estanciasGrid.vue').default
  }
}
</script>
