<template>
    <div style="height: calc(100vh - 105px)">
      <q-item clickable v-ripple class="q-ma-md q-pa-xs bg-blue-grey-1 text-grey-8">
        <!-- cabecera de formulario. Botón de busqueda y cierre de tab -->
        <q-item-section avatar>
          <q-icon name="today" />
        </q-item-section>
        <q-item-section>
          <div class="row">
            <q-item-label class="text-h6">
              {{ nomFormulario }}
            </q-item-label>
              <q-select class="q-ml-lg col-xs-5 col-sm-3" outlined dense label="Mes" stack-label
                @input="getRecords()"
                v-model="filterRecord.mes"
                :options="listaMeses"
                option-value="idmes"
                option-label="mes"
                emit-value
                map-options/>
              <q-select class="q-ml-xs col-xs-5 col-sm-2" outlined dense label="Anyo" stack-label
                @input="getRecords()"
                v-model="filterRecord.anyo"
                :options="listaAnyos"
                option-value="anyo"
                option-label="anyo"
                emit-value
                map-options/>
              <q-select class="q-ml-xs col-xs-5 col-sm-2" outlined dense label="Ver por" stack-label
                @input="gestionarVerPor()"
                v-model="filterRecord.verPor"
                :options="['Por días', 'Por Servicios']"/>
          </div>
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
      <calendarioGrid v-if="verPorDias"
        :filterRecord="filterRecord"
        :key="refreshKey"
        />
      <calendarioPorBungGrid v-if="!verPorDias"
        :filterRecord="filterRecord"
        :key="refreshKey"
        />
    </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
  props: ['value', 'id', 'keyValue'], // se pasan como parametro desde mainTabs. value = { registrosSeleccionados: [], filterRecord: {} }
  data () {
    return {
      verPorDias: false,
      expanded: false,
      refreshKey: 0,
      visible: '',
      listaMeses: [],
      listaAnyos: [],
      filterRecord: {},
      nomFormulario: 'Calendario Reservas',
      registrosSeleccionados: []
    }
  },
  computed: {
    ...mapState('login', ['user']) // importo state.user desde store-login
  },
  methods: {
    gestionarVerPor () {
      if (this.filterRecord.verPor === 'Por días') this.verPorDias = true
      else this.verPorDias = false
    },
    cargaMesesAnyos () {
      for (let i = 1; i <= 12; i++) {
        var dateObj = new Date('2021-' + i + '-01')
        this.listaMeses.push({ idmes: i, mes: dateObj.toLocaleString('es-ES', { month: 'long' }) })
      }
      var d = new Date()
      for (let i = d.getFullYear() - 3; i < d.getFullYear() + 3; i++) {
        this.listaAnyos.push(i)
      }
    },
    getRecords () {
      this.filterRecord.verPor = 'Por Servicios'
      this.refreshKey++
      this.expanded = false
    }
  },
  mounted () {
    this.filterRecord = { mes: (new Date()).getMonth() + 1, anyo: (new Date()).getFullYear() }
    this.cargaMesesAnyos()
    this.getRecords()
  },
  destroyed () {
    this.$emit('changeTab', { idTab: this.value.idTab, filterRecord: Object.assign({}, this.filterRecord), registrosSeleccionados: Object.assign({}, this.registrosSeleccionados) })
  },
  components: {
    calendarioGrid: require('components/Calendario/calendarioGrid.vue').default,
    calendarioPorBungGrid: require('components/Calendario/calendarioPorBungGrid.vue').default
  }
}
</script>
