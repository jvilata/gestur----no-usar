<template>
  <q-card class="q-pt-none q-pl-xs q-pr-xs">
      <div class="row">
        <q-input class="col-xs-3 col-sm-2" outlined label="Tipo Reg." stack-label v-model="recordToSubmit.tipoReg" />
        <q-input class="col-xs-9 col-sm-5" autogrow outlined label="Cód. Establecimiento" stack-label v-model="recordToSubmit.cod" />
        <q-input class="col-xs-12 col-sm-5" autogrow outlined label="Nom. Establecimiento" stack-label v-model="recordToSubmit.nombreEstablecimiento" />
        <q-input
        label="Fecha Envío"
        class="col-xs-6 col-sm-2"
        clearable
        outlined
        stack-label
        :value="formatDate(recordToSubmit.fechaEnvio)"
        @input="(val) => recordToSubmit.fechaEnvio=val"
        >
        <template v-slot:append>
            <q-icon name="event" class="cursor-pointer">
            <q-popup-proxy ref="fechaEnvio">
                <wgDate
                    @input="$refs.fechaEnvio.hide()"
                    v-model="recordToSubmit.fechaEnvio" />
            </q-popup-proxy>
            </q-icon>
        </template>
        </q-input>
        <q-input class="col-xs-6 col-sm-2" autogrow outlined label="NumReg Tipo 2" stack-label v-model="recordToSubmit.numReg" />
        <q-input class="col-xs-3 col-sm-3" autogrow outlined label="Nº Archivo" stack-label v-model="recordToSubmit.numArchivo" />
        <q-input class="col-xs-9 col-sm-5" autogrow outlined label="Archivo" stack-label v-model="recordToSubmit.archivo" />
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
      recordToSubmit: {
        fechaEnvio: ''
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
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    },
    rellenarDatosFact () {
      console.log('metodo por implementar')
    }
  },
  mounted () {
    if (this.recordToSubmit.fechaEnvio === undefined || this.recordToSubmit.fechaEnvio === '') {
      this.recordToSubmit.fechaEnvio = this.formatDate(Date.now())
    }
  },
  destroyed () {
    // guardamos valor en tabs por si despus queremos recuperarlo
    this.$emit('input', this.recordToSubmit)
  }
}
</script>
