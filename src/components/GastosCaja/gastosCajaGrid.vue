<template>
  <q-item class="row q-ma-xs q-pa-xs">
    <!-- GRID. en row-key ponemos la columna del json que sea la id unica de la fila -->
    <q-table
      class="estanciasGrid-header-table"
      virtual-scroll
      :pagination.sync="pagination"
      :rows-per-page-options="[0]"
      :virtual-scroll-sticky-size-start="48"
      row-key="name"
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
              v-if="['devuelto', 'tipoGasto', 'descripcion', 'cantidad', 'factura', 'fecha'].includes(col.name)"
              v-model="props.row[col.name]"
              buttons
              @save="updateRecord(props.row)">
              <q-input
                v-if="['descripcion', 'cantidad', 'factura'].includes(col.name)"
                type="text"
                v-model="props.row[col.name]"
                dense
                autofocus />
                <q-select v-if="['tipoGasto'].includes(col.name)"
                  class="col-xs-6 col-sm-3"
                  outlined
                  clearable
                  label="Tipo Gasto"
                  stack-label
                  v-model="props.row[col.name]"
                  :options="listaTiposGasto"
                  option-value="codElemento"
                  option-label="valor1"
                  emit-value
                  map-options
                  use-input
                />
                <q-select v-if="['devuelto'].includes(col.name)"
                  class="col-xs-4 col-sm-3"
                  label="Devuelto"
                  stack-label
                  outlined
                  clearable
                  v-model="props.row[col.name]"
                  :options="listaSINO"
                  option-value="id"
                  option-label="desc"
                  emit-value
                  map-options
                />
                <wgDate v-if="['fecha'].includes(col.name)"
                  v-model="props.row[col.name]" />
            </q-popup-edit>
           </q-td>
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
  </q-item>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'
export default {
  props: ['value'], // en 'value' tenemos la tabla de datos del grid
  data () {
    return {
      rowId: '',
      registrosSeleccionados: [],
      columns: [
        { name: 'fecha', align: 'left', label: 'Fecha', field: 'fecha', sortable: true, format: val => date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY') },
        { name: 'descripcion', align: 'left', label: 'Descripción', field: 'descripcion', sortable: true },
        { name: 'cantidad', align: 'left', label: 'Cantidad', field: 'cantidad', sortable: true },
        { name: 'factura', align: 'left', label: 'Factura', field: 'factura', sortable: true },
        { name: 'id', align: 'left', label: 'id', field: 'id', sortable: true },
        { name: 'tipoGasto', align: 'left', label: 'Tipo Gasto', field: 'tipoGasto', sortable: true },
        { name: 'devuelto', align: 'left', label: 'devuelto', field: 'devuelto', sortable: true }
      ],
      pagination: { rowsPerPage: 0 }
    }
  },
  computed: {
    ...mapState('login', ['user']),
    ...mapState('tablasAux', ['listaTiposGasto', 'listaSINO'])
  },
  components: {
    wgDate: wgDate
  },
  mounted () {
    if (this.value.fechaInicio !== undefined || this.value.fechaFin !== undefined) {
      this.getRecords()
    }
  },
  methods: {
    ...mapActions('tabs', ['addTab']),
    ...mapActions('gastos', ['findGastos', 'addGastos', 'borrarGastos']),
    getRecords () {
      var objFilter = {}
      Object.assign(objFilter, this.value) // en this.value tenemos el valor de filterRecord (viene de facturasMain)
      // return this.$axios.get('estancias/bd_estancias.php/findEstanciasFilter', { params: objFilter })
      this.findGastos(objFilter)
        .then(response => {
          this.registrosSeleccionados = response.data
          this.expanded = false
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    addRecord () {
      var record = {
        fecha: date.formatDate(new Date(), 'YYYY-MM-DD 00:00:00'),
        descripcion: '',
        cantidad: 0.0,
        factura: '',
        tipoGasto: 'C'
      }
      this.addGastos(record)
        .then((response) => {
          record.id = response.data.id
          this.registrosSeleccionados.push(record)
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    updateRecord (record) {
      this.addGastos(record)
        .then((response) => {
          console.log(response.data)
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
          .then(result => {
            this.$q.dialog({
              message: 'Se ha borrado el gasto.'
            })
            var index = this.registrosSeleccionados.findIndex(function (record) { // busco elemento del array con este id
              if (record.id === id) return true
            })
            this.registrosSeleccionados.splice(index, 1) // lo elimino del array
          })
          .catch(error => {
            this.$q.dialog({
              message: error.message
            })
          })
      })
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
