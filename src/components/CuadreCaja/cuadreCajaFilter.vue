<!-- componente que se llama desde personalMain y que presenta el formulario de filtro y el boton de busqueda -->
  <template>
  <q-card style="width: 400px;" class="q-pr-xs q-gutter-xs">
    <q-card-section class="bg-primary text-white">
      <div class="text-h6">Filtrar por</div>
    </q-card-section>
    <q-form @submit="getRecords" class="q-gutter-y-xs">
        <q-input label="Fecha Inicio" class="col-xs-12 col-sm-5" clearable outlined stack-label :value="formatDate(filterR.fechaInicial)">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaInicial">
                  <wgDate
                      @input="$refs.fechaInicial.hide()"
                      v-model="filterR.fechaInicial" />
              </q-popup-proxy>
              </q-icon>
          </template>
        </q-input>
        <q-input label="Fecha Fin" class="col-xs-12 col-sm-5" clearable outlined stack-label :value="formatDate(filterR.fechaCierre)">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaCierre">
                  <wgDate
                      @input="$refs.fechaCierre.hide()"
                      v-model="filterR.fechaCierre" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
        <q-input class="col-xs-6 col-sm-4" outlined stack-label v-model="filterR.facturacionPeriodo" label="Facturación Periodo"/>
        <q-input class="col-xs-6 col-sm-4" outlined stack-label v-model="filterR.recaudacionCaja" label="Recaudación Caja"/>
        <q-input class="col-xs-12 col-sm-4" outlined stack-label v-model="filterR.gastosCajaPeriodo" label="Gastos Caja"/>
        <q-input class="col-xs-2 col-sm-4" outlined stack-label v-model="filterR.cajaPendiente" label="Caja a Ingresar"/>
        <q-input outlined clearable label="ID" stack-label v-model="filterR.observaciones" />
      <q-card-actions align="right">
        <q-btn  flat type="submit" label="Buscar" color="primary"/>
        <q-btn  flat label="Cancel" color="primary" @click="$emit('close')"/>
      </q-card-actions>
  </q-form>
  </q-card>
</template>

<script>
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'
export default {
  props: ['value'],
  data () {
    return {
      filterR: {}
    }
  },
  methods: {
    getRecords () {
      this.$emit('getRecords', this.filterR)
    },
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    }
  },
  mounted () {
    this.filterR = this.value
  },
  components: {
    wgDate: wgDate
  }
}
</script>
