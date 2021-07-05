<template>
  <q-item class="row">
    <!-- GRID. en row-key ponemos la columna del json que sea la id unica de la fila -->
    <q-table
      class="facturasFormLineas-header-table"
      dense
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
          <q-th>
          </q-th>

          <q-th
            v-for="col in props.cols"
            :key="col.name"
            :props="props"
          >
            {{ col.label }}
          </q-th>
        </q-tr>
      </template>

      <template v-slot:body="props">
        <q-tr :props="props" :key="`m_${props.row.id}`" @mouseover="rowId=`m_${props.row.id}`">
          <q-td>
            <!-- columna de acciones: editar, borrar, etc -->
            <div style="max-width: 30px" class="q-mr-lg">
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
              {{ col.value }}
            </div>
          </q-td>
        </q-tr>
      </template>

      <template v-slot:no-data>
        <div class="absolute-bottom q-mb-sm" style="left: 45vw">
          <q-btn
            @click.stop="addRecord"
            round
            dense
            color="indigo-5"
            size="15px"
            icon="add"/>
        </div>
        <div>
          No hay registros, pulse el botón + para añadir
        </div>
      </template>

      <template v-slot:bottom>
        <div class="absolute-bottom q-mb-sm" style="left: 45vw">
          <q-btn
            @click.stop="addRecord"
            round
            dense
            color="indigo-5"
            size="15px"
            icon="add"/>
        </div>
        <div>
          {{ registrosSeleccionados.length }} Filas
        </div>
      </template>
    </q-table>

    <q-dialog v-model="mostrarDialog">
      <estanciasFormLinDetalle @close="mostrarDialog=false"
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
  props: ['value'], // en 'value' tenemos la tabla de datos del grid
  data () {
    return {
      rowId: '',
      recordToSubmit: {},
      mostrarDialog: false,
      registrosSeleccionados: [],
      registroEditado: {},
      columns: [
        { name: 'idServicio', label: 'Servicio', align: 'center', field: 'idServicio', sortable: true, format: val => { var res = this.listaServicios.find(row => row.id === val); return (res === undefined ? '' : res.descripcionCorta) } },
        { name: 'Numero', align: 'left', label: 'Número', field: 'Numero', sortable: true },
        { name: 'fechaInicio', align: 'left', label: 'Fecha Inicio ', field: 'fechaInicio', sortable: true, format: val => { var res = date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY'); return (res === '31-12-1899' ? '' : res) } },
        { name: 'fechaFin', align: 'left', label: 'Fecha Fin ', field: 'fechaFin', sortable: true, format: val => { var res = date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY'); return (res === '31-12-1899' ? '' : res) } },
        { name: 'noches', align: 'left', label: 'Noches', field: 'noches', sortable: true },
        { name: 'cantidad', align: 'left', label: 'Cantidad', field: 'cantidad', sortable: true },
        { name: 'tarifa', align: 'left', label: 'Tarifa', field: 'tarifa', sortable: true },
        { name: 'tipoIva', align: 'left', label: '%IVA', field: 'tipoIva', sortable: true },
        { name: 'dto', align: 'left', label: '%DTO', field: 'dto', sortable: true },
        { name: 'total', align: 'left', label: 'Total', field: 'total', sortable: true },
        {
          name: 'dudoso',
          label: 'Dudoso',
          align: 'left',
          field: 'dudoso',
          sortable: true,
          format: val => {
            var obj = this.listaSINO.find(x => x.id === val) // mapea el valor 0 , 1 en la listaSINO a string SI , NO
            return (obj !== undefined ? obj.desc : val)
          }
        },
        { name: 'comentarios', align: 'left', label: 'Comentarios', field: 'comentarios', sortable: true }
      ],
      pagination: { rowsPerPage: 0 }
    }
  },
  computed: {
    ...mapState('login', ['user']),
    ...mapState('tablasAux', ['listaTipoServ', 'listaSINO']),
    ...mapState('servicios', ['listaServicios'])
  },
  methods: {
    ...mapActions('tabs', ['addTab']),
    ...mapActions('estancias', ['findLinEstancias', 'borrarReserva', 'addReserva']),
    getRecords () {
      var objFilter = { idEstancia: this.value.id }
      this.findLinEstancias(objFilter)
        .then(response => {
          this.registrosSeleccionados = response.data
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
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
      // return this.$axios.get('estancias/bd_estancias.php/guardarReservasBD/', { params: record })
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
    deleteRecord (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿ Borrar esta fila ?',
        ok: true,
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.borrarReserva(id)
          .then(response => {
            var index = this.registrosSeleccionados.findIndex(function (record) { // busco elemento del array con este id
              if (record.id === id) return true
            })
            this.registrosSeleccionados.splice(index, 1) // lo elimino del array
            this.calcularTotalesLineas()
          })
          .catch(error => {
            this.$q.dialog({ title: 'Error', message: error })
          })
      })
    },
    updateRecord (recordToSubmit) {
      Object.assign(recordToSubmit, { user: this.user.pers.login, ts: date.formatDate(new Date(), 'YYYY-MM-DD HH:mm:ss') })
      // return this.$axios.get('estancias/bd_estancias.php/guardarReservasBD/', { params: recordToSubmit })
      this.addReserva(recordToSubmit)
        .then(response => {
          this.calcularTotalesLineas()
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    calcularTotalesLineas () {
      // calcula totales y pasalos a estanciasForm
      var obj = { base: 0, totalIva: 0 }
      this.registrosSeleccionados.forEach(row => { var base = parseFloat(row.total) / (1 + parseFloat(row.tipoIva) / 100); obj.base += base; obj.totalIva += parseFloat(row.total) - base })
      this.$emit('calculaTotalesEst', obj)
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
    editRecord (rowChanges) {
      Object.assign(this.registroEditado, rowChanges)
      this.mostrarDialog = true
    }
  },
  mounted () {
    this.getRecords()
  },
  components: {
    estanciasFormLinDetalle: require('components/EstanciasReservas/estanciasFormLinDetalle.vue').default
  }
}
</script>
<style lang="sass">
  .facturasFormLineas-header-table
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
