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
          <q-input label="Fecha Inicio" class="col-xs-12 col-sm-5" clearable outlined stack-label :value="formatDate(recordToSubmit.fechaInicial)">
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
          <q-input label="Fecha Fin" class="col-xs-12 col-sm-5" clearable outlined stack-label :value="formatDate(recordToSubmit.fechaCierre)">
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
          <q-btn outline class="col-xs-4 col-sm-2" color="primary" label="Calcular" @click="calcular" />
        </div>
        <div class="row q-mt-md q-mb-md">
          <q-input class="col-xs-6 col-sm-4" outlined readonly stack-label v-model="recordToSubmit.facturacionPeriodo" label="Facturación Periodo"/>
          <q-input class="col-xs-6 col-sm-4" outlined readonly stack-label v-model="recordToSubmit.recaudacionCaja" label="Recaudación Caja"/>
          <q-input class="col-xs-12 col-sm-4" outlined readonly stack-label v-model="recordToSubmit.gastosCajaPeriodo" label="Gastos Caja"/>
        </div>
        <div class="row q-mt-xl q-mb-md">
          <q-input class="col-xs-2 col-sm-4" outlined stack-label v-model="recordToSubmit.cajaPendiente" label="Caja a Ingresar"/>
          <q-input class="col-xs-10 col-sm-8" outlined stack-label v-model="recordToSubmit.observaciones" label="Observaciones"/>
        </div>
      </q-card-section>
      <q-card-actions align=right>
        <q-btn type="submit" label="Save" color="primary"/>
        <q-btn @click="$emit('close')" label="Cancel" color="negative"/>
      </q-card-actions>
    </q-form>
  </q-card>
</template>

<script>
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'
export default {
  props: ['value', 'cabecera'], // value es el objeto con los campos de filtro que le pasa accionesMain con v-model
  data () {
    return {
      title: 'Cuadre Caja línea',
      recordToSubmit: { }
    }
  },
  computed: {
    // añadir
  },
  methods: {
    saveForm () {
      this.$emit('saveRecord', this.recordToSubmit) // lo captura estanciasFormLineas
    },
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    },
    calcular () {
      // a implementar
    },
    rellenarDatosServicio () {
      const servicio = this.listaServicios.find(serv => serv.id === this.recordToSubmit.idServicio)
      this.recordToSubmit.tipoIva = servicio.tipoIva
      this.recordToSubmit.Numero = servicio.Numero
      var record = {
        idServicio: servicio.id,
        tipoServ: servicio.tipoServicio,
        tipoTarifa: this.cabecera.tipoTarifa
      }
      // return this.$axios.get('servicios/bd_servicios.php/calcularTarifaServicio', { params: record })
      this.calcularTarifa(record)
        .then(response => {
          this.recordToSubmit.tarifa = response.data[0].tarifa
          this.calcularTotal()
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    calcularDatosLinea () {
      var obj = {
        unidades: parseFloat(this.recordToSubmit.unidades),
        precio: parseFloat(this.recordToSubmit.precio),
        piva: parseFloat(this.recordToSubmit.piva)
      }
      obj.neto = Math.round((obj.unidades * obj.precio - obj.descuento) * 100.0) / 100 // base de iva
      obj.totalIva = Math.round((obj.neto * obj.piva / 100.0) * 100.0) / 100
      obj.totalLinea = Math.round((obj.neto + obj.totalIva) * 100.0) / 100
      Object.assign(this.recordToSubmit, obj)
    }
  },
  components: {
    wgDate: wgDate
  }
}
</script>
