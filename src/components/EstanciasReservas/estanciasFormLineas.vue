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
            <div style="max-width: 30px">
            <!--edit icon . Decomentamos si necesitamos accion especifica de edicion -->
            <q-btn flat v-if="rowId===`m_${props.row.id}`"
              @click.stop="editRecord(props.row, props.row.id)"
              round
              dense
              size="sm"
              color="primary"
              icon="edit"/>
            <q-btn flat v-if="rowId===`m_${props.row.id}`"
              @click.stop="deleteRecord(props.row.id)"
              round
              dense
              size="sm"
              color="red"
              icon="delete"/>
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
        @saveRecord="saveRecord"/>
    </q-dialog>
  </q-item>
</template>

<script>
import { mapState, mapActions } from 'vuex'
// import { date } from 'quasar'

export default {
  props: ['value'], // en 'value' tenemos la tabla de datos del grid
  data () {
    return {
      rowId: '',
      mostrarDialog: false,
      registrosSeleccionados: [],
      registroEditado: {},
      columns: [
        { name: 'servicio', label: 'Servicio', align: 'center', field: 'servicio', sortable: true },
        { name: 'numero', align: 'left', label: 'Número', field: 'numero', sortable: true },
        { name: 'fechaInicio', align: 'left', label: 'fechaInicio', field: 'fechaInicio', sortable: true },
        { name: 'fechaFin', align: 'left', label: 'fechaFin', field: 'fechaFin', sortable: true },
        { name: 'noches', align: 'left', label: 'noches', field: 'noches', sortable: true },
        { name: 'cantidad', align: 'left', label: 'cantidad', field: 'cantidad', sortable: true, format: val => this.$numeral(parseFloat(val)).format('0,0.00') },
        { name: 'tarifa', align: 'left', label: 'tarifa', field: 'tarifa', sortable: true },
        { name: 'piva', align: 'left', label: '%Iva', field: 'piva', sortable: true, format: val => this.$numeral(parseFloat(val)).format('0,0.00') },
        { name: 'pdescuento', align: 'left', label: '%Dto', field: 'pdescuento', sortable: true, format: val => this.$numeral(parseFloat(val)).format('0,0.00') },
        { name: 'total', align: 'left', label: 'Total', field: 'total', sortable: true, format: val => this.$numeral(parseFloat(val)).format('0,0.00') },
        { name: 'dudoso', label: 'dudoso', align: 'left', field: 'dudoso', sortable: true },
        { name: 'comentarios', align: 'left', label: 'comentarios', field: 'comentarios', sortable: true }
      ],
      pagination: { rowsPerPage: 0 }
    }
  },
  computed: {
    ...mapState('login', ['user'])
  },
  methods: {
    ...mapActions('tabs', ['addTab']),
    getRecords () {
    },
    addRecord () {
    //   var record = {
    //     idcabFactura: this.value.id,
    //     descripcion: 'Nueva línea',
    //     unidades: 0,
    //     precio: 0,
    //     pdescuento: 0,
    //     neto: 0,
    //     piva: 21,
    //     totalIva: 0,
    //     totalLinea: 0,
    //     user: this.user.user.email,
    //     ts: date.formatDate(new Date(), 'YYYY-MM-DD HH:mm:ss')
    //   }
    //   return this.$axios.post('facturas/bd_facturas.php/findLinFacturasFilter/', record)
    //     .then(response => {
    //       record.id = response.data.id
    //       this.registrosSeleccionados.push(record)
    //       this.editRecord(record, record.id)
    //     })
    //     .catch(error => {
    //       this.$q.dialog({ title: 'Error', message: error })
    //     })
    },
    deleteRecord (id) {
    //   this.$q.dialog({
    //     title: 'Confirmar',
    //     message: '¿ Borrar esta fila ?',
    //     ok: true,
    //     cancel: true,
    //     persistent: true
    //   }).onOk(() => {
    //     return this.$axios.delete(`facturas/bd_facturas.php/findLinFacturasFilter/${id}`, JSON.stringify({ id: id }))
    //       .then(response => {
    //         var index = this.registrosSeleccionados.findIndex(function (record) { // busco elemento del array con este id
    //           if (record.id === id) return true
    //         })
    //         this.registrosSeleccionados.splice(index, 1) // lo elimino del array
    //         this.calcularTotalesLineas()
    //       })
    //       .catch(error => {
    //         this.$q.dialog({ title: 'Error', message: error })
    //       })
    //   })
    },
    updateRecord (recordToSubmit) {
    //   Object.assign(recordToSubmit, { user: this.user.user.email, ts: date.formatDate(new Date(), 'YYYY-MM-DD HH:mm:ss') })
    //   return this.$axios.put(`facturas/bd_facturas.php/findLinFacturasFilter/${recordToSubmit.id}`, recordToSubmit)
    //     .then(response => {
    //       this.calcularTotalesLineas()
    //     })
    //     .catch(error => {
    //       this.$q.dialog({ title: 'Error', message: error })
    //     })
    },
    saveRecord (record) {
    //   this.mostrarDialog = false
    //   var index = this.registrosSeleccionados.findIndex(function (sel) { // busco elemento del array con este id
    //     if (sel.id === record.id) return true
    //   })
    //   Object.assign(this.registrosSeleccionados[index], record)
    //   this.updateRecord(record)
    // },
    // editRecord (rowChanges, id) {
    //   Object.assign(this.registroEditado, rowChanges)
    //   this.mostrarDialog = true
    // },
    // mostrarDatosPieTabla () {
    //   return this.registrosSeleccionados.length + ' Filas'
    // }
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
