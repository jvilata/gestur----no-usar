<!-- componente principal de definicion de formularios. Se apoya en otros 2 componentes: Filter y Grid -->
  <template>
    <div style="height: 100vh">
      <q-item clickable v-ripple class="q-ma-md q-pa-xs bg-blue-grey-1 text-grey-8">
        <!-- cabecera de formulario. Botón de busqueda y cierre de tab -->
        <q-item-section avatar>
          <q-icon name="create" />
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-h6">
            {{ nomFormulario }}
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
      <!-- formulario tabla de resultados de busqueda -->
      <tarifasGrid
        v-model="registrosSeleccionados"
        />
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import tarifasGrid from 'components/TarifasServicios/tarifasGrid.vue'
export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      nomFormulario: 'Tarifas Servicios',
      registrosSeleccionados: []
    }
  },
  computed: {
    ...mapState('login', ['user']),
    ...mapState('tarifas', ['listaTarifas'])
  },
  methods: {
    ...mapActions('tarifas', ['loadListaTarifas'])
  },
  mounted () {
    // cuando se haga la llamada al back-end en el store-tarifas: se hará la llamada corrspondiente:
    this.loadListaTarifas()
      .then(result => {
        this.registrosSeleccionados = result.data
      })
      .catch(error => {
        this.$q.dialog({
          message: error.message
        })
      })
  },
  components: {
    tarifasGrid: tarifasGrid
  }
}
</script>
