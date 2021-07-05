<template>
  <q-card class="q-pt-none q-pl-xs q-pr-xs">
      <div class="row">
        <q-input class="col-xs-3 col-sm-2" outlined label="Tipo Reg." stack-label v-model="regTipo1.tipoReg" />
        <q-input class="col-xs-9 col-sm-5" autogrow outlined label="Cód. Establecimiento" stack-label v-model="regTipo1.codEstablecimiento" />
        <q-input class="col-xs-12 col-sm-5" autogrow outlined label="Nom. Establecimiento" stack-label v-model="regTipo1.nombreEstablecimiento" />
        <q-input class="col-xs-6 col-sm-4" autogrow outlined label="Fecha Envío" stack-label v-model="regTipo1.fechaEnvio" />
        <q-input class="col-xs-6 col-sm-4" autogrow outlined label="Hora Envío" stack-label v-model="regTipo1.HoraEnvio" />
        <q-input class="col-xs-6 col-sm-4" autogrow outlined label="NumReg Tipo 2" stack-label v-model="regTipo1.NumRegistrosTipo2" />
      </div>
      <div class="row">
        <q-input class="col-xs-3 col-sm-3" autogrow outlined label="Nº Archivo" stack-label v-model="regTipo1.numArchivo" />
        <q-input class="col-xs-9 col-sm-5" autogrow outlined label="Archivo" stack-label v-model="regTipo1.nomArchivo" />
        <q-btn outline class="col-xs-12 col-sm-4" color="primary" label="Generar Archivo" @click="generarArchivo"/>
      </div>
  </q-card>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { date } from 'quasar'

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
  methods: {
    ...mapActions('guardiaC', ['generarArchivoGC']),
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    },
    generarArchivo () {
      // llamada backend
      this.generarArchivoGC()
        .then(response => {
          // console.log('generarArchivoGC desde cabecera con exito, response.data es:', response.data)
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    }
  },
  mounted () {
    if (this.regTipo1.fechaEnvio === undefined || this.regTipo1.fechaEnvio === '') {
      this.regTipo1.fechaEnvio = this.formatDate(Date.now())
    }
    Object.assign(this.regTipo1, this.value)
  },
  destroyed () {
    this.$emit('input', this.regTipo1)
  }
}
</script>
