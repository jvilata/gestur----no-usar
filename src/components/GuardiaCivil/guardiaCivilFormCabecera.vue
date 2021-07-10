<template>
  <q-card class="q-pt-none q-pl-xs q-pr-xs">
      <div class="row">
        <!--q-input class="col-xs-3 col-sm-2" outlined label="Tipo Reg." stack-label v-model="regTipo1.tipoReg" /-->
        <q-input class="col-xs-4 col-sm-3" autogrow outlined label="Cód. Establecimiento" stack-label v-model="regTipo1.codEstablecimiento" />
        <q-input class="col-xs-8 col-sm-5" autogrow outlined label="Nom. Establecimiento" stack-label v-model="regTipo1.nombreEstablecimiento" />
        <q-input
            label="Fecha Envío"
            class="col-xs-6 col-sm-4"
            clearable
            outlined
            stack-label
            :value="formatDate(regTipo1.fechaEnvio)"
            @input="(val) => regTipo1.fechaEnvio=val"
            >
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaEnvio">
                  <wgDate
                      @input="$refs.fechaEnvio.hide()"
                      v-model="regTipo1.fechaEnvio"
                      :showTime="true" />
              </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
        <!-- <q-input class="col-xs-6 col-sm-4" autogrow outlined label="Fecha Envío" stack-label v-model="regTipo1.fechaEnvio" /> -->
        <!--q-input class="col-xs-6 col-sm-4" autogrow outlined label="Hora Envío" stack-label v-model="regTipo1.HoraEnvio" /-->

        <q-input class="col-xs-6 col-sm-2" autogrow outlined label="NumReg Tipo 2" stack-label v-model="regTipo1.NumRegistrosTipo2" />
        <q-input class="col-xs-3 col-sm-2" autogrow outlined label="Nº Archivo" stack-label v-model="regTipo1.numArchivo" />
        <q-input class="col-xs-9 col-sm-4" autogrow outlined label="Archivo" stack-label v-model="regTipo1.nomArchivo" />
        <q-btn outline class="col-xs-12 col-sm-4" color="primary" label="Generar Archivo" @click="generarArchivo"/>
      </div>
  </q-card>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'
import { openBlobFile } from 'components/General/cordova.js'

export default {
  props: ['value'], // value es el objeto con los campos de filtro que le pasa accionesMain con v-model
  data () {
    return {
      regTipo1: {
        fechaEnvio: '',
        HoraEnvio: '',
        numArchivo: '',
        nomArchivo: '',
        NumRegistrosTipo2: '',
        tipoReg: '',
        codEstablecimiento: '',
        nombreEstablecimiento: ''
      }
    }
  },
  computed: {
    ...mapState('login', ['user'])
  },
  components: {
    wgDate: wgDate
  },
  methods: {
    ...mapActions('guardiaC', ['generarArchivoGC']),
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY HH:mm')
    },
    generarArchivo () {
      var paramRecord = {
      }
      var formData = new FormData()
      for (var key in paramRecord) {
        formData.append(key, paramRecord[key])
      }

      var nomFile = this.regTipo1.nomArchivo
      this.$axios.post('guardiac/bd_guardiac.php/generarArchivoGC', formData, { responseType: 'blob' })
        .then(function (response) {
          if (window.cordova === undefined) { // desktop
            const url = window.URL.createObjectURL(new Blob([response.data], { type: response.data.type }))
            const link = document.createElement('a')
            link.href = url
            link.download = nomFile
            // link.target = '_blank'
            document.body.appendChild(link)
            // window.open('', 'view') // abre nueva ventana para que no sustituya a la actual
            link.click()
            document.body.removeChild(link)
          } else { // estamos en un disp movil            console.log('hola3')
            const blobPdf = response.data // new Blob([response.data], { type: response.data.type })
            openBlobFile(nomFile, blobPdf, response.data.type)
          }
        }).catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    }
  },
  mounted () {
    Object.assign(this.regTipo1, this.value)
  },
  destroyed () {
    this.$emit('input', this.regTipo1)
  }
}
</script>
