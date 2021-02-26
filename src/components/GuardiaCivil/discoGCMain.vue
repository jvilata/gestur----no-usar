<!-- componente principal de definicion de formularios. Se apoya en otros 2 componentes: Filter y Grid -->
  <template>
    <div style="height: calc(100vh - 105px)">
      <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-md q-pa-xs bg-blue-grey-1 text-grey-8">
        <!-- cabecera de formulario. Botón de busqueda y cierre de tab -->
        <q-item-section avatar>
          <q-icon name="shield" />
        </q-item-section>
        <q-item-section avatar>
          <div class="row">
            <q-btn label="GENERAR" style="font-size: 0.8rem;" color="brown-3" text-color="grey-9"/>
          </div>
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
        <guardiaCivilFormCabecera :value="recordToSubmit" :key="refresh" @hasChanges="value=>cambiaDatos(value)" @calculaTotalesEst="calculaTotalesEst"/>
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
          <guardiaCivilFormLineas :key="refresh" :value="recordToSubmit" @calculaTotalesEst="calculaTotalesEst"/>
        </q-expansion-item>
      </q-list>
    </q-card>
    </div>
</template>

<script>
import { mapState } from 'vuex'
// import { openBlobFile } from 'components/General/cordova.js'
// import { openURL } from 'quasar'

export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      generado: false,
      recordToSubmit: {},
      title: 'DISCO GUARDIA CIVIL',
      refresh: 0,
      hasChanges: false,
      colorBotonSave: 'primary',
      primeraVez: true,
      listaOpciones: [
        { name: 'generar', title: 'Generar Archivo GC', icon: 'print', function: 'generarGC' }
      ]
    }
  },
  computed: {
    ...mapState('login', ['user']) // importo state.user desde store-login
  },
  methods: {
    generarGC () {
      // a implementar
      this.generado = true
    },
    cambiaDatos (record) {
      // ??
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
          // generar
          this.$emit('close')
        }).onCancel(() => {
          this.$emit('close')
        })
      } else { this.$emit('close') }
    }
  },
  mounted () {
    this.getRecord()
  },
  components: {
    guardiaCivilFormCabecera: require('components/GuardiaCivil/guardiaCivilFormCabecera.vue').default,
    guardiaCivilFormLineas: require('components/GuardiaCivil/guardiaCivilFormLineas.vue').default
  }
}
</script>
