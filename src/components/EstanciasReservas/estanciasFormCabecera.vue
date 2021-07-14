<!-- componente que se llama desde accionesMain y que presenta el formulario de filtro y el boton de busqueda -->
  <template>
  <q-card class="q-pt-none q-pl-xs q-pr-xs">
      <div class="row">
        <q-input class="col-xs-6 col-sm-1" readonly outlined label="ID Estancia" stack-label v-model="recordToSubmit.id" />
          <q-select
            class="col-xs-6 col-sm-3"
            outlined
            clearable
            label="Tipo Estancia"
            stack-label
            v-model="recordToSubmit.tipoEstancia"
            :options="listaTipoEstanciaFilter"
            option-value="codElemento"
            option-label="valor1"
            emit-value
            map-options
            @filter="filterTipoEst"
            use-input
            hide-selected
            fill-input
            input-debounce="0"
          />
        <q-select
          class="col-xs-12 col-sm-8"
          outlined
          label="Cliente"
          stack-label
          v-model="recordToSubmit.idCliente"
          :options="listaClientesFilter"
          option-value="id"
          option-label="nombre"
          emit-value
          map-options
          @filter="filterClientes"
          use-input
          hide-selected
          fill-input
          input-debounce="0"
        />
          <!-- <q-select
          class="col-xs-6 col-sm-3"
          outlined
          clearable
          label="Tipo Tarifa"
          stack-label
          v-model="recordToSubmit.tipoTarifa"
          :options="listaTipoTarifa"
          option-value="codElemento"
          option-label="valor1"
          emit-value
          map-options
          use-input
          hide-selected
          fill-input
          input-debounce="0"
        /> -->
      </div>
      <div class="row">
          <q-input
            label="Fecha Entrada"
            class="col-xs-6 col-sm-3"
            clearable
            outlined
            stack-label
            :value="formatDate(recordToSubmit.fechaEntrada)"
            @input="(val) => recordToSubmit.fechaEntrada=val"
            >
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaEntrada">
                  <wgDate
                      @input="$refs.fechaEntrada.hide()"
                      v-model="recordToSubmit.fechaEntrada" />
              </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-input
            label="Fecha Salida"
            class="col-xs-6 col-sm-3"
            clearable
            outlined
            stack-label
            :value="formatDate(recordToSubmit.fechaSalida)"
            @input="(val) => recordToSubmit.fechaSalida=val"
            >
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaSalida">
                  <wgDate
                      @input="$refs.fechaSalida.hide()"
                      v-model="recordToSubmit.fechaSalida" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
        <q-input class="col-xs-12 col-sm-6" autogrow outlined label="Observaciones" stack-label v-model="recordToSubmit.observaciones" />
      </div>
      <div class="row q-mt-lg">
        <q-input class="col-xs-4 col-sm-2" outlined readonly label="Base" stack-label v-model="recordToSubmit.base" />
        <q-input class="col-xs-4 col-sm-1" outlined label="%Retención" stack-label v-model="recordToSubmit.porRetencion" @blur="$emit('calculaTotalesEst', recordToSubmit)"/>
        <q-input class="col-xs-4 col-sm-1" outlined readonly label="Importe retención" stack-label v-model="recordToSubmit.Retencion"/>
        <q-input class="col-xs-6 col-sm-2" outlined readonly label="Total IVA" stack-label v-model="recordToSubmit.totalIva"/>
        <q-input class="col-xs-6 col-sm-2" outlined readonly label="Total Estancia" stack-label v-model="recordToSubmit.totalEstancia" />
        <q-input class="col-xs-3 col-sm-1" outlined label="Fianza" stack-label v-model="recordToSubmit.Fianza" />
        <q-input class="col-xs-3 col-sm-1" outlined label="En efectivo" stack-label v-model="recordToSubmit.ACuenta" />
        <q-input class="col-xs-3 col-sm-1" outlined label="Transferencia" stack-label v-model="recordToSubmit.PorBanco" />
        <q-input class="col-xs-3 col-sm-1" outlined label="TPV" stack-label v-model="recordToSubmit.PorDatafono" />
      </div>
      <div class="row q-mt-sm">
        <q-btn outline class="col-xs-12 col-sm-2" color="primary" label="Generar Factura" @click="rellenarDatosFact" />
        <q-input
            label="Fecha Factura"
            class="col-xs-6 col-sm-2"
            clearable
            outlined
            stack-label
            :value="formatDate(recordToSubmit.FechaFactura)"
            @input="(val) => recordToSubmit.FechaFactura=val"
            >
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="FechaFactura">
                  <wgDate
                      @input="$refs.FechaFactura.hide()"
                      v-model="recordToSubmit.FechaFactura" />
              </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
        <q-input class="col-xs-6 col-sm-2" outlined label="Número Factura" stack-label v-model="recordToSubmit.NroFactura" />
      </div>
  </q-card>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'

export default {
  props: ['value'], // value es el objeto con los campos de filtro que le pasa accionesMain con v-model
  data () {
    return {
      listaClientesFilter: this.listaClientes,
      listaTipoEstanciaFilter: this.listaTipoEstancia,
      recordToSubmit: {}
    }
  },
  computed: {
    ...mapState('login', ['user']),
    ...mapState('clientes', ['listaClientes']),
    ...mapState('tablasAux', ['listaTipoEstancia', 'listaTipoTarifa'])
  },
  components: {
    wgDate: wgDate
  },
  methods: {
    ...mapActions('estancias', ['generarFactura', 'findEstancia']),
    filterClientes (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.listaClientesFilter = this.listaClientes.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterTipoEst (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.listaTipoEstanciaFilter = this.listaTipoEstancia.filter(v => v.valor1.toLowerCase().indexOf(needle) > -1)
      })
    },
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    },
    rellenarDatosFact () {
      // solo hay que generar factura cuando nroFactura sea cero
      if (this.recordToSubmit.NroFactura === null || this.recordToSubmit.NroFactura === '0' || this.recordToSubmit.NroFactura === 0 || this.recordToSubmit.NroFactura === '') {
        this.generarFactura(this.recordToSubmit)
          .then(response => {
            // volvemos a leer la factura
            this.findEstancia({ id: this.recordToSubmit.id })
              .then(response => {
                Object.assign(this.recordToSubmit, response.data[0])
              })
              .catch(error => {
                this.$q.dialog({ title: 'Error', message: error })
              })
          })
          .catch(error => {
            this.$q.dialog({ title: 'Error', message: error })
          })
      } else {
        this.$q.dialog({
          title: 'Aviso',
          message: 'Ya se ha generado una factura para esta estancia',
          ok: true,
          persistent: true
        }).onOk(() => {
          this.$emit('close')
        })
      }
    }
  },
  mounted () {
    this.listaClientesFilter = this.listaClientes
    this.listaTipoEstanciaFilter = this.listaTipoEstancia
    this.recordToSubmit = Object.assign({}, this.value)
  },
  watch: {
    recordToSubmit: { // detecta cambios en las propiedades de este objeto (tienen que estar inicializadas en data())
      handler (val) {
        this.$emit('hasChanges', { record: this.recordToSubmit, hasChanges: true, colorBotonSave: 'red' })
      },
      deep: true
    }
  },
  destroyed () {
    // guardamos valor en tabs por si despus queremos recuperarlo
    this.$emit('input', this.recordToSubmit)
  }
}
</script>
