<!-- componente que se llama desde accionesMain y que presenta el formulario de filtro y el boton de busqueda -->
  <template>
  <q-card style="max-width: 800px;" class="q-pr-xs q-gutter-xs">
    <q-form @submit="saveForm" @keyup.esc="$emit('close')">
      <q-card-section class="row bg-primary text-white">
        <div class="text-h6">{{title}}</div>
        <q-space />
        <q-btn @click="$emit('close')" flat round dense icon="close"/>
      </q-card-section>
      <q-card-section>
        <div class="row q-mb-md">
          <q-input class="col-xs-4 col-sm-2" outlined label="Número" stack-label v-model="recordToSubmit.Numero" />
        </div>
        <div class="row">
          <q-input label="Fecha Inicio" class="col-xs-12 col-sm-5" clearable outlined stack-label :value="formatDate(recordToSubmit.fechaInicio)">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaIni">
                  <wgDate
                      @input="$refs.fechaIni.hide()"
                      v-model="recordToSubmit.fechaInicio" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
          <q-input label="Fecha Fin" class="col-xs-12 col-sm-5" clearable outlined stack-label :value="formatDate(recordToSubmit.fechaFin)">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaFin">
                  <wgDate
                      @input="$refs.fechaFin.hide()"
                      v-model="recordToSubmit.fechaFin" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
          <q-input class="col-xs-4 col-sm-2" outlined label="Noches" stack-label v-model="recordToSubmit.noches" />
          <q-input class="col-xs-8 col-sm-3" outlined stack-label v-model="recordToSubmit.cantidad" label="Cantidad" @blur="calcularTotal"/>
          <q-input class="col-xs-12 col-sm-3" outlined stack-label v-model="recordToSubmit.tarifa" label="Tarifa" @blur="calcularTotal"/>
          <q-input class="col-xs-6 col-sm-3" outlined stack-label v-model="recordToSubmit.tipoIva" label="%IVA" @blur="calcularTotal"/>
          <q-input class="col-xs-6 col-sm-3" outlined stack-label v-model="recordToSubmit.dto" label="Dto" @blur="calcularTotal"/>
        </div>
        <div class="row q-mt-xl q-mb-md">
          <q-input class="col-xs-6 col-sm-4" outlined readonly stack-label v-model="totalBruto" label="Total Bruto"/>
          <q-input class="col-xs-6 col-sm-4" outlined readonly stack-label v-model="totalNeto" label="Total Neto"/>
          <q-input class="col-xs-12 col-sm-4" outlined readonly stack-label v-model="recordToSubmit.total" label="Total"/>
        </div>
        <div class="row">
          <q-input class="col-xs-8 col-sm-9" outlined stack-label v-model="recordToSubmit.comentarios" label="Comentarios"/>
        </div>
      </q-card-section>
      <q-card-actions align=right>
        <q-btn type="submit" label="Save" color="primary"/>
        <q-btn @click="$emit('close')" label="Cancel" color="negative"/>
      </q-card-actions>
    </q-form>
  </q-card>
</template>

<script>
import { mapState } from 'vuex'
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'
export default {
  props: ['value', 'cabecera'], // value es el objeto con los campos de filtro que le pasa accionesMain con v-model
  data () {
    return {
      title: 'Detalle línea',
      totalBruto: 0,
      totalNeto: 0,
      recordToSubmit: { }
    }
  },
  computed: {
    ...mapState('servicios', ['listaServicios'])
  },
  methods: {
    saveForm () {
      this.$emit('saveRecord', this.recordToSubmit) // lo captura estanciasFormLineas
    },
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    },
    calcularTotal () {
      const tarifa = (this.recordToSubmit.tarifa) / (1 + (this.recordToSubmit.tipoIva / 100)) // tarifa sin IVA
      this.totalBruto = Math.round(this.recordToSubmit.cantidad * this.recordToSubmit.noches * tarifa * 100) / 100
      this.totalNeto = Math.round(this.totalBruto * 100 * (1 - this.recordToSubmit.dto / 100)) / 100
      this.recordToSubmit.total = Math.round(this.totalNeto * 100 * (1 + this.recordToSubmit.tipoIva / 100)) / 100
    }
  },
  components: {
    wgDate: wgDate
  },
  mounted () {
    this.recordToSubmit = Object.assign({}, this.value) // asignamos valor del parametro por si viene de otro tab
    this.calcularTotal()
  }
}
</script>
