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
      :data="listaRegTipo2"
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
            <q-popup-edit
              v-if="!['id'].includes(col.name)"
              v-model="props.row[col.name]"
              buttons
              @save="updateRecord(props.row)">
              <q-input
                v-if="['descripcion', 'cantidad', 'factura'].includes(col.name)"
                type="text"
                v-model="props.row[col.name]"
                dense
                autofocus />
                <wgDate v-if="['fecha'].includes(col.name)"
                  v-model="props.row[col.name]" />
            </q-popup-edit>
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
      <guardiaCivilFormLinDetalle
        @close="mostrarDialog=false"
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
        { name: 'id', label: 'ID', align: 'center', field: 'id', sortable: true },
        { name: 'Nombre', align: 'left', label: 'nombre', field: 'Nombre', sortable: true },
        { name: 'PrimerApellido', align: 'left', label: 'Primer Apellido', field: 'PrimerApellido', sortable: true },
        { name: 'SegundoApellido', align: 'left', label: 'Segundo Apellido', field: 'SegundoApellido', sortable: true },
        { name: 'sexo', align: 'left', label: 'sexo', field: 'sexo', sortable: true },
        { name: 'TipoDoc', align: 'left', label: 'Tipo Doc.', field: 'TipoDoc', sortable: true },
        { name: 'dni', label: 'DNI', align: 'center', field: 'dni', sortable: true },
        { name: 'pasaporte', align: 'left', label: 'Pasaporte', field: 'pasaporte', sortable: true },
        { name: 'FechaEntrada', align: 'left', label: 'Fecha Entrada', field: 'FechaEntrada', sortable: true, format: val => { var res = date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY'); return (res === '31-12-1899' ? '' : res) } },
        { name: 'FechaExp', align: 'left', label: 'Fecha Exp.', field: 'FechaExp', sortable: true, format: val => { var res = date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY'); return (res === '31-12-1899' ? '' : res) } },
        { name: 'FechaNac', align: 'left', label: 'Fecha Nac.', field: 'FechaNac', sortable: true, format: val => { var res = date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY'); return (res === '31-12-1899' ? '' : res) } },
        { name: 'PaisNac', align: 'left', label: 'paisNac', field: 'PaisNac', sortable: true }
      ],
      pagination: { rowsPerPage: 0 },
      listaRegTipo2: []
    }
  },
  computed: {
    ...mapState('login', ['user'])
  },
  methods: {
    ...mapActions('tabs', ['addTab']),
    ...mapActions('guardiaC', ['borrarTipo2']),
    addRecord () {
      /* var record = {}
      this.addReserva(record)
        .then(response => {
          record.id = response.data.id
          this.registrosSeleccionados.push(record)
          this.editRecord(record)
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        }) */
    },
    deleteRecord (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿ Borrar esta fila ?',
        ok: true,
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.borrarTipo2(id)
          .then(response => {
            var index = this.listaRegTipo2.findIndex(function (record) { // busco elemento del array con este id
              if (record.id === id) return true
            })
            this.listaRegTipo2.splice(index, 1) // lo elimino del array
          })
          .catch(error => {
            this.$q.dialog({ title: 'Error', message: error })
          })
      })
    },
    updateRecord (recordToSubmit) {
      Object.assign(recordToSubmit, { user: this.user.pers.login, ts: date.formatDate(new Date(), 'YYYY-MM-DD HH:mm:ss') })
      /* this.addReserva(recordToSubmit)
        .then(response => {
          this.calcularTotalesLineas()
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        }) */
    },
    saveRecord (record) {
      this.mostrarDialog = false
      /* var index = this.registrosSeleccionados.findIndex(function (sel) {
        // busco elemento del array con este id
        if (sel.id === record.id) return true
      })
      Object.assign(this.registrosSeleccionados[index], record)
      this.updateRecord(record) */
    },
    editRecord (rowChanges) {
      Object.assign(this.registroEditado, rowChanges)
      this.mostrarDialog = true
    }
  },
  components: {
    guardiaCivilFormLinDetalle: require('components/GuardiaCivil/guardiaCivilFormLinDetalle.vue').default
  },
  mounted () {
    this.listaRegTipo2 = this.value
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
