<template>
  <q-card style="width: 400px;" class="q-pr-xs q-gutter-xs">
    <q-card-section class="bg-primary text-white">
      <div class="text-h6">Filtrar por</div>
    </q-card-section>

    <q-form @submit="getRecords" class="q-gutter-y-xs">
      <q-input outlined clearable label="Desde - Fecha" stack-label :value="formatDate(filterR.fechaInicio)" @input="val => filterR.fechaInicio=val" >
        <template v-slot:append>
            <q-icon name="event" class="cursos-pointer">
              <q-popup-proxy ref="qProxy1">
                <wgDate
                  v-model="filterR.fechaInicio"
                  @input="$refs.qProxy1.hide()"/>
              </q-popup-proxy>
            </q-icon>
        </template>
      </q-input>
      <q-input outlined clearable label="Hasta - Fecha" stack-label :value="formatDate(filterR.fechaFin)" @input="val => filterR.fechaFin=val" >
        <template v-slot:append>
            <q-icon name="event" class="cursos-pointer">
              <q-popup-proxy ref="qProxy1">
                <wgDate
                  v-model="filterR.fechaFin"
                  @input="$refs.qProxy1.hide()"/>
              </q-popup-proxy>
            </q-icon>
        </template>
      </q-input>
      <q-input outlined clearable label="Descripción" stack-label v-model="filterR.descripcion" />
      <q-card-actions align="right">
        <q-btn  flat type="submit" label="Buscar" color="primary"/>
        <q-btn  flat label="Cancel" color="primary" @click="$emit('hide')"/><!-- lo captura accionesMain -->
      </q-card-actions>
  </q-form>
  </q-card>
</template>

<script>
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'
export default {
  props: ['value'], // value es el objeto con los campos de filtro que le pasa accionesMain con v-model
  data () {
    return {
      filterR: {
        fechaInicio: '',
        fechaFin: '',
        descripcion: ''
      }
    }
  },
  methods: {
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
