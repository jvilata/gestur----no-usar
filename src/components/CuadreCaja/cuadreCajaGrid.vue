<template>
  <div class="q-ma-sm">
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
              <div style="max-width: 45px" v-if="rowId === `m_${props.row.id}`">
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
                <div v-if="!['descuadre'].includes(col.name)">{{ col.value }}</div>
                <div v-if="['descuadre'].includes(col.name)">{{ Math.round(((parseFloatN(props.row.gastosCajaPeriodo)+parseFloatN(props.row.cajaPendiente))-parseFloatN(props.row.recaudacionCaja))*100)/100 }}</div>
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
            <div v-if="['descuadre'].includes(col.name)">{{ devolverStringCuadre(Math.round(registrosSeleccionados.reduce((a, b) => a + (parseFloatN(b.gastosCajaPeriodo)+parseFloatN(b.cajaPendiente))-parseFloatN(b.recaudacionCaja), 0)*100)/100) }}</div>
          </q-th>
        </q-tr>
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
    </q-table>
    <q-dialog v-model="mostrarDialog">
      <editCuadreCaja @close="mostrarDialog=false"
        v-model="registroEditado"
        :cabecera="value"
        @saveRecord="saveRecord"/>
    </q-dialog>
  </q-item>
  <div class="row q-ma-sm">
  <!--
  <q-input class="col-xs-6 col-sm-4" v-model="comprobarC" outlined readonly stack-label/>
  <q-btn outline class="col-xs-4 col-sm-2" color="primary" label="Comprobar Caja" @click="comprobarCaja"/>
  -->
  </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { date } from 'quasar'
export default {
  props: ['value'], // en 'value' tenemos la tabla de datos del grid
  data () {
    return {
      rowId: '',
      registrosSeleccionados: [],
      columns: [
        { name: 'fechaInicial', align: 'left', label: 'Fecha Inicial', field: 'fechaInicial', sortable: true, format: val => date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY') },
        { name: 'fechaCierre', align: 'left', label: 'Fecha Final', field: 'fechaCierre', sortable: true, format: val => date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY') },
        { name: 'facturacionPeriodo', align: 'left', label: 'Facturacion Periodo', field: 'facturacionPeriodo', sortable: true },
        { name: 'recaudacionCaja', align: 'left', label: 'Recaudacion Caja', field: 'recaudacionCaja', sortable: true },
        { name: 'gastosCajaPeriodo', align: 'left', label: 'Gastos Caja', field: 'gastosCajaPeriodo', sortable: true },
        { name: 'cajaPendiente', align: 'left', label: 'Caja a Ingresar', field: 'cajaPendiente', sortable: true },
        { name: 'descuadre', align: 'left', label: 'Descuadre', field: 'descuadre', sortable: true },
        { name: 'observaciones', align: 'left', label: 'Observaciones', field: 'observaciones', sortable: true }
      ],
      pagination: { rowsPerPage: 0 },
      mostrarDialog: false,
      registroEditado: {},
      refreshKey: 0,
      comprobarC: ''
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
    ...mapActions('cuadrecaja', ['findCuadreCaja', 'addGastos', 'borrarGastos', 'comprobarCajaTot']),
    parseFloatN (v) {
      if (v === null || v === undefined) return 0
      else return parseFloat(v)
    },
    devolverStringCuadre (v) {
      if (v < 0) return 'Faltan ' + -1 * v
      else if (v > 0) return 'Sobran ' + v
      else return 'Cuadrado'
    },
    comprobarCaja () {
      this.comprobarCajaTot(this.value)
        .then(response => {
          this.comprobarC = response.data[0].cuadreTotalCaja
          this.refreshKey++
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
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
      this.refreshKey++
      this.updateRecord(record)
    },
    updateRecord (recordToSubmit) {
      // Object.assign(recordToSubmit, { user: this.user.pers.login, ts: date.formatDate(new Date(), 'YYYY-MM-DD HH:mm:ss') })
      this.addGastos(recordToSubmit)
        .then(response => {
          console.log(response)
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    deleteRecord (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿ Borrar esta fila ?',
        ok: true,
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.borrarGastos(id)
          .then(response => {
            var index = this.registrosSeleccionados.findIndex(function (record) { // busco elemento del array con este id
              if (record.id === id) return true
            })
            this.registrosSeleccionados.splice(index, 1) // lo elimino del array
          })
          .catch(error => {
            this.$q.dialog({ title: 'Error', message: error })
          })
      })
    },
    addRecord () {
      var record = {
        fechaInicial: date.formatDate(new Date(), 'YYYY-MM-DD 00:00:00'),
        fechaCierre: date.formatDate(new Date(), 'YYYY-MM-DD 00:00:00'),
        cajaPendiente: 0,
        observaciones: '',
        facturacionPeriodo: 0,
        recaudacionCaja: 0,
        gastosCajaPeriodo: 0
      }
      this.addGastos(record)
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
