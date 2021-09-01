  <!-- componente principal de definicion de formularios. Se apoya en otros 2 componentes: Filter y Grid -->
  <template>
    <div style="height: calc(100vh - 105px)" >
      <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-md q-pa-xs bg-indigo-1 text-grey-8">
        <!-- cabecera de formulario. Botón de busqueda y cierre de tab -->
        <q-item-section avatar>
          <q-icon name="fas fa-filter" />
        </q-item-section>
        <q-item-section >
          <q-item-label class="text-h6">
            {{ nomFormulario }}
          </q-item-label>
          <q-item-label>
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
        <dashboardFilter
          :value="filterRecord"
          @input="(value) => Object.assign(filterRecord, value)"
          @getRecords="getRecords"
          @hide="expanded = !expanded"
        />
      </q-dialog>

      <q-tab-panels v-model="ltab" animated >
          <q-tab-panel v-for="(tab, index) in menuItems" :key="index" :name="tab.link.name"  class="q-pa-none">
            <router-view :value="filterRecord" :key="refreshRec" :keyValue="refreshRec"/>
          </q-tab-panel>
      </q-tab-panels>
      <!-- podemos poner tabs en el pie para dispositivos moviles pero quita pantalla y no me gusta -->
      <q-tabs v-model="ltab" dense
        class="absolute-bottom bg-primary text-white">
        <q-route-tab v-for="(tab,index) in menuItems"
          no-caps
          :key="index"
          :icon="tab.icon"
          :label="tab.title"
          :name="tab.link.name"
          :to="{ name: tab.link.name }"
          exact />
      </q-tabs>
    </div>
</template>

<script>
import Vue from 'vue'
import { date } from 'quasar'
import { mapState } from 'vuex'
import dashboardFilter from 'components/Dashboard/dashboardFilter.vue'
export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      expanded: false,
      visible: '',
      refreshRec: 0,
      filterRecord: {},
      ltab: '',
      nomFormulario: 'Dashboard',
      menuItems: [
        {
          title: 'Panel Datos',
          // caption: '',
          icon: 'table_chart',
          link: { name: 'gridEstadis' }
        },
        {
          title: 'Evolución Fact. Meses',
          icon: 'show_chart',
          link: { name: 'evolucionFacturacionMes' }
        },
        {
          title: 'Evolución Fact. Años',
          icon: 'show_chart',
          link: { name: 'evolucionFacturacionAnyos' }
        }
      ],
      registrosPatrimonioEntidad: [],
      registrosPatrimonioComprometido: []
    }
  },
  computed: {
    ...mapState('login', ['user'])
  },
  methods: {
    getRecords (filter, saltar = false) { // saltar marcara si tenemos que saltar la carga del grid activosInverison
      Object.assign(this.filterRecord, filter) // no haría falta pero así obliga a refrescar el componente para que visulice el filtro
      this.expanded = false
      this.ltab = 'gridEstadis'
      this.refreshRec++ // obliga a refrescar el componente para que cargue los nuevos datos
    }
  },
  mounted () {
    Vue.set(this.filterRecord, 'codEmpresa', this.user.codEmpresa) // añado campo codEmpresa
    if (this.value !== undefined && this.value.registrosSeleccionados && Object.keys(this.value.registrosSeleccionados).length > 0) { // si ya hemos cargado previamente los recargo al volver a este tab
      this.expanded = false
      // this.registrosSeleccionados = Object.values(this.value.registrosSeleccionados) // v-model: en 'value' podemos leer el valor del v-model
      Object.assign(this.filterRecord, this.value.filterRecord)
      this.getRecords(this.filterRecord, true) // carga datos de panel datos, saltandose la carga del grid de activosInversion
    } else { // es la primera vez que entro, cargo valores po defecto
      Vue.set(this.filterRecord, 'fechaDesde', date.formatDate(new Date(), 'YYYY-MM-DD'))
      Vue.set(this.filterRecord, 'fechaHasta', date.formatDate(new Date(), 'YYYY-MM-DD'))
      this.$router.push({ name: 'gridEstadis' })
      this.getRecords(this.filterRecord)
    }
  },
  destroyed () {
    if (this.value !== undefined) {
      this.$emit('changeTab', { id: this.value.id, filterRecord: Object.assign({}, this.filterRecord), registrosSeleccionados: {} })
    }
  },
  components: {
    dashboardFilter: dashboardFilter
  }
}
</script>
