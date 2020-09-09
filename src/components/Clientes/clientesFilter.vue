<!-- componente que se llama desde personalMain y que presenta el formulario de filtro y el boton de busqueda -->
  <template>
  <q-card style="width: 400px;" class="q-pr-xs q-gutter-xs">
    <q-card-section class="bg-primary text-white">
      <div class="text-h6">Filtrar por</div>
    </q-card-section>
    <q-form @submit="getRecords" class="q-gutter-y-xs">
    <q-tab-panels v-model="tab">
      <q-tab-panel name="General">
        <q-input outlined clearable label="Cliente" stack-label autofocus v-model="filterR.nombre" />
        <q-input outlined clearable label="Teléfono" stack-label v-model="filterR.telefono" />
        <q-input outlined clearable label="DNI/Pasaporte" stack-label v-model="filterR.dni" />
        <!-- <q-input outlined clearable label="Servicios Periódicos" stack-label v-model="filterR.paisTrab" /> -->
        <q-input outlined clearable label="Orden ID" stack-label v-model="filterR.id" />
      </q-tab-panel>
    </q-tab-panels>
      <q-card-actions align="right">
        <q-btn  flat type="submit" label="Buscar" color="primary"/>
        <q-btn  flat label="Cancel" color="primary" @click="$emit('close')"/><!-- lo captura accionesMain -->
      </q-card-actions>
  </q-form>
  </q-card>
</template>

<script>
export default {
  props: ['value'], // Prop Value pasada por el componente clienteFilter
  data () {
    return {
      tab: 'General',
      filterR: {}
    }
  },
  methods: {
    getRecords () {
      this.$emit('getRecords', this.filterR) // lo captura accionesMain
    }
  },
  mounted () {
    this.filterR = this.value // asignamos valor del parametro por si viene de otro tab
  },
  destroyed () {
    // guardamos valor en tabs por si despus queremos recuperarlo
    this.$emit('input', this.filterR)
  }
}
</script>
