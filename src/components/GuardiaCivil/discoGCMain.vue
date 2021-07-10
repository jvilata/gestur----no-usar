<!-- componente principal de definicion de formularios. Se apoya en otros 2 componentes: Filter y Grid -->
  <template>
    <div style="height: calc(100vh - 105px)">
      <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-md q-pa-xs bg-blue-grey-1 text-grey-8">
        <!-- cabecera de formulario. Botón de busqueda y cierre de tab -->
        <q-item-section avatar>
          <q-icon name="shield" />
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-h6">
            {{ title }}
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
        <div class="row q-ma-md">
        <q-input
            label="Fecha Inicial"
            class="col-xs-6 col-sm-4"
            clearable
            outlined
            stack-label
            :value="formatDate(recordToSubmit.FechaInicialEntrada)"
            @input="(val) => recordToSubmit.FechaInicialEntrada=val"
            >
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="FechaInicialEntrada">
                  <wgDate
                      @input="$refs.FechaInicialEntrada.hide()"
                      v-model="recordToSubmit.FechaInicialEntrada" />
              </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-input
            label="Fecha Final"
            class="col-xs-6 col-sm-4"
            clearable
            outlined
            stack-label
            :value="formatDate(recordToSubmit.FechaFinalEntrada)"
            @input="(val) => recordToSubmit.FechaFinalEntrada=val"
            >
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="FechaFinalEntrada">
                  <wgDate
                      @input="$refs.FechaFinalEntrada.hide()"
                      v-model="recordToSubmit.FechaFinalEntrada" />
              </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-btn outline class="col-xs-12 col-sm-4" color="primary" label="Generar Lista" @click="generarListaGC"/>
      </div>
    <q-card flat class="q-pb-xl">
      <q-list bordered>
        <q-expansion-item
          class="q-pt-none q-pl-xs q-pr-xs"
          group="somegroup"
          dense
          label="Cabecera Disco"
          default-opened
          header-class="bg-brown-1 text-grey-8"
        >
        <guardiaCivilFormCabecera :value="regTipo1" :key="refresh" />
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
          <guardiaCivilFormLineas :key="refresh" :value="listaRegTipo2"/>
        </q-expansion-item>
      </q-list>
    </q-card>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'

export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      recordToSubmit: {
        FechaInicialEntrada: '',
        FechaFinalEntrada: ''
      },
      generado: false,
      title: 'DISCO GUARDIA CIVIL',
      refresh: 0,
      hasChanges: false,
      colorBotonSave: 'primary',
      primeraVez: true,
      listaOpciones: [
        { name: 'generar', title: 'Generar Archivo GC', icon: 'print', function: 'generarGC' }
      ],
      regTipo1: {},
      listaRegTipo2: []
    }
  },
  computed: {
    ...mapState('login', ['user']) // importo state.user desde store-login
  },
  methods: {
    ...mapActions('guardiaC', ['findTipo1', 'findTipo2', 'generarGC']),
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    },
    getRecordsTipo1 () {
      // cabecera
      this.findTipo1({})
        .then(response => {
          Object.assign(this.regTipo1, response.data[0])
          this.refresh++
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    getRecordsTipo2 () {
      // lineas
      this.findTipo2([])
        .then(response => {
          Object.assign(this.listaRegTipo2, response.data)
          this.refresh++
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    generarListaGC () {
      // a implementar
      this.generado = true
      this.generarGC(this.recordToSubmit)
        .then(response => {
          this.getRecordsTipo1()
          this.getRecordsTipo2()
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    }
  },
  mounted () {
    this.getRecordsTipo1()
    this.getRecordsTipo2()
    if (this.recordToSubmit.FechaInicialEntrada === '') {
      this.recordToSubmit.FechaInicialEntrada = date.formatDate(new Date(), 'YYYY-MM-DD 00:00:00')
    }
    if (this.recordToSubmit.FechaFinalEntrada === '') {
      this.recordToSubmit.FechaFinalEntrada = date.formatDate(new Date(), 'YYYY-MM-DD 00:00:00')
    }
  },
  destroyed () {
    this.$emit('changeTab', { idTab: this.value.idTab, filterRecord: Object.assign({}, this.filterRecord), registrosSeleccionados: Object.assign({}, this.registrosSeleccionados) })
  },
  components: {
    wgDate: wgDate,
    guardiaCivilFormCabecera: require('components/GuardiaCivil/guardiaCivilFormCabecera.vue').default,
    guardiaCivilFormLineas: require('components/GuardiaCivil/guardiaCivilFormLineas.vue').default
  }
}
</script>
