<template>
    <div style="height: calc(100vh - 113px)">
      <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-xs q-pa-xs bg-blue-grey-1 text-grey-8" >
        <q-item-section avatar>
          <q-icon name="search" size="md"/>
        </q-item-section>
        <q-item-section class="text-center">
          <q-item-label class="text-h6 ">
            {{ nomFormulario }}
          </q-item-label>
        </q-item-section>
        <q-item-section side>
          <q-btn
          @click="$emit('close')" flat round dense icon="close"/>
        </q-item-section>
      </q-item>
        <q-dialog v-model="expanded"  >
        <!-- Componente de filtro -->
        <clientesFilter
            :value="filterRecord"
            @input="(value) => Object.assign(filterRecord, value)"
            @getRecords="getRecords"
            @close="expanded = !expanded"
        />
        </q-dialog>
      <!-- Tabla de resultados de busqueda -->
      <clientesGrid
        v-model="registrosSeleccionados"
        />
    </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
//   props: ['value', 'id', 'keyValue'],

  data () {
    return {
      nomFormulario: 'Lista de Clientes',
      expanded: false,
      filterRecord: {},
      registrosSeleccionados: [
        { // Array de Objetos (Clientes)
          id: '1',
          nombre: 'Marta Vilata',
          telefono: '+34 674078554',
          dni: '21793010G',
          nacionalidad: 'ES',
          matricula: '1376GTJ',
          pendCobro: '1/3/2020'
        },
        {
          id: '2',
          nombre: 'Ana Vilata',
          telefono: '+34 639677128',
          dni: '21793009A',
          nacionalidad: 'ES',
          matricula: '1376GTJ',
          pendCobro: '1/5/2020'
        }
      ]
    }
  },
  methods: {
    ...mapActions('login', ['desconectarLogin']),
    getRecords (filterR) {
      // hago la busqueda de registros segun condiciones del formulario Filter que ha lanzado el evento getRecords
      Object.assign(this.filterRecord, filterR) // no haría falta pero así obliga a refrescar el componente para que visulice el filtro
      this.expanded = false
      this.registrosSeleccionados = filterR
    //   this.loadListaDetalleEmpleados(filterR)
    //     .then(response => {
    //       this.registrosSeleccionados = response.data.root
    //       this.expanded = false
    //     })
    //     .catch(error => {
    //       this.$q.dialog({ title: 'Error', message: error.response.statusText })
    //       this.desconectarLogin()
    //     })
    }
  },
  destroyed () {
    this.$emit('changeTab', { idTab: this.value.idTab, filterRecord: this.filterRecord })
  },
  components: {
    clientesFilter: require('components/Clientes/clientesFilter.vue').default,
    clientesGrid: require('components/Clientes/clientesGrid.vue').default
  }
}
</script>
