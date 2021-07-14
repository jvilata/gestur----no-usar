<template>
  <q-item class="row">
    <!-- GRID. en row-key ponemos la columna del json que sea la id unica de la fila -->
    <q-table
      class="accionesGrid-header-table"
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
              {{ col.value }}
            </div>
            <q-popup-edit
              v-if="!['id','Numero'].includes(col.name)"
              v-model="props.row[col.name]"
              buttons
              @save="updateRecord(props.row)">
              <q-input
                v-if="['tipoIva', 'descripcionCorta', 'descripcionLarga'].includes(col.name)"
                type="text"
                v-model="props.row[col.name]"
                dense
                autofocus />
              <q-select
                v-if="['tipoServicio'].includes(col.name)"
                outlined
                :value="col.value"
                @input="(value) => props.row[col.name]=value"
                :options="listaTipoServ"
                stack-label
                option-value="codElemento"
                option-label="valor1"
                emit-value
              />
            </q-popup-edit>
          </q-td>
        </q-tr>
      </template>
   <template v-slot:no-data>
        <div class="absolute-bottom text-center q-mb-sm">
          <q-btn
            @click="expanded = !expanded"
            round
            dense
            color="indigo-5"
            size="20px"
            icon="add">
            <q-tooltip>Añadir Servicio</q-tooltip>
          </q-btn>
        </div>
        <div>
          Pulse + para añadir
        </div>
      </template>
      <template v-slot:bottom>
        <div class="absolute-bottom text-center q-mb-sm">
          <q-btn
            @click="addRecord()"
            round
            dense
            color="indigo-5"
            size="20px"
            icon="add">
            <q-tooltip>Añadir Servicio</q-tooltip>
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
import { mapActions, mapState } from 'vuex'
export default {
  props: ['value', 'idActivo'], // en 'value' tenemos la tabla de datos del grid, en idActivo en caso de que vengamos de ActivosFormMain
  data () {
    return {
      rowId: '',
      columns: [
        // { name: 'id', label: 'Id', align: 'left', field: 'id', sortable: true },
        { name: 'id', align: 'left', label: 'ID', field: 'id', sortable: true },
        { name: 'descripcionCorta', align: 'left', label: 'Descripción Corta', field: 'descripcionCorta', sortable: true },
        { name: 'descripcionLarga', align: 'left', label: 'Descripción Larga', field: 'descripcionLarga', sortable: true },
        { name: 'tipoIva', align: 'left', label: '%IVA', field: 'tipoIva', sortable: true },
        { name: 'numero', align: 'left', label: 'Número', field: 'Numero', sortable: true },
        {
          name: 'tipoServicio',
          align: 'left',
          label: 'Tipo Servicio',
          field: 'tipoServicio',
          sortable: true,
          format: val => {
            var obj = this.listaTipoServ.find(x => x.codElemento === val) // mapea el valor 0 , 1 en la listaSINO a string SI , NO
            return (obj !== undefined ? obj.valor1 : val)
          }
        }
      ],
      pagination: { rowsPerPage: 0 }
    }
  },
  methods: {
    ...mapActions('tabs', ['addTab']),
    ...mapActions('servicios', ['borrarTablaAux', 'addServicios', 'borrarServicio']),
    updateRecord (record) { // Volvemos a llamar a addServicios con el contenido de props.row
      this.addServicios(record) // para actualizar el contenido (actualizamos porque extraemos el id y modificamos)
        .then(response => {
          console.log(response)
          // record.id = response.data.id
          // this.value.push(record)
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    },
    addRecord () {
      var record = {
        descripcionCorta: 'Nuevo Servicio',
        descripcionLarga: 'Nuevo Servicio',
        tipoServicio: '1',
        tipoIva: 10
      }
      this.addServicios()
        .then(response => {
          record.id = response.data.id
          this.value.push(record)
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
        this.borrarServicio(id)
          .then(result => {
            this.$q.dialog({
              message: 'Se ha borrado con éxito.'
            })
            var index = this.value.findIndex(function (record) { // busco elemento del array con este id
              if (record.id === id) return true
            })
            this.value.splice(index, 1) // lo elimino del array
          })
          .catch(error => {
            this.$q.dialog({
              message: error.message
            })
          })
      })
    },
    mostrarDatosPieTabla () {
      return this.value.length + ' Filas'
    }
  },
  computed: {
    ...mapState('tablasAux', ['listaTipoServ'])
  }
}
</script>
<style lang="sass">
  .accionesGrid-header-table
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
