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
          <q-input label="Fecha Inicio" class="col-xs-12 col-sm-5" clearable outlined stack-label :value="formatDate(recordToSubmit.fechaInicio)" @blur="calcularFechaFin">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaIni">
                  <wgDate
                      @input="$refs.fechaIni.hide()"
                      v-model="recordToSubmit.fechaInicio" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
          <q-input label="Fecha Fin" class="col-xs-12 col-sm-5" clearable outlined stack-label :value="formatDate(recordToSubmit.fechaFin)" @blur="calcularNoches(recordToSubmit.fechaFin, recordToSubmit.fechaInicio)">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaFin">
                  <wgDate
                      @input="$refs.fechaFin.hide()"
                      v-model="recordToSubmit.fechaFin" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
          <q-input class="col-xs-4 col-sm-2" outlined label="Noches" stack-label v-model="recordToSubmit.noches" />
        </div>
        <div class="row q-mt-xl q-mb-md">
          <q-input class="col-xs-6 col-sm-4" outlined readonly stack-label v-model="totalBruto" label="Total Bruto"/>
          <q-input class="col-xs-6 col-sm-4" outlined readonly stack-label v-model="totalNeto" label="Total Neto"/>
          <q-input class="col-xs-12 col-sm-4" outlined readonly stack-label v-model="recordToSubmit.total" label="Total"/>
        </div>
        <div class="row">
          <q-select
            class="col-xs-4 col-sm-3"
            label="Dudoso Cobro"
            stack-label
            outlined
            clearable
            v-model="recordToSubmit.dudoso"
            :options="listaSINO"
            option-value="id"
            option-label="desc"
            emit-value
            map-options
          />
          <!-- <q-input class="col-xs-3 col-sm-3" outlined stack-label v-model="recordToSubmit.dudoso" label="Dudoso"/> -->
          <q-input class="col-xs-8 col-sm-9" outlined stack-label v-model="recordToSubmit.comentarios" label="Comentarios"/>
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
import { mapState, mapActions } from 'vuex'
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'
export default {
  props: ['value', 'cabecera'], // value es el objeto con los campos de filtro que le pasa accionesMain con v-model
  data () {
    return {
      title: 'Cuadre Caja línea',
      totalBruto: 0,
      totalNeto: 0,
      recordToSubmit: { },
      listaActivosFilter: []
    }
  },
  computed: {
    ...mapState('tablasAux', ['listaTipoServ', 'listaSINO']),
    ...mapState('servicios', ['listaServicios'])
  },
  methods: {
    ...mapActions('servicios', ['loadListaServicios', 'calcularTarifa']),
    saveForm () {
      this.$emit('saveRecord', this.recordToSubmit) // lo captura estanciasFormLineas
    },
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    },
    calcularFechaFin () {
      if (this.recordToSubmit.fechaInicio !== '') {
        const dateFin = date.addToDate(this.recordToSubmit.fechaInicio, { days: 1 })
        this.recordToSubmit.fechaFin = date.formatDate(dateFin, 'YYYY-MM-DD')
        this.calcularNoches(this.recordToSubmit.fechaFin, this.recordToSubmit.fechaInicio)
      }
    },
    calcularNoches (dateFin, dateIni) {
      const diff = date.getDateDiff(dateFin, dateIni, 'days')
      this.recordToSubmit.noches = diff
      this.calcularTotal()
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
    calcularTotal () {
      const tarifa = Math.round(this.recordToSubmit.tarifa / (1 + (this.recordToSubmit.tipoIva / 100))) // tarifa sin IVA
      this.totalBruto = Math.round(this.recordToSubmit.cantidad * this.recordToSubmit.noches * tarifa)
      this.totalNeto = Math.round(this.totalBruto * (1 - this.recordToSubmit.dto / 100))
      this.recordToSubmit.total = Math.round(this.totalNeto * (1 + this.recordToSubmit.tipoIva / 100))
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
  },
  mounted () {
    if (this.listaServicios.length === 0) {
      this.loadListaServicios()
    }
    this.recordToSubmit = Object.assign({}, this.value) // asignamos valor del parametro por si viene de otro tab
    this.calcularTotal()
  }
}
</script>
