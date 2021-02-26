<template>
    <div style="height: calc(100vh - 113px)">
      <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-xs q-pa-xs bg-blue-grey-1 text-grey-8" >
        <q-item-section avatar>
          <q-icon name="search" size="md"/>
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-h6 ">
            {{ nomFormulario }}
          </q-item-label>
          <q-item-label>
            <small>{{ Object.keys(filterRecord).length ? filterRecord : 'Pulse para definir filtro' }}</small>
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
            @getRecords="(val) => getRecords(val)"
            @close="expanded = !expanded"
        />
        </q-dialog>
      <!-- Tabla de resultados de busqueda -->
      <clientesGrid
        fromClientesMain=true
        v-model="registrosSeleccionados"
        />
    </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  props: ['value', 'id', 'keyValue'],

  data () {
    return {
      nomFormulario: 'Lista de Clientes',
      expanded: false,
      filterRecord: {},
      registrosSeleccionados: [] // Array de Objetos (Clientes)
    }
  },
  methods: {
    ...mapActions('login', ['desconectarLogin']),
    ...mapActions('clientes', ['loadListaClientes']),
    getRecords (filterR) {
      Object.assign(this.filterRecord, filterR) // no haría falta pero así obliga a refrescar el componente para que visulice el filtro
      // llamada al backend
      this.loadListaClientes(filterR)
        .then(response => {
          this.registrosSeleccionados = response.data
          this.expanded = false
        })
        .catch(error => {
          this.registrosSeleccionados = {}
          this.$q.dialog({ title: 'Error', message: error })
          this.desconectarLogin()
        })
    }
  },
  destroyed () {
    this.$emit('changeTab', { idTab: this.value.idTab, filterRecord: this.filterRecord })
  },
  mounted () {
    if (this.value.filterRecord) { // FilterRecord tiene valores porque ya hemos hecho una búsqueda
      this.expanded = false // Escondemos clientesFilter
      this.getRecords(this.value.filterRecord) // refresco la lista por si se han hecho cambios
    } else { // no esta inicializado
      this.registrosSeleccionados = []
    }
  },
  components: {
    clientesFilter: require('components/Clientes/clientesFilter.vue').default,
    clientesGrid: require('components/Clientes/clientesGrid.vue').default
  }
}
</script>
