<template>
  <q-card style="width: 400px;" class="q-pr-xs q-gutter-xs">
    <q-card-section class="bg-primary text-white">
      <div class="text-h6">{{ filterR.id !== undefined ?'Editar Reserva. Id: '+ filterR.id : 'Nueva Reserva' }}</div>
    </q-card-section>

    <q-form @submit="guardarReserva" class="q-gutter-y-xs">
      <q-input outlined readonly autofocus label="Servicio" stack-label v-model="filterR.descServicio" />
      <q-input
        outlined
        :rules="[val => !!val || 'Campo requerido']"
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
        :rules="[val => !!val || 'Campo requerido']"
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
      <q-input outlined clearable autofocus label="Cliente" stack-label v-model="filterR.cliente"  :rules="[val => !!val || 'Campo requerido']"/>
      <q-input outlined clearable autofocus label="Observaciones" stack-label v-model="filterR.observaciones" />
      <q-input outlined clearable label="ID Estancia" stack-label v-model="filterR.idEstancia" />
      <q-card-actions align="right">
        <q-btn v-if="filterR.id !== undefined" flat label="Borrar reserva" color="negative" @click="borrarReserva()"/>
        <q-btn class type="submit" label="Guardar" color="primary"/>
        <q-btn flat label="Cancel" color="primary" @click="$emit('hide')"/><!-- lo captura accionesMain -->
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
        fechaEntrada: '',
        fechaSalida: ''
      },
      val: false,
      noFact: false
    }
  },
  methods: {
    guardarReserva () {
      this.filterR.fechaEntrada = this.filterR.fechaEntrada.substr(0, 10)
      this.filterR.fechaSalida = this.filterR.fechaSalida.substr(0, 10)
      this.$emit('guardarReserva', this.filterR)
    },
    borrarReserva () {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿ Borrar esta reserva ?',
        ok: true,
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.$emit('borrarReserva', this.filterR)
      })
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
