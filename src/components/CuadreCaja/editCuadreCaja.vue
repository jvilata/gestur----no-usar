<template>
  <q-card style="max-width: 800px;" class="q-pr-xs q-gutter-xs">
    <q-form @submit="saveForm" @keyup.esc="$emit('close')">
      <q-card-section class="row bg-primary text-white">
        <div class="text-h6">{{title}}</div>
        <q-space />
        <q-btn @click="$emit('close')" flat round dense icon="close"/>
      </q-card-section>
      <q-card-section>
        <div class="row">
          <q-input label="Fecha Inicio" class="col-xs-12 col-sm-5" clearable outlined stack-label :value="formatDate(recordToSubmit.fechaInicial)"  @input="(val) => recordToSubmit.fechaInicial=val">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaInicial">
                  <wgDate
                      @input="$refs.fechaInicial.hide()"
                      v-model="recordToSubmit.fechaInicial" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
          <q-input label="Fecha Fin" class="col-xs-12 col-sm-5" clearable outlined stack-label :value="formatDate(recordToSubmit.fechaCierre)"  @input="(val) => recordToSubmit.fechaCierre=val">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaCierre">
                  <wgDate
                      @input="$refs.fechaCierre.hide()"
                      v-model="recordToSubmit.fechaCierre" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
          <q-btn outline class="col-xs-4 col-sm-2" color="primary" label="Calcular" @click="calcular" :disabled="recordToSubmit.fechaInicial == null || recordToSubmit.fechaCierre == null ? !disabledCalc : disabledCalc" />
        </div>
        <div class="row q-mt-md q-mb-md">
          <q-input class="col-xs-6 col-sm-3" outlined readonly stack-label v-model="recordToSubmit.facturacionPeriodo" label="Facturación Periodo"/>
          <q-input class="col-xs-6 col-sm-3" outlined readonly stack-label v-model="recordToSubmit.recaudacionCaja" label="Recaudación Caja"/>
          <q-input class="col-xs-6 col-sm-3" outlined readonly stack-label v-model="recordToSubmit.gastosCajaPeriodo" label="Gastos Caja"/>
          <q-input class="col-xs-6 col-sm-3" outlined readonly stack-label :value="recordToSubmit.recaudacionCaja-recordToSubmit.gastosCajaPeriodo" label="Diferencia"/>
        </div>
        <div class="row q-mt-xl q-mb-md">
          <q-input class="col-xs-2 col-sm-4" outlined stack-label v-model="recordToSubmit.cajaPendiente" label="Caja a Ingresar"/>
          <q-input class="col-xs-10 col-sm-8" outlined stack-label v-model="recordToSubmit.observaciones" label="Observaciones"/>
        </div>
      </q-card-section>
      <q-card-actions align=right>
        <q-btn type="submit" label="Guardar" color="primary"/>
        <q-btn @click="$emit('close')" label="Cancelar" color="negative"/>
      </q-card-actions>
    </q-form>
  </q-card>
</template>

<script>
import { date } from 'quasar'
import { mapActions } from 'vuex'
import wgDate from 'components/General/wgDate.vue'
export default {
  props: ['value', 'cabecera'], // value es el objeto con los campos de filtro que le pasa accionesMain con v-model
  data () {
    return {
      title: 'Cuadre Caja línea',
      recordToSubmit: {
        fechaInicial: '',
        fechaCierre: '',
        cajaPendiente: '',
        facturacionPeriodo: '',
        recaudacionCaja: '',
        gastosCajaPeriodo: '',
        observaciones: ''
      },
      disabledCalc: false,
      refreshKey: 0
    }
  },
  mounted () {
    Object.assign(this.recordToSubmit, this.value)
  },
  methods: {
    ...mapActions('cuadrecaja', ['calcularCierre']),
    saveForm () {
      this.$emit('saveRecord', this.recordToSubmit) // lo captura estanciasFormLineas
    },
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    },
    calcular () {
      this.calcularCierre(this.recordToSubmit)
        .then(response => {
          this.recordToSubmit.facturacionPeriodo = response.data[0].totFactura
          this.recordToSubmit.recaudacionCaja = response.data[0].totalCaja
          this.recordToSubmit.gastosCajaPeriodo = response.data[0].gastosCaja
          this.refreshKey++
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    }
  },
  components: {
    wgDate: wgDate
  }
}
</script>
