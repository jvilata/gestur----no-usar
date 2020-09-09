<template>
  <q-item class="row q-ma-xs q-pa-xs">
    <!-- GRID. en row-key ponemos la columna del json que sea la id unica de la fila -->
    <q-table
      class="personalGrid-header-table"
      virtual-scroll
      :pagination.sync="pagination"
      :rows-per-page-options="[0]"
      :virtual-scroll-sticky-size-start="48"
      row-key="id"
      :data="value"
      :columns="columns"
      table-style="max-height: 60vh; max-width: 96vw"
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
        </q-tr>
      </template>

      <template v-slot:body="props">
        <q-tr :props="props" :key="`m_${props.row.id}`" @mouseover="rowId=`m_${props.row.id}`">
          <q-td
            v-for="col in props.cols"
            :key="col.name"
            :props="props"
          >
          <div :style="col.style">
              <div>{{ col.value }}</div>
          </div>
           </q-td>
        </q-tr>
      </template>

      <template v-slot:bottom>
        <div>
          {{ value.length }} Filas
        </div>
      </template>

    </q-table>
  </q-item>
</template>

<script>
export default {
  props: ['value'], // en 'value' tenemos los registrosSeleccionados
  data () {
    return {
      expanded: false,
      columns: [
        { name: 'id', align: 'left', label: 'ID', field: 'id' },
        { name: 'nombre', align: 'left', label: 'Nombre', field: 'nombre', style: 'width: 150px; whiteSpace: normal' },
        { name: 'telefono', label: 'Teléfono', align: 'left', field: 'telefono', style: 'width: 100px' },
        { name: 'dni', label: 'DNI', align: 'left', field: 'dni' },
        { name: 'nacionalidad', align: 'left', label: 'Nacionalidad', field: 'nacionalidad' },
        { name: 'matricula', align: 'left', label: 'Matrícula', field: 'matricula' },
        { name: 'pendCobro', align: 'left', label: 'pendCobro', field: 'pendCobro' }
      ],
      pagination: { rowsPerPage: 0 }
    }
  },
  methods: {
    mostrarDatosPieTabla () {
      return this.value.length + ' Filas'
    }
  },
  mounted () {
    console.log(this.value)
  }
}
</script>
<style lang="sass">
  .personalGrid-header-table
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

    /* this is when the loading indicator appears */
    &.q-table--loading thead tr:last-child th
      /* height of all previous header rows */
      top: 48px
</style>
