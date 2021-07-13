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
      :data="registrosSeleccionados"
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
                <div >{{ col.value }}</div>
            </div>
           </q-td>
        </q-tr>
      </template>
      <template v-slot:bottom-row>
        <!-- BOTTOM-ROW DE LA TABLA -->
        <q-tr >
          <q-th>
             <div style="max-width: 35px"></div>
          </q-th>
          <q-th
            v-for="col in columns"
            :key="col.name"
            :align="col.align"
          >
            <div v-if="['base'].includes(col.name)">{{ registrosSeleccionados.reduce((a, b) => a + (parseFloat(b.base)), 0) }}</div>
            <div v-if="['total'].includes(col.name)">{{ registrosSeleccionados.reduce((a, b) => a + (parseFloat(b.totalEstancia)), 0) }}</div>
          </q-th>
        </q-tr>
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
          {{ registrosSeleccionados.length }} Filas
        </div>
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
      registrosSeleccionados: [],
      visible: false,
      columns: [
        { name: 'id', align: 'left', label: 'ID Estancia', field: 'id', sortable: true },
        { name: 'nombre', align: 'left', label: 'Nombre Completo', field: 'nombre', sortable: true },
        { name: 'fechaEntrada', align: 'left', label: 'Fecha Entrada', field: 'fechaEntrada', sortable: true, format: val => { var res = date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY'); return (res === '31-12-1899' ? '' : res) } },
        { name: 'fechaSalida', align: 'left', label: 'Fecha Salida', field: 'fechaSalida', sortable: true, format: val => { var res = date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY'); return (res === '31-12-1899' ? '' : res) } },
        { name: 'tipoEstancia', align: 'left', label: 'Tipo Estancia', field: 'tipoEstancia', sortable: true, format: val => { var res = this.listaTipoEstancia.find(row => row.codElemento === val); return (res === undefined ? '' : res.valor1) } },
        { name: 'tipoTarifa', align: 'left', label: 'Tipo Tarifa', field: 'tipoTarifa', sortable: true, format: val => { var res = this.listaTipoTarifa.find(row => row.codElemento === val); return (res === undefined ? '' : res.valor1) } },
        { name: 'NroFactura', align: 'left', label: 'Nro Factura', field: 'NroFactura', sortable: true },
        { name: 'FechaFactura', align: 'left', label: 'Fecha fact', field: 'FechaFactura', sortable: true, format: val => { var res = date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY'); return (res === '31-12-1899' ? '' : res) } },
        { name: 'base', align: 'left', label: 'Base', field: 'base', sortable: true },
        { name: 'totalIva', align: 'left', label: 'IVA', field: 'totalIva', sortable: true },
        { name: 'total', align: 'left', label: 'Total', field: 'totalEstancia', sortable: true },
        { name: 'cobrado', align: 'left', label: 'Cobrado', field: e => this.parseaFloat(e.ACuenta) + this.parseaFloat(e.PorDatafono) + this.parseaFloat(e.PorBanco), sortable: true },
        { name: 'Fianza', align: 'left', label: 'Fianza', field: 'Fianza', sortable: true }
      ],
      pagination: { rowsPerPage: 0 }
    }
  },
  computed: {
    ...mapState('login', ['user']),
    ...mapState('tablasAux', ['listaTipoEstancia', 'listaTipoTarifa'])
  },
  methods: {
    ...mapActions('tabs', ['addTab']),
    ...mapActions('estancias', ['findEstancia', 'addEstancia', 'borrarEstancia']),
    parseaFloat (f) {
      if (f === null) return 0
      else return parseFloat(f)
    },
    getRecords () {
      var objFilter = {}
      if (this.fromEstanciasMain !== undefined) {
        Object.assign(objFilter, this.value) // en this.value tenemos el valor de filterRecord (viene de facturasMain)
        // return this.$axios.get('estancias/bd_estancias.php/findEstanciasFilter', { params: objFilter })
        this.findEstancia(objFilter)
          .then(response => {
            this.registrosSeleccionados = response.data
            this.expanded = false
          })
          .catch(error => {
            this.$q.dialog({ title: 'Error', message: error })
          })
      }
    },
    addRecord () { // llamada store vuex
      var record = {
        tipoEstancia: 2,
        fechaEntrada: date.formatDate(new Date(), 'YYYY-MM-DD'),
        fechaSalida: date.addToDate(new Date(), { days: 1 })
      }
      this.addEstancia(record)
        .then(response => {
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
      this.addTab(['estanciasForm', 'Estancia-' + rowChanges.id, rowChanges, rowChanges.id])
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
            var index = this.registrosSeleccionados.findIndex(function (record) { // busco elemento del array con este id
              if (record.id === id) return true
            })
            this.registrosSeleccionados.splice(index, 1)
            this.$q.dialog({
              message: 'Se ha borrado la estancia.'
            })
            this.getRecords()
          })
          .catch(error => {
            this.$q.dialog({
              message: error.message
            })
          })
      })
    }
  },
  mounted () {
    this.getRecords()
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
