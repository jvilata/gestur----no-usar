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
              @save="updateRecord(props.row)" >
              <q-input
                v-if="['Nombre', 'PrimerApellido', 'SegundoApellido', 'dni', 'pasaporte', 'PaisNac'].includes(col.name)"
                type="text"
                v-model="props.row[col.name]"
                dense
                autofocus/>
              <q-input
                v-if="['FechaExp', 'FechaNac'].includes(col.name)"
                type="text"
                mask="##-##-####"
                :value="props.row[col.name]===null||props.row[col.name]===undefined?null:props.row[col.name].substr(8,2)+'-'+props.row[col.name].substr(5,2)+'-'+props.row[col.name].substr(0,4)"
                @input="v=>props.row[col.name]=(v===null||v===undefined?null:v.substr(6,4)+'-'+v.substr(3,2)+'-'+v.substr(0,2)+' 00:00:00')"
                dense
                autofocus/>
              <wgDate v-if="['FechaEntrada', ].includes(col.name)"
                v-model="props.row[col.name]"/>
              <q-select
                v-if="['sexo'].includes(col.name)"
                outlined
                v-model="props.row[col.name]"
                :options="sexoList"
                emit-value
                map-options
              />
              <q-select
                v-if="['TipoDoc'].includes(col.name)"
                outlined
                v-model="props.row[col.name]"
                :options="tipoDocList"
                emit-value
                map-options
              />
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
          {{ listaRegTipo2.length }} Filas
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
      recordToSubmit: {},
      registrosSeleccionados: [],
      response: 0,
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
        { name: 'FechaEntrada', align: 'left', label: 'Fecha Entrada', field: 'FechaEntrada', sortable: true, format: val => { var res = date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY'); return ((res === '30-11-1899') || (res === '31-12-1899') ? '' : res) } },
        { name: 'FechaExp', align: 'left', label: 'Fecha Exp.', field: 'FechaExp', sortable: true, format: val => { var res = date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY'); return ((res === '30-11-1899') || (res === '31-12-1899') ? '' : res) } },
        { name: 'FechaNac', align: 'left', label: 'Fecha Nac.', field: 'FechaNac', sortable: true, format: val => { var res = date.formatDate(date.extractDate(val, 'YYYY-MM-DD HH:mm:ss'), 'DD-MM-YYYY'); return ((res === '30-11-1899') || (res === '31-12-1899') ? '' : res) } },
        { name: 'PaisNac', align: 'left', label: 'paisNac', field: 'PaisNac', sortable: true }
      ],
      pagination: { rowsPerPage: 0 },
      listaRegTipo2: [],
      sexoList: [
        'M', 'F'
      ],
      tipoDocList: [
        'D', 'P', 'C'
      ],
      refresh: 0
    }
  },
  computed: {
    ...mapState('login', ['user'])
  },
  methods: {
    ...mapActions('tabs', ['addTab']),
    ...mapActions('viajeros', ['findViajerosFilter', 'addViajero', 'borrarViajero']),
    getRecords () {
      var objFilter = { idEstancia: this.value.id }
      this.findViajerosFilter(objFilter)
        .then(response => {
          this.listaRegTipo2 = response.data
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    addRecord () {
      var record = {
        FechaEntrada: this.value.fechaEntrada,
        Nombre: '',
        PrimerApellido: '',
        SegundoApellido: '',
        dni: '',
        pasaporte: '',
        PaisNac: 'ESP',
        idEstancia: this.value.id,
        sexo: 'M',
        TipoDoc: 'D',
        FechaExp: '2000-01-01 00:00:00',
        FechaNac: '2000-01-01 00:00:00'
      }
      this.addViajero(record)
        .then(response => {
          record.id = response.data.id
          this.listaRegTipo2.push(record)
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
        this.borrarViajero(id)
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
    updateRecord (record) { // Volvemos a llamar a addServicios con el contenido de props.row
      this.addViajero(record)
        .then(response => {
          this.refresh++
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    }
  },
  components: {
    wgDate: wgDate
  },
  mounted () {
    this.getRecords()
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
