<template>
  <q-item class="row center" >
        <q-table
          class="col dashboardGridPlanif-header-table"
          dense
          :pagination.sync="pagination"
          :rows-per-page-options="[0]"
          :virtual-scroll-sticky-size-start="48"
          row-key="id"
          :data="registrosSeleccionados"
          :columns="columns"
          table-style="max-height: 70vh; max-width: 93vw"
          hide-bottom
        >

          <template v-slot:header="props">
            <!-- CABECERA DE LA TABLA -->
            <q-tr :props="props">
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

          <template v-slot:bottom-row>
            <!-- BOTTOM-ROW DE LA TABLA -->
            <q-tr >
              <q-th
                v-for="col in columns"
                :key="col.name"
                :align="col.align"
              >
                <div v-if="['persBunga'].includes(col.name)">{{ registrosSeleccionados.reduce((a, b) => a + (parseFloat(b.persBunga) || 0), 0) | numeralFormat }}</div>
                <div v-if="['persCamAdult'].includes(col.name)">{{ registrosSeleccionados.reduce((a, b) => a + (parseFloat(b.persCamAdult) || 0), 0) | numeralFormat }}</div>
                <div v-if="['persViajeros'].includes(col.name)">{{ registrosSeleccionados.reduce((a, b) => a + (parseFloat(b.persViajeros) || 0), 0) | numeralFormat }}</div>
                <div v-if="['parcOcupadas'].includes(col.name)">{{ registrosSeleccionados.reduce((a, b) => a + (parseFloat(b.parcOcupadas) || 0), 0) | numeralFormat }}</div>
                <div v-if="['bungaOcupados'].includes(col.name)">{{ registrosSeleccionados.reduce((a, b) => a + (parseFloat(b.bungaOcupados) || 0), 0) | numeralFormat }}</div>
              </q-th>
            </q-tr>
          </template>

        </q-table>
  </q-item>
</template>

<script>
import { date } from 'quasar'
export default {
  props: ['value'], // en 'value' tenemos la tabla de datos del grid
  data () {
    return {
      rowId: '',
      ltab: 'Semanal',
      registrosSeleccionados: [],
      columns: [
        { name: 'dia', required: true, label: 'Dia', align: 'right', field: 'dia', format: val => date.formatDate(val, 'DD-MM-YYYY') },
        { name: 'pais', required: true, label: 'Pais', align: 'right', field: 'pais' },
        { name: 'provincia', required: true, label: 'Provincia', align: 'right', field: 'provincia' },
        { name: 'persViajeros', required: true, label: 'persViajeros', align: 'right', field: 'persViajeros' },
        { name: 'persBunga', required: true, label: 'persBunga', align: 'right', field: 'persBunga' },
        { name: 'bungaOcupados', required: true, label: 'bungaOcupados', align: 'right', field: 'bungaOcupados' },
        { name: 'persCamAdult', required: true, label: 'persCamAdult', align: 'right', field: 'persCamAdult' },
        { name: 'tiendasOcupadas', required: true, label: 'Tiendas', align: 'right', field: 'tiendasOcupadas' },
        { name: 'caravanasOcupadas', required: true, label: 'Carav/CC', align: 'right', field: 'caravanasOcupadas' },
        { name: 'guardaCaravana', required: true, label: 'Guarda C', align: 'right', field: 'guardaCaravana' }
        // { name: 'prealComprometidoAnyoSig', required: true, label: '%R+C 1Y', align: 'right', field: 'prealComprometidoAnyoSig', format: val => parseFloat(val).toFixed(2) }
      ],
      pagination: { rowsPerPage: 0 }
    }
  },
  methods: {
    getGridEstadisticas (objFilter) { // lo duplico de dashboardPanelDatos, me hace falta para los totales del grid
      // cards resumen de rentabilidad y patrimonio actual
      this.$axios.get('estancias/bd_estancias.php/findGridEstadis/', { params: objFilter })
        .then(response => {
          var resData = response.data
          var result = []
          resData.forEach(row => {
            if (row.provincia === 'ALICANTE' || row.provincia === 'CASTELLON' || row.provincia === 'VALENCIA') row.provincia = 'C.VALENCIANA'
            if (row.provincia === 'HUESCA' || row.provincia === 'TERUEL' || row.provincia === 'ZARAGOZA') row.provincia = 'ARAGON'
            if (row.provincia === 'BARCELONA' || row.provincia === 'TARRAGONA' || row.provincia === 'LERIDA' || row.provincia === 'LLEIDA' || row.provincia === 'GIRONA' || row.provincia === 'GERONA') row.provincia = 'CATALUÑA'
          })
          resData.sort((a, b) => (a.pais + a.provincia).localeCompare(b.pais + b.provincia))
          // para cada dia
          for (var d = new Date(objFilter.fechaDesde); d <= new Date(objFilter.fechaHasta); d.setDate(d.getDate() + 1)) {
            var provAnt = 'xx'
            var paisAnt = 'xx'
            resData.forEach(row => {
              if (paisAnt !== row.pais || provAnt !== row.provincia) {
                result.push({
                  id: d + row.pais + row.provincia,
                  dia: new Date(d),
                  pais: row.pais,
                  provincia: row.provincia,
                  persViajeros: 0, // que entran ese dia
                  persBunga: 0, // contar las personas de la estancia
                  persCamAdult: 0, // contar idserv 6
                  tiendasOcupadas: 0, // idserv: 8,9,10, tiendas, caravanas, coche-cama
                  caravanasOcupadas: 0, // idserv: 8,9,10, tiendas, caravanas, coche-cama
                  guardaCaravana: 0, // idser: 16,15 guarda parcela, parking
                  bungaOcupados: 0 // tipoServ: 1,2,6,7
                })
              }
              paisAnt = row.pais
              provAnt = row.provincia
            })
          }
          result.forEach(rowResult => {
            var dataFilter = resData.filter(rowData => (rowResult.pais === rowData.pais && rowResult.provincia === rowData.provincia &&
                                    date.formatDate(rowResult.dia, 'YYYY-MM-DD 00:00:00') >= rowData.fechaInicio &&
                                    date.formatDate(rowResult.dia, 'YYYY-MM-DD 00:00:00') <= rowData.fechaFin))
            dataFilter.forEach(row => {
              if (date.formatDate(rowResult.dia, 'YYYY-MM-DD') === row.fechaInicio.substring(0, 10)) {
                if (row.idServicio === '6' || row.idServicio === '7') rowResult.persViajeros += parseFloat(row.cantidad) // 6 adul, 7 niños
                if (row.tipoServicio === '1' || row.tipoServicio === '2' || row.tipoServicio === '6' || row.tipoServicio === '7') rowResult.persViajeros += (parseFloat(row.persBunga) === 0 ? 1 : parseFloat(row.persBunga))
              }
              if (date.formatDate(rowResult.dia, 'YYYY-MM-DD') !== row.fechaFin.substring(0, 10) &&
                (row.tipoServicio === '1' || row.tipoServicio === '2' || row.tipoServicio === '6' || row.tipoServicio === '7')) {
                rowResult.persBunga += (parseFloat(row.persBunga) === 0 ? 1 : parseFloat(row.persBunga))
                rowResult.bungaOcupados += parseFloat(row.cantidad)
              }
              if (row.idServicio === '6' || row.idServicio === '7') rowResult.persCamAdult += parseFloat(row.cantidad) // 6 adul, 7 niños
              if (row.idServicio === '8') rowResult.tiendasOcupadas += parseFloat(row.cantidad)
              if (row.idServicio === '9' || row.idServicio === '10') rowResult.caravanasOcupadas += parseFloat(row.cantidad)
              if (row.idServicio === '15' || row.idServicio === '16') rowResult.guardaCaravana += parseFloat(row.cantidad)
            })
          })
          this.registrosSeleccionados = result
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error.message })
        })
    },
    mostrarDatosPieTabla () {
      return this.registrosSeleccionados.length + ' Filas'
    }
  },
  mounted () {
    this.getGridEstadisticas(this.value) // carga panel de datos, me hace falta para totales grid
  }
}
</script>
<style lang="sass">
  .dashboardGridPlanif-header-table
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
