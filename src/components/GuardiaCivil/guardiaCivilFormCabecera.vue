<template>
  <q-card class="q-pt-none q-pl-xs q-pr-xs">
      <div class="row">
        <q-input class="col-xs-3 col-sm-2" readonly outlined label="Tipo Reg." stack-label v-model="recordToSubmit.tipoReg" />
        <q-input class="col-xs-9 col-sm-5" autogrow outlined label="Cód. Establecimiento" stack-label v-model="recordToSubmit.cod" />
        <q-input class="col-xs-12 col-sm-5" autogrow outlined label="Nom. Establecimiento" stack-label v-model="recordToSubmit.nombreEstablecimiento" />
        <q-input
        label="Fecha Envío"
        class="col-xs-6 col-sm-2"
        clearable
        outlined
        stack-label
        :value="formatDate(recordToSubmit.fechaEnvío)"
        @input="(val) => recordToSubmit.fechaEnvío=val"
        >
        <template v-slot:append>
            <q-icon name="event" class="cursor-pointer">
            <q-popup-proxy ref="fechaEnvío">
                <wgDate
                    @input="$refs.fechaEnvío.hide()"
                    v-model="recordToSubmit.fechaEnvío" />
            </q-popup-proxy>
            </q-icon>
        </template>
        </q-input>
        <q-input class="col-xs-6 col-sm-2" autogrow outlined label="NumReg Tipo 2" stack-label v-model="recordToSubmit.numReg" />
        <q-input class="col-xs-3 col-sm-3" autogrow outlined label="Nº Archivo" stack-label v-model="recordToSubmit.numArchivo" />
        <q-input class="col-xs-9 col-sm-5" autogrow outlined label="Archivo" stack-label v-model="recordToSubmit.archivo" />
      </div>
      <div class="row q-mt-sm">
        <q-btn outline class="col-xs-12 col-sm-2" color="primary" label="Generar Lista" @click="rellenarDatosFact" />
        <q-input
            label="Fecha Inicial Entrada"
            class="col-xs-6 col-sm-2"
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
            label="Fecha Final Entrada"
            class="col-xs-6 col-sm-2"
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
      recordToSubmit: {}
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
  destroyed () {
    // guardamos valor en tabs por si despus queremos recuperarlo
    this.$emit('input', this.recordToSubmit)
  }
}
</script>
