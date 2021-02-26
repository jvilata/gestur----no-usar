<template>
  <q-card style="width: 400px;" class="q-pr-xs q-gutter-xs">
    <q-card-section class="bg-primary text-white">
      <div class="text-h6">Filtrar por</div>
    </q-card-section>

    <q-form @submit="getRecords" class="q-gutter-y-xs">
      <q-input outlined clearable label="ID Estancia" stack-label v-model="filterR.id" />
      <q-input outlined clearable autofocus label="Nombre" stack-label v-model="filterR.nombre" />
      <q-select
            class="col-xs-6 col-sm-3"
            outlined
            label="Tipo Estancia"
            stack-label
            v-model="filterR.tipoEstancia"
            :options="listaTipoEstancia"
            option-value="codElemento"
            option-label="valor1"
            emit-value
            map-options
          />
      <q-input
        outlined
        clearable
        label="Fecha Entrada"
        stack-label
        :value="formatDate(filterR.fechaEntrada)"
        @input="(val) => filterR.fechaEntrada=val"
      >
        <template v-slot:append>
            <q-icon name="event" class="cursos-pointer">
              <q-popup-proxy ref="qProxy1">
                <wgDate
                  v-model="filterR.fechaEntrada"
                  @input="$refs.qProxy1.hide()"/>
              </q-popup-proxy>
            </q-icon>
        </template>
      </q-input>
      <q-input
        outlined
        clearable
        label="Fecha Salida"
        stack-label
        :value="formatDate(filterR.fechaSalida)"
        @input="(val) => filterR.fechaSalida=val"
      >
        <template v-slot:append>
            <q-icon name="event" class="cursos-pointer">
              <q-popup-proxy ref="qProxy">
                <wgDate
                  v-model="filterR.fechaSalida"
                  @input="$refs.qProxy.hide()"/>
              </q-popup-proxy>
            </q-icon>
        </template>
      </q-input>
      <q-banner dense class="bg-grey-3">
      <template v-slot:avatar>
        <q-icon name="description" color="primary" />
      </template>
      Filtros de Factura
      <q-checkbox v-model="val" @input="rellenarFechas" label="Las del año" />
      <q-input
        outlined
        clearable
        label="Fecha Factura Desde"
        stack-label
        :value="formatDate(filterR.fechaFacturaDesde)"
        @input="(val) => filterR.fechaFacturaDesde=val"
        class="q-mb-sm"
      >
        <template v-slot:append>
            <q-icon name="event" class="cursos-pointer">
              <q-popup-proxy ref="qProxy">
                <wgDate
                  v-model="filterR.fechaFacturaDesde"
                  @input="$refs.qProxy.hide()"/>
              </q-popup-proxy>
            </q-icon>
        </template>
      </q-input>
      <q-input
        outlined
        clearable
        label="Fecha Factura Hasta"
        stack-label
        :value="formatDate(filterR.fechaFacturaHasta)"
        @input="(val) => filterR.fechaFacturaHasta=val"
      >
        <template v-slot:append>
            <q-icon name="event" class="cursos-pointer">
              <q-popup-proxy ref="qProxy">
                <wgDate
                  v-model="filterR.fechaFacturaHasta"
                  @input="$refs.qProxy.hide()"/>
              </q-popup-proxy>
            </q-icon>
        </template>
      </q-input>
    </q-banner>
      <q-card-actions align="right">
        <q-btn flat type="submit" label="Buscar" color="primary"/>
        <q-btn flat label="Cancel" color="primary" @click="$emit('hide')"/><!-- lo captura accionesMain -->
      </q-card-actions>
  </q-form>
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
      filterR: {},
      val: false
    }
  },
  computed: {
    ...mapState('tablasAux', ['listaTipoEstancia'])
  },
  methods: {
    rellenarFechas () {
      this.filterR.fechaFacturaDesde = date.formatDate(new Date(), 'YYYY-01-01')
      this.filterR.fechaFacturaHasta = date.formatDate(new Date(), 'YYYY-MM-DD')
    },
    getRecords () {
      this.$emit('getRecords', this.filterR)
    },
    formatDate (pdate) {
      return date.formatDate(pdate, 'DD-MM-YYYY')
    }
  },
  components: {
    wgDate: wgDate
  },
  mounted () {
    this.filterR = Object.assign({}, this.value) // asignamos valor del parametro por si viene de otro tab
  }
}
</script>
