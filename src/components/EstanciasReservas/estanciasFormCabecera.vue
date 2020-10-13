<!-- componente que se llama desde accionesMain y que presenta el formulario de filtro y el boton de busqueda -->
  <template>
  <q-card class="q-pt-none q-pl-xs q-pr-xs">
      <div class="row">
        <q-input class="col-xs-4 col-sm-1" readonly outlined label="ID Estancia" stack-label v-model="recordToSubmit.id" />
        <q-select
          class="col-xs-8 col-sm-4"
          outlined
          label="Cliente"
          stack-label
          v-model="recordToSubmit.idCliente"
          :options="listaClientesFilter"
          map-options
          option-value="id"
          option-label="nombre"
          emit-value
          @filter="filterClientes"
          use-input
          hide-selected
          fill-input
          input-debounce="0"
        />
          <q-select
            class="col-xs-6 col-sm-3"
            outlined
            label="Tipo Estancia"
            stack-label
            v-model="recordToSubmit.tipoEstancia"
            :options="listaTiposEstancia"
          />
        <q-input class="col-xs-3 col-sm-1" outlined label="Nº. Ticket" stack-label v-model="recordToSubmit.nroTicket" />
        <q-input class="col-xs-3 col-sm-3" outlined label="FianzaEntr." stack-label v-model="recordToSubmit.fianzaEntregada" />
      </div>
      <div class="row q-mt-xs">
          <q-input
            label="Fecha Inicio"
            class="col-xs-6 col-sm-2"
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
            label="Fecha Fin"
            class="col-xs-6 col-sm-2"
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
        <q-select
          class="col-xs-5 col-sm-2"
          outlined
          label="Tarifa"
          stack-label
          v-model="recordToSubmit.tipoTarifa"
          :options="listaTarifas"
        />
        <q-input class="col-xs-7 col-sm-6" autogrow outlined label="Observaciones" stack-label v-model="recordToSubmit.observaciones" />
      </div>
  </q-card>
</template>

<script>
import { mapState } from 'vuex'
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'

export default {
  props: ['value'], // value es el objeto con los campos de filtro que le pasa accionesMain con v-model
  data () {
    return {
      listaClientesFilter: this.listaCiientes,
      recordToSubmit: {},
      listaTiposEstancia: ['Camping', 'Caravana', 'Molino'],
      listaTarifas: ['Tarifa Alta', 'Tarifa Baja', 'Tarifa Media']
    }
  },
  computed: {
    ...mapState('login', ['user']),
    ...mapState('clientes', ['listaClientes'])
  },
  components: {
    wgDate: wgDate
  },
  methods: {
    filterClientes (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.listaClientesFilter = this.listaClientes.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    }
  },
  mounted () {
    this.recordToSubmit = Object.assign({}, this.value)
  },
  destroyed () {
    // guardamos valor en tabs por si despus queremos recuperarlo
    this.$emit('input', this.recordToSubmit)
  }
}
</script>
