 <template>
    <div style="height: 100vh">
      <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-md q-pa-xs bg-blue-grey-1 text-grey-8">
        <!-- cabecera de formulario. Botón de busqueda y cierre de tab -->
        <q-item-section avatar>
          <q-icon name="create" />
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-h6">
            {{ nomFormulario }}
          </q-item-label>
          <q-item-label>
            <!-- poner un campo de fiterRecord que exista en este filtro -->
            <small>{{ Object.keys(filterRecord).length > 0 ? filterRecord : 'Pulse para definir filtro' }}</small>
          </q-item-label>
        </q-item-section>
        <q-item-section side>
          <q-btn
          @click="$emit('close')"
          flat
          round
          dense
          icon="close"/>
        </q-item-section>
      </q-item>

      <q-dialog v-model="expanded"  >
        <!-- formulario con campos de filtro -->
        <usuariosFilter
          :value="filterRecord"
          @getRecords="(val) => getRecords(val)"
          @hide="expanded = !expanded"
        />
      </q-dialog>

      <!-- formulario tabla de resultados de busqueda -->
      <usuariosGrid
        v-model="registrosSeleccionados"
      />
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import usuariosFilter from 'components/Usuarios/usuariosFilter.vue'
import usuariosGrid from 'components/Usuarios/usuariosGrid.vue'
export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      expanded: false,
      visible: '',
      filterRecord: {},
      nomFormulario: 'Usuarios',
      registrosSeleccionados: []
    }
  },
  computed: {
    ...mapState('login', ['user']) // importo state.user desde store-login
  },
  methods: {
    ...mapActions('login', ['loadListaUsuarios']),
    getRecords (filter) {
      // hago la busqueda de registros segun condiciones del formulario Filter que ha lanzado el evento getRecords
      var objFilter = Object.assign({}, filter)
      this.loadListaUsuarios(objFilter)
        .then(response => {
          this.registrosSeleccionados = response.data
          this.expanded = false
        })
        .catch(error => {
          this.$q.dialog({ title: 'Error', message: error })
        })
    }
  },
  mounted () {
    if (this.value.registrosSeleccionados) { // si ya hemos cargado previamente los recargo al volver a este tab
      this.expanded = false
      Object.assign(this.filterRecord, this.value.filterRecord)
      this.getRecords(this.filterRecord)
    } else { // es la primera vez que entro, cargo valores po defecto
      this.filterRecord = { }
      this.getRecords(this.filterRecord)
    }
  },
  components: {
    usuariosFilter: usuariosFilter,
    usuariosGrid: usuariosGrid
  }
}
</script>
