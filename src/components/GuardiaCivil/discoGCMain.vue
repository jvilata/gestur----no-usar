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
            @click="confirmarCierre"
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
          <q-btn outline class="col-xs-12 col-sm-4" color="primary" label="Generar" @click="generarGC"/>
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
        <guardiaCivilFormCabecera :value="regTipo1" :key="refresh"/>
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
// import { openBlobFile } from 'components/General/cordova.js'
// import { openURL } from 'quasar'
import wgDate from 'components/General/wgDate.vue'

export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      recordToSubmit: {},
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
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    generarGC () {
      // a implementar
      this.generado = true
      this.generarGC()
        .then(response => {
          // console.log('generarGC desde MAIN con exito, response.data es:', response.data)
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    rellenarDatosFact () {
      console.log('metodo por implementar')
    },
    cambiaDatos (record) {
      // ??
    },
    updateRecord () {
      // ?
    },
    calculaTotalesEst () {
      // a impleentar
    },
    confirmarCierre () {
      if (!this.generado) {
        this.$q.dialog({
          title: 'Aviso',
          message: '¿Desea salir sin generar el documento?',
          ok: true,
          cancel: true,
          persistent: true
        }).onOk(() => {
          // generar --> LLAMADA AL BACKEND
          this.$emit('close')
        }).onCancel(() => {
          this.$emit('close')
        })
      } else { this.$emit('close') }
    }
  },
  mounted () {
    this.getRecordsTipo1()
    this.getRecordsTipo2()
  },
  components: {
    wgDate: wgDate,
    guardiaCivilFormCabecera: require('components/GuardiaCivil/guardiaCivilFormCabecera.vue').default,
    guardiaCivilFormLineas: require('components/GuardiaCivil/guardiaCivilFormLineas.vue').default
  }
}
</script>
