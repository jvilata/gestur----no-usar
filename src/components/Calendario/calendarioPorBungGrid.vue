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
        <q-tr :props="props" :key="`m_${props.row.id}`"
          @mouseover="rowId=`m_${props.row.id}`"
          :class="[(props.row.id !== selectedRowID) ? '':'bg-green-1']"
          @click="selectedRowID = props.row.id">
          <q-td
            v-for="col in props.cols"
            :key="col.name"
            :props="props"
          >
            <div :style="col.style">
                <div @click="editarReserva(props.row, col)" :style="(props.row[col.name]!==undefined && col.name!=='descServicio') ? (props.row[col.name].ingresado?'color: forestgreen': (duracionReserva(props.row[col.name])>30? 'color: secondary': 'color: goldenrod')) :''">
                  {{ col.value ?  col.value : '.' }}
                </div>
            </div>
           </q-td>
        </q-tr>
      </template>
      <template v-slot:bottom>
        <div>
          {{ registrosSeleccionados.length }} Filas
        </div>
      </template>
      <template v-slot:no-data>
        <div>
          No hay datos
        </div>
      </template>
    </q-table>

      <q-dialog v-model="expanded"  >
        <!-- formulario con campos de filtro -->
        <calendarioReserva
          :value="reservaActual"
          @guardarReserva="(val) => guardarReserva(val)"
          @borrarReserva="(val) => borrarReserva(val)"
          @devolverReserva="(val) => devolverReserva(val)"
          @hide="expanded = !expanded"
        />
      </q-dialog>
  </q-item>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { date } from 'quasar'
export default {
  props: ['filterRecord'], // en 'value' tenemos la tabla de datos del grid
  data () {
    return {
      expanded: false,
      rowId: '',
      selectedRowID: '',
      reservaActual: {},
      registrosSeleccionados: [],
      visible: false,
      bunga: [],
      listaReservas: [
        { id: 1, idServicio: '1', descServicio: 'Bung1', fechaEntrada: '2021-12-01', fechaSalida: '2021-12-02', cliente: 'jose blas vilata tamarit ', idCliente: 0, idEstancia: 0, observaciones: 'llevaremo sun perro y 2 caravanas' },
        { id: 2, idServicio: '36', descServicio: 'Bung2', fechaEntrada: '2021-12-01', fechaSalida: '2021-12-02', cliente: 'jose1 vilata', idCliente: 0, idEstancia: 0, observaciones: 'ob2' },
        { id: 3, idServicio: '37', descServicio: 'Bung3', fechaEntrada: '2021-12-02', fechaSalida: '2021-12-03', cliente: 'jose2 vilata', idCliente: 0, idEstancia: 0, observaciones: 'ob3' },
        { id: 4, idServicio: '1', descServicio: 'Bung1', fechaEntrada: '2021-12-03', fechaSalida: '2021-12-06', cliente: 'jose1 vilata', idCliente: 0, idEstancia: 0, observaciones: 'ob1b' }
      ],
      columns: [],
      pagination: { rowsPerPage: 0 }
    }
  },
  computed: {
    ...mapState('login', ['user']),
    ...mapState('tablasAux', ['listaTipoEstancia', 'listaTipoTarifa'])
  },
  methods: {
    ...mapActions('servicios', ['loadListaServicios']),
    ...mapActions('prereservas', ['loadPrereservas', 'addPrereservas', 'borrarPrereservas', 'devolverPrereservas']),
    duracionReserva (r) {
      var date1 = new Date(r.fechaEntrada)
      var date2 = new Date(r.fechaSalida)
      var diff = date.getDateDiff(date2, date1, 'days')
      return diff
    },
    daysInMonth (month, year) {
      return new Date(year, month, 0).getDate()
    },
    right (str, chr) {
      return str.substr(str.length - chr, str.length)
    },
    cargaColBunga () {
      this.columns = [
        { name: 'dia', align: 'left', label: 'dia', field: 'dia', style: 'font-size: 80%' }
      ]
      this.bunga.forEach(row => {
        this.columns.push({
          name: 'd' + row.id,
          align: 'left',
          label: row.descripcionCorta + ' ' + (row.Numero !== '0' ? row.Numero : ''),
          field: 'd' + row.id,
          style: 'width: 50px; whiteSpace: normal; font-size: 80%',
          format: res => (res ? res.cliente.substr(0, 20) + ' ' + (res.observaciones ? res.observaciones.substr(0, 20) : '') : '')
        })
      })
    },
    getServicios () {
      this.loadListaServicios({ tipoServicio: '1, 2, 6, 7, 9, 10, 3' })
        .then(response => {
          this.bunga = response.data
          this.getReservas()
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error.message })
        })
    },
    getReservas () {
      this.loadPrereservas(this.filterRecord)
        .then(response => {
          this.listaReservas = response.data
          this.getRecords()
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error.message })
        })
    },
    getRecords () {
      this.registrosSeleccionados = []
      // this.cargaColumns()
      this.cargaColBunga()

      var mesanyo = this.filterRecord.anyo + '-' + this.right(('0' + this.filterRecord.mes), 2)
      var dm = this.daysInMonth(this.filterRecord.mes, this.filterRecord.anyo)
      for (let i = 1; i <= dm; i++) {
        var r = {
          id: i,
          // idServicio: rbunga.id,
          dia: this.right(('0' + i), 2)
        }
        var lres = this.listaReservas.filter(res => ((res.fechaEntrada.substr(0, 10) <= mesanyo + '-' + r.dia) && (res.fechaSalida.substr(0, 10) >= mesanyo + '-' + r.dia)))
        lres.forEach(res => {
          r['d' + res.idServicio] = res
        })
        this.registrosSeleccionados.push(r)
      }
    },
    editarReserva (row, col) {
      this.reservaActual = row[col.name]
      if (this.reservaActual === undefined) {
        this.reservaActual = {
          idServicio: (col.name).substr(1, 3),
          descServicio: col.label,
          fechaEntrada: this.filterRecord.anyo + '-' + this.right(('0' + this.filterRecord.mes), 2) + '-' + row.dia
        }
        var d = new Date(this.reservaActual.fechaEntrada)
        d.setDate(d.getDate() + 1)
        this.reservaActual.fechaSalida = date.formatDate(d, 'YYYY-MM-DD')
      }
      this.expanded = true
    },
    guardarReserva (reserva) {
      this.expanded = false
      this.addPrereservas(reserva)
        .then(result => {
          this.getServicios()
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error.message })
        })
    },
    borrarReserva (reserva) {
      this.expanded = false
      this.borrarPrereservas(reserva.id)
        .then(result => {
          this.getServicios()
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error.message })
        })
    },
    devolverReserva (reserva) {
      this.expanded = false
      reserva.fechaEntrada = date.formatDate(new Date(reserva.fechaEntrada), 'DD/MM/YYYY')
      reserva.fechaSalida = date.formatDate(new Date(reserva.fechaSalida), 'DD/MM/YYYY')
      this.devolverPrereservas(reserva)
        .then(result => {
          this.getServicios()
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error.message })
        })
    }
  },
  mounted () {
    this.getServicios()
  },
  components: {
    calendarioReserva: require('components/Calendario/calendarioReserva.vue').default
  }
}
</script>

<style lang="sass">
  .estanciasGrid-header-table
    .q-table__top,
    .q-table__bottom,
    thead tr:first-child th
      /* bg color is important for th; just specify one */
      background-color: $indigo-1

    thead tr th
      position: sticky
      z-index: 1
    thead tr:first-child th
      top: 0

    td:first-child
      background-color: $orange-1
    th:first-child
      position: sticky
      left: 0
      z-index: 3
    td:first-child
      position: sticky
      left: 0
      z-index: 2

    /* this is when the loading indicator appears */
    &.q-table--loading thead tr:last-child th
      /* height of all previous header rows */
      top: 48px
</style>
