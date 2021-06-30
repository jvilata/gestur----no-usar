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
                <q-icon  name="edit" class="text-grey q-pr-md" style="font-size: 1.5rem;" @click="editRecord(props.row, props.row.id)"/>
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
            <q-popup-edit
              v-model="props.row[col.name]"
              buttons
              @save="updateRecord(props.row)">
              <q-input
                v-if="['descripcion', 'cantidad', 'factura'].includes(col.name)"
                type="text"
                v-model="props.row[col.name]"
                dense
                autofocus />
            </q-popup-edit>
           </q-td>
           <!-- <q-td>
              <q-slide-transition>
                <q-btn icon="cloud_download" style="font-size: 0.8rem;" color="indigo-3" @click="editRecord(props.row.id)"/>
              </q-slide-transition>
            </q-td> -->
        </q-tr>
      </template>
      <template v-slot:no-data>
        <div class="absolute-bottom text-center q-mb-sm">
          <q-btn
            @click.stop="addRecord()"
            round
            dense
            color="indigo-5"
            size="20px"
            icon="add">
            <q-tooltip>Añadir</q-tooltip>
          </q-btn>
        </div>
        <div>
          Pulse + para añadir
        </div>
      </template>
      <template v-slot:bottom>
        <div class="absolute-bottom text-center q-mb-sm">
          <q-btn
            @click.stop="addRecord()"
            round
            dense
            color="indigo-5"
            size="20px"
            icon="add">
            <q-tooltip>Añadir</q-tooltip>
          </q-btn>
        </div>
        <div>
          {{ `${registrosSeleccionados.length ? registrosSeleccionados.length + ' Filas' : 'No hay registros, pulse + para añadir clientes'}` }}
        </div>
      </template>
    </q-table>
    <q-dialog v-model="mostrarDialog">
      <editCuadreCaja @close="mostrarDialog=false"
        v-model="registroEditado"
        :cabecera="value"
        @saveRecord="saveRecord"/>
    </q-dialog>
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
      columns: [
        { name: 'fechaInicial', align: 'left', label: 'Fecha Inicial', field: 'fechaInicial', sortable: true, format: val => date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY') },
        { name: 'fechaCierre', align: 'left', label: 'Fecha Final', field: 'fechaCierre', sortable: true, format: val => date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY') },
        { name: 'facturacionPeriodo', align: 'left', label: 'Facturacion Periodo', field: 'facturacionPeriodo', sortable: true },
        { name: 'recaudacionCaja', align: 'left', label: 'recaudacionCaja', field: 'recaudacionCaja', sortable: true },
        { name: 'gastosCajaPeriodo', align: 'left', label: 'gastosCaja', field: 'gastosCajaPeriodo', sortable: true },
        { name: 'cajaPendiente', align: 'left', label: 'cajaAIngresar', field: 'cajaPendiente', sortable: true },
        { name: 'descuadre', align: 'left', label: 'descuadre', field: 'descuadre', sortable: true },
        { name: 'observaciones', align: 'left', label: 'observaciones', field: 'observaciones', sortable: true }
      ],
      pagination: { rowsPerPage: 0 },
      mostrarDialog: false,
      registroEditado: {}
    }
  },
  computed: {
    ...mapState('login', ['user'])
  },
  components: {
    editCuadreCaja: require('components/CuadreCaja/editCuadreCaja.vue').default
  },
  mounted () {
    // if (this.value.fechaInicio !== undefined || this.value.fechaFin !== undefined) {
    this.getRecords()
    // }
  },
  methods: {
    ...mapActions('tabs', ['addTab']),
    ...mapActions('cuadrecaja', ['findCuadreCaja']),
    getRecords () {
      var objFilter = {}
      Object.assign(objFilter, this.value) // en this.value tenemos el valor de filterRecord (viene de facturasMain)
      this.findCuadreCaja(objFilter)
        .then(response => {
          this.registrosSeleccionados = response.data
          this.expanded = false
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    saveRecord (record) {
      this.mostrarDialog = false
      var index = this.registrosSeleccionados.findIndex(function (sel) {
        // busco elemento del array con este id
        if (sel.id === record.id) return true
      })
      Object.assign(this.registrosSeleccionados[index], record)
      this.updateRecord(record)
    },
    deleteRecord (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿ Borrar esta fila ?',
        ok: true,
        cancel: true,
        persistent: true
      }).onOk(() => {

      })
    },
    addRecord () {
      var record = {
        idServicio: 0,
        Numero: 0,
        dudoso: '0',
        cantidad: 1,
        tarifa: 0,
        idEstancia: this.value.id,
        noches: 1,
        fechaInicio: this.value.fechaEntrada,

        fechaFin: this.value.fechaSalida,
        tipoIva: 10,
        dto: 0,
        comentarios: ''
      }
      this.addReserva(record)
        .then(response => {
          record.id = response.data.id
          this.registrosSeleccionados.push(record)
          this.editRecord(record)
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    editRecord (rowChanges) {
      Object.assign(this.registroEditado, rowChanges)
      this.mostrarDialog = true
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
