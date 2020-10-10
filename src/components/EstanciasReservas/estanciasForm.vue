<!-- componente principal de definicion de formularios. Se apoya en otros 2 componentes: Filter y Grid -->
  <template>
    <div style="height: calc(100vh - 105px)">
    <q-card flat class="q-pb-xl">
      <q-list bordered>
        <q-expansion-item
          class="q-pt-none q-pl-xs q-pr-xs"
          group="somegroup"
          dense
          label="Cabecera Factura"
          default-opened
          header-class="bg-brown-1 text-grey-8"
        >
        <estanciasFormCabecera :value="value" :key="refresh" @input="updateRecord"/>
        </q-expansion-item>
        <q-separator />
        <q-expansion-item
          class="q-pt-xs q-pl-xs q-pr-xs"
          group="somegroup1"
          dense
          label="Detalle"
          default-opened
          header-class="bg-brown-1 text-grey-8"
        >
          <estanciasFormLineas :value="value" @calculaTotalesFac="calculaTotalesFac"/>
        </q-expansion-item>
      </q-list>
    </q-card>
    <q-card flat class="q-pt-lg bg-white q-gutter-xl" align="center">
        <q-btn dense label="Cancelar" color="indigo-4" icon="clear" @click="$emit('close')"/><!-- lo captura accionesMain -->
        <q-btn dense label="Guardar" color="indigo-4" icon="save" @click="refresh++"/>
    </q-card>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      recordToSubmit: {},
      title: 'Estancias',
      refresh: 0,
      valueTotales: {}
    }
  },
  computed: {
    ...mapState('login', ['user']) // importo state.user desde store-login
  },
  methods: {
    ...mapActions('estancias', ['addEstancia']),
    updateRecord (record) {
      Object.assign(this.value, record)
      Object.assign(this.value, this.valueTotales)
      this.addEstancia(this.value)
        .then(response => {
          console.log(response.data)
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    calculaTotalesFac (totales) { // cuando se guardan cambios en una linea de detalle
    //   this.valueTotales.base = totales.base
    //   this.valueTotales.totalIva = totales.totalIva
    //   this.valueTotales.retencion = Math.round(totales.base * (this.value.por_retencion / 100.0) * 100.0) / 100
    //   this.valueTotales.totalFactura = totales.base + totales.totalIva - this.valueTotales.retencion
    //   this.refresh++
    }
  },
  components: {
    estanciasFormCabecera: require('components/EstanciasReservas/estanciasFormCabecera.vue').default,
    estanciasFormLineas: require('components/EstanciasReservas/estanciasFormLineas.vue').default
  }
}
</script>
