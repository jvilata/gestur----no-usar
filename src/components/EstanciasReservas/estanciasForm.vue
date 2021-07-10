<!-- componente principal de definicion de formularios. Se apoya en otros 2 componentes: Filter y Grid -->
  <template>
    <div style="height: calc(100vh - 105px)">
      <q-card flat>
      <q-card-section   class="q-pa-xs">
            <q-item class="q-pa-xs bg-blue-grey-1 text-grey-8">
              <!-- cabecera de formulario. Botón de busqueda y cierre de tab -->
              <q-item-section avatar>
                <div class="row">
                  <q-btn icon="save"  class="q-ma-xs" :color="colorBotonSave" dense @click="saveChanges"/>
                  <q-btn icon="more_vert"  class="q-ma-xs" color="primary" dense>
                    <q-menu ref="menu1">
                      <q-list dense>
                        <q-item
                          v-for="(opcion, index) in listaOpciones"
                          :key="index"
                          clickable
                          v-close-popup
                          @click.native="ejecutarOpcion(opcion)"
                          >
                          <q-item-section avatar>
                            <q-icon :name="opcion.icon" />
                          </q-item-section>
                          <q-item-section>{{opcion.title}}</q-item-section>
                        </q-item>
                      </q-list>
                    </q-menu>
                  </q-btn>
                </div>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-h6">
                  {{ title }} / {{ recordToSubmit.nombre }}
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
      </q-card-section>
    </q-card>
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
        <estanciasFormCabecera :value="recordToSubmit" :key="refresh" @hasChanges="value=>cambiaDatos(value)" @calculaTotalesEst="calculaTotalesEst"/>
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
          <estanciasFormLineas :key="refresh" :value="recordToSubmit" @calculaTotalesEst="calculaTotalesEst"/>
        </q-expansion-item>
        <q-expansion-item
          class="q-pt-xs q-pl-xs q-pr-xs"
          group="somegroup1"
          dense
          label="Viajeros"
          default-opened
          header-class="bg-brown-1 text-grey-8"
        >
          <estanciasFormViajeros :key="refresh" :value="recordToSubmit"/>
        </q-expansion-item>
      </q-list>
    </q-card>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
// import { openBlobFile } from 'components/General/cordova.js'
// import { openURL } from 'quasar'

export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      recordToSubmit: {},
      title: 'Estancias',
      refresh: 0,
      hasChanges: false,
      colorBotonSave: 'primary',
      primeraVez: true,
      listaOpciones: [
        { name: 'imprimir', title: 'Imprimir', icon: 'print', function: 'imprimirPreview' }
      ]
    }
  },
  computed: {
    ...mapState('login', ['user']) // importo state.user desde store-login
  },
  methods: {
    ...mapActions('estancias', ['addEstancia', 'findEstancia']),
    cambiaDatos (record) {
      this.hasChanges = record.hasChanges
      this.colorBotonSave = record.colorBotonSave
      Object.assign(this.recordToSubmit, record.record)
    },
    getRecord () {
      var record = {
        id: this.value.id
      }
      this.findEstancia(record)
        .then(response => {
          Object.assign(this.recordToSubmit, response.data[0])
          setTimeout(() => { this.primeraVez = false; this.colorBotonSave = 'primary'; this.hasChanges = false }, 100) // dejo pasar un poco porque en el render se modifica el registro
          this.refresh++ // refresca datos cabecera
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    // funciones de menu de estancias
    ejecutarOpcion (opcion) {
      this[opcion.function](this.value)
      this.$refs.menu1.hide()
    },
    saveChanges () {
      if (this.hasChanges) {
        this.updateRecord()
      }
    },
    updateRecord () {
      this.addEstancia(this.recordToSubmit)
        .then(response => {
          this.colorBotonSave = 'primary'
          this.hasChanges = false
          this.$q.notify('Se ha actualizado registro')
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    calculaTotalesEst (totales) { // cuando se guardan cambios en una linea de detalle
      if (this.recordToSubmit.porRetencion === '' || this.recordToSubmit.porRetencion === null) this.recordToSubmit.porRetencion = '0'
      if (!totales.porRetencion) totales.porRetencion = this.recordToSubmit.porRetencion
      totales.base = Math.round(parseFloat(totales.base) * 100.0) / 100
      totales.totalIva = Math.round(parseFloat(totales.totalIva) * 100.0) / 100
      totales.Retencion = Math.round(parseFloat(totales.base) * (parseFloat(totales.porRetencion) / 100.0) * 100.0) / 100
      totales.totalEstancia = Math.round((parseFloat(totales.base) + parseFloat(totales.totalIva) - parseFloat(totales.Retencion)) * 100.0) / 100
      // Object.assign(this.valueTotales, totales) // para que no se pierdan los valores en el saveChanges
      Object.assign(this.recordToSubmit, totales)
      this.refresh++ // refresca datos cabecera
    },
    confirmarCierre () {
      if (this.hasChanges) {
        this.$q.dialog({
          title: 'Aviso',
          message: '¿Desea guardar los cambios?',
          ok: true,
          cancel: true,
          persistent: true
        }).onOk(() => {
          this.updateRecord()
          this.$emit('close')
        }).onCancel(() => {
          this.$emit('close')
        })
      } else { this.$emit('close') }
    },
    imprimirPreview (selected) {
      this.imprimir(selected, 0)
    },
    imprimir (selected, aDisco) { // aDisco: 0 -> preview; aDisco: 1 -> imprime en onedrive
      return new Promise((resolve, reject) => {
        var paramRecord = {
          id: selected.id,
          aDisco: aDisco
        }
        var formData = new FormData()
        for (var key in paramRecord) {
          formData.append(key, paramRecord[key])
        }
        this.$axios.post('estancias/pdf_invoice.php/', formData, { responseType: (aDisco === 0 ? 'blob' : '') })
          .then(function (response) {
            if (aDisco === 2) resolve(response)
            else {
              if (window.cordova === undefined) { // desktop
                const url = window.URL.createObjectURL(new Blob([response.data], { type: response.data.type }))
                const link = document.createElement('a')
                link.href = url
                link.target = '_blank'
                document.body.appendChild(link)
                // window.open('', 'view') // abre nueva ventana para que no sustituya a la actual
                link.click()
              } else { // estamos en un disp movil
                // const blobPdf = response.data // new Blob([response.data], { type: response.data.type })
                // openBlobFile(selected.archivoDrive, blobPdf, response.data.type)
              }
              resolve('ok')
            }
          }).catch(error => {
            reject(error.message)
          })
      })
    }
  },
  mounted () {
    this.getRecord()
  },
  components: {
    estanciasFormCabecera: require('components/EstanciasReservas/estanciasFormCabecera.vue').default,
    estanciasFormLineas: require('components/EstanciasReservas/estanciasFormLineas.vue').default,
    estanciasFormViajeros: require('components/EstanciasReservas/estanciasFormViajeros.vue').default
  }
}
</script>
