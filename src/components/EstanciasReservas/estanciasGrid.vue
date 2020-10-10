<template>
  <q-item class="row q-ma-xs q-pa-xs">
    <!-- GRID. en row-key ponemos la columna del json que sea la id unica de la fila -->
    <q-table
      class="estanciasGrid-header-table"
      virtual-scroll
      :pagination.sync="pagination"
      :rows-per-page-options="[0]"
      :virtual-scroll-sticky-size-start="48"
      row-key="id"
      :data="value"
      :columns="columns"
      table-style="max-height: 70vh; max-width: 93vw"
    >

      <template v-slot:header="props">
        <!-- CABECERA DE LA TABLA -->
        <q-tr :props="props">
          <q-th> </q-th>
          <q-th
            v-for="col in props.cols"
            :key="col.name"
            :props="props"
          >
            <div :style="col.style">
              {{ col.label }}
            </div>
          </q-th>
          <q-th> </q-th>
        </q-tr>
      </template>

      <template v-slot:body="props">
        <q-tr :props="props" :key="`m_${props.row.id}`" @mouseover="rowId=`m_${props.row.id}`">
            <q-td>
              <div style="max-width: 35px" v-if="rowId === `m_${props.row.id}`">
                <q-icon name="edit" class="text-grey q-pr-md" style="font-size: 1.5rem;" @click="editRecord(props.row, props.row.id)"/>
                <q-icon name="delete" class="text-red" style="font-size: 1.5rem;" @click="deleteRecord(props.row.id)"/>
              </div>
            </q-td>
          <q-td
            v-for="col in props.cols"
            :key="col.name"
            :props="props"
          >
            <div :style="col.style">
                <div v-if="col.name !=='fianzaEntregada'">{{ col.value }}</div>
                <div v-else><q-checkbox v-model="col.value" color="indigo-5" /></div>
            </div>
           </q-td>
           <q-td>
              <q-slide-transition>
                <q-btn icon="cloud_download" style="font-size: 0.8rem;" color="indigo-3"/>
              </q-slide-transition>
            </q-td>
        </q-tr>
      </template>
      <template v-slot:no-data>
        <div class="absolute-bottom text-center q-mb-sm">
          <q-btn
            @click.stop="addRecord"
            round
            dense
            color="indigo-5"
            size="20px"
            icon="add">
            <q-tooltip>Añadir Estancia</q-tooltip>
          </q-btn>
        </div>
        <div>
          Pulse + para añadir estancias
        </div>
      </template>
      <template v-slot:bottom>
        <div class="absolute-bottom text-center q-mb-sm">
          <q-btn
            @click.stop="addRecord"
            round
            dense
            color="indigo-5"
            size="20px"
            icon="add">
            <q-tooltip>Añadir Estancia</q-tooltip>
          </q-btn>
        </div>
        <div>
          {{ `${value.length ? value.length + ' Filas' : 'No hay registros, pulse + para añadir clientes'}` }}
        </div>
      </template>
    </q-table>
  </q-item>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { date } from 'quasar'
export default {
  props: ['value', 'fromEstanciasMain'], // en 'value' tenemos la tabla de datos del grid
  data () {
    return {
      rowId: '',
      visible: false,
      columns: [
        { name: 'id', align: 'left', label: 'ID Estancia', field: 'id', sortable: true },
        { name: 'nombre', align: 'left', label: 'Nombre', field: 'nombre', sortable: true },
        { name: 'fechaEntrada', align: 'left', label: 'Fecha Entrada', field: 'fechaEntrada', sortable: true, format: val => date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY') },
        { name: 'fechaSalida', align: 'left', label: 'Fecha Salida', field: 'fechaSalida', sortable: true, format: val => date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY') },
        { name: 'tipoEstancia', align: 'left', label: 'Tipo Estancia', field: 'tipoEstancia', sortable: true },
        { name: 'nroTicket', align: 'left', label: 'Nº.Ticket', field: 'nroTicket', sortable: true },
        { name: 'tipoTarifa', align: 'left', label: 'Tipo Tarifa', field: 'tipoTarifa', sortable: true },
        { name: 'fianzaEntregada', align: 'left', label: 'Fianza Entregada', field: 'fianzaEntregada', sortable: true }
      ],
      pagination: { rowsPerPage: 0 }
    }
  },
  computed: {
    ...mapState('login', ['user'])
  },
  methods: {
    ...mapActions('tabs', ['addTab']),
    ...mapActions('estancias', ['addEstancia', 'borrarEstancia']),
    addRecord () {
      var record = {
        fechaEntrada: date.formatDate(new Date(), 'YYYY-MM-DD HH:mm:ss')
      }
      this.addEstancia(record)
        .then(response => {
          console.log(response.data.id)
          record.id = response.data.id
          this.editRecord(record, record.id)
        })
        .catch(error => {
          this.$q.dialog({
            message: error.message
          })
        })
    },
    editRecord (rowChanges, id) {
      this.addTab(['estanciasFormMain', 'Estancia-' + rowChanges.id, rowChanges, rowChanges.id])
    },
    deleteRecord (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿ Borrar esta fila ?',
        ok: true,
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.borrarEstancia(id)
          .then(result => {
            this.$q.dialog({
              message: 'Se ha borrado la estancia.'
            })
          })
          .catch(error => {
            this.$q.dialog({
              message: error.message
            })
          })
      })
    },
    mostrarDatosPieTabla () {
      return this.registrosSeleccionados.length + ' Filas'
    }
  }
}
</script>

<style lang="sass">
  .estanciasGrid-header-table
    .q-table__top,
    .q-table__bottom,
    thead tr:first-child th
      /* bg color is important for th; just specify one */
      background-color: $blue-grey-1

    thead tr th
      position: sticky
      z-index: 1
    thead tr:first-child th
      top: 0

    /* this is when the loading indicator appears */
    &.q-table--loading thead tr:last-child th
      /* height of all previous header rows */
      top: 48px
</style>
