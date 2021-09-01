  <!-- componente que se llama desde accionesMain y que presenta el formulario de filtro y el boton de busqueda -->
  <template>
  <q-card style="width: 400px;" class="q-pr-xs q-gutter-xs">
    <q-card-section class="bg-primary text-white">
      <div class="text-h6">Filtrar por</div>
    </q-card-section>

    <q-form @submit="getRecords" class="q-gutter-y-xs">
      <q-input
        outlined
        clearable
        label="Fecha Desde"
        stack-label
        :value="formatDate(filterR.fechaDesde)"
        @input="(val) => filterR.fechaDesde=val"
        class="q-mb-sm"
      >
        <template v-slot:append>
            <q-icon name="event" class="cursos-pointer">
              <q-popup-proxy ref="fechaDesde">
                <wgDate
                  v-model="filterR.fechaDesde"
                  @input="$refs.fechaDesde.hide()"/>
              </q-popup-proxy>
            </q-icon>
        </template>
      </q-input>
      <q-input
        outlined
        clearable
        label="Fecha Hasta"
        stack-label
        :value="formatDate(filterR.fechaHasta)"
        @input="(val) => filterR.fechaHasta=val"
      >
        <template v-slot:append>
            <q-icon name="event" class="cursos-pointer">
              <q-popup-proxy ref="fechaHasta">
                <wgDate
                  v-model="filterR.fechaHasta"
                  @input="$refs.fechaHasta.hide()"/>
              </q-popup-proxy>
            </q-icon>
        </template>
      </q-input>
      <q-card-actions align="right">
        <q-btn  flat type="submit" label="Buscar" color="primary"/>
        <q-btn  flat label="Cancel" color="primary" @click="$emit('hide')"/><!-- lo captura accionesMain -->
      </q-card-actions>
  </q-form>
  </q-card>
</template>

<script>
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'
export default {
  props: ['value'], // value es el objeto con los campos de filtro que le pasa accionesMain con v-model
  data () {
    return {
      filterR: {}
    }
  },
  methods: {
    getRecords () {
      this.$emit('getRecords', this.filterR) // lo captura accionesMain
    },
    formatDate (pdate) {
      return date.formatDate(pdate, 'DD-MM-YYYY')
    }
  },
  components: {
    wgDate: wgDate
  },
  mounted () { // mounted se llama cada vez que toma el foco este componente, y destroyed cada vez que lo pierde
    this.filterR = Object.assign({}, this.value) // asignamos valor del parametro por si viene de otro tab
  },
  destroyed () {
    // guardamos valor en tabs por si despus queremos recuperarlo
    this.$emit('input', this.filterR)
  }
}
</script>
