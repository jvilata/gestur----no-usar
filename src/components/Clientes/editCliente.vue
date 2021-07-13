<template>
  <div>
    <q-item clickable v-ripple class="q-ma-xs q-pa-xs bg-blue-grey-1 text-grey-8" >
      <q-item-section avatar>
        <div class="row">
          <q-btn icon="save"  class="q-ma-xs" :color="colorBotonSave" dense @click="guardarCliente"/>
        </div>
      </q-item-section>
      <q-item-section>
        <q-item-label class="text-h6">
            {{ `CLIENTE - ${ keyValue }` }}
        </q-item-label>
      </q-item-section>
      <q-item-section side>
          <q-btn
          @click="confirmarCierre"
          flat
          round
          dense
          icon="close"/>
      </q-item-section>
    </q-item>
    <q-card flat>
      <q-card-section  class="q-pt-none q-pl-xs q-pr-xs">
        <div class="row q-mb-sm">
          <q-input autogrow outlined clearable label="Nombre" v-model="cliente.nombre" class="col-xs-4 col-sm-4" @blur="cambiaDatos"/>
          <q-input autogrow outlined clearable label="Primer Apellido" v-model="cliente.apellido1" class="col-xs-4 col-sm-4" @blur="cambiaDatos"/>
          <q-input autogrow outlined clearable label="Segundo Apellido" v-model="cliente.apellido2" class="col-xs-4 col-sm-4" @blur="cambiaDatos"/>
        </div>
        <div class="row q-mb-sm">
          <q-input outlined autogrow clearable label="Email" v-model="cliente.email" class="col-xs-7 col-sm-7" @blur="cambiaDatos" />
          <q-input outlined autogrow clearable label="Matrícula" v-model="cliente.matricula" class="col-xs-5 col-sm-5" @blur="cambiaDatos"/>
        </div>
        <div class="row q-mb-sm">
          <q-input outlined autogrow clearable label="Dirección" v-model="cliente.direccion" class="col-xs-12 col-sm-8" @blur="cambiaDatos"/>
          <q-input outlined autogrow clearable label="Población" v-model="cliente.poblacion" class="col-xs-8 col-sm-4" @blur="cambiaDatos"/>
          <q-input outlined autogrow clearable label="C.Postal" v-model="cliente.cpostal" class="col-xs-4 col-sm-2" @blur="cambiaDatos"/>
          <q-input outlined autogrow clearable label="Provincia" v-model="cliente.provincia" class="col-xs-8 col-sm-6" @blur="cambiaDatos"/>
          <q-input outlined autogrow clearable label="País" v-model="cliente.pais" class="col-xs-4 col-sm-4" @blur="cambiaDatos"/>
        </div>
        <div class="row q-mb-sm">
          <q-input outlined clearable label="Teléfonos" v-model="cliente.telefonos" class="col-xs-12 col-sm-12" @blur="cambiaDatos"/>
        </div>
        <div class="row q-mb-sm">
          <q-select
          class="col-xs-5 col-sm-2"
          label="Tipo Doc."
          stack-label
          clearable
          outlined
          v-model="cliente.tipoDoc"
          :options="listaTipoDoc"
          map-options
          option-value="codElemento"
          option-label="valor1"
          emit-value
          @blur="cambiaDatos"
        />
          <q-input outlined clearable label="DNI/Pasaporte" v-model="cliente.nroDoc" class="col-xs-7 col-sm-4" @blur="cambiaDatos"/>
          <q-input label="Fecha Nacimiento" class="col-xs-7 col-sm-3" clearable outlined stack-label :value="formatDate(cliente.fechaNacimiento)" @input="(val) => recordToSubmit.fechaNacimiento=val" @blur="cambiaDatos" mask="##/##/####" hint="Mask: DD/MM/YYYY">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaNac">
                  <wgDate
                      @input="v => { $refs.fechaNac.hide(); recordToSubmit.fechaNacimiento=v }"
                      :value="cliente.fechaNacimiento" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
          <q-input outlined clearable label="Nacionalidad" v-model="cliente.nacionalidad" class="col-xs-5 col-sm-3" />
        </div>
        <div class="row q-mb-sm">
          <q-input label="Fecha Expedición" class="col-xs-6 col-sm-6" clearable outlined stack-label :value="formatDate(cliente.fechaExpedicion)" @input="(val) => recordToSubmit.fechaExpedicion=val" @blur="cambiaDatosExpedicion(cliente.fechaExpedicion)">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaExp">
                  <wgDate
                      @input="$refs.fechaExp.hide()"
                      v-model="cliente.fechaExpedicion" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
           <q-input label="Fecha Validez" class="col-xs-6 col-sm-6" clearable outlined stack-label :value="formatDate(cliente.fechaValidez)" @input="(val) => recordToSubmit.fechaValidez=val" @blur="cambiaDatos">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaVal">
                  <wgDate
                      @input="$refs.fechaVal.hide()"
                      v-model="cliente.fechaValidez" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
        </div>
        <div class="row q-mb-sm">
          <q-expansion-item
            expand-separator
            clearable
            icon="home"
            label="Cliente con Servicio Periódico"
            caption="Más info"
            style="width: 100%"
            header-class="bg-green-1"
          >
            <q-card>
              <q-card-section  class="q-pt-none q-pl-xs q-pr-xs">
                <div class="row q-mb-sm">
                  <q-select
                    class="col-xs-12 col-sm-5"
                    outlined
                    stack-label
                    clearable
                    label="Tipo Servicio Periódico"
                    v-model="cliente.tipoServicioPeriodico"
                    :options="listaTipoServFilter"
                    option-value="codElemento"
                    option-label="valor1"
                    emit-value
                    map-options
                    @blur="cambiaDatos"
                    @filter="filterTipoServ"
                    use-input
                    hide-selected
                    fill-input
                    input-debounce="0"/>
                  <q-select
                    standout="bg-green-3"
                    class="col-xs-7 col-sm-4"
                    stack-label
                    outlined
                    clearable
                    label="Tipo Factura"
                    v-model="cliente.tipoFacturacion"
                    :options="listaTipoFactFilter"
                    option-value="codElemento"
                    option-label="valor1"
                    emit-value
                    map-options
                    @blur="cambiaDatos"
                    @filter="filterTipoFact"
                    use-input
                    hide-selected
                    fill-input
                    input-debounce="0"
                  />
                  <q-input outlined label="Precio" v-model="cliente.precio" class="col-xs-5 col-sm-3" @blur="cambiaDatos"/>
                </div>
                <div class="row q-mt-xs">
                  Cuenta Bancaria
                </div>
                <div class="row">
                    <q-input label="C.Banco" outlined v-model="cliente.codBanco" class="col-xs-3 col-sm-3 q-pr-sm" @blur="cambiaDatos"/>
                    <q-input label="C.Sucursal" outlined v-model="cliente.codSucursal" class="col-xs-3 col-sm-3 q-pr-sm" @blur="cambiaDatos"/>
                    <q-input label="DC" outlined v-model="cliente.digitosControl" class="col-xs-2 col-sm-2 q-pr-sm" @blur="cambiaDatos"/>
                    <q-input label="Cuenta" outlined v-model="cliente.cuentaBancaria" class="col-xs-4 col-sm-4 q-pr-sm" @blur="cambiaDatos"/>
                </div>
              </q-card-section>
            </q-card>
         </q-expansion-item>
        </div>
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { date } from 'quasar'
import wgDate from 'components/General/wgDate.vue'
export default {
  props: ['value', 'id', 'keyValue'],
  data () {
    return {
      cliente: {
        fechaValidez: ''
      },
      colorBotonSave: 'primary',
      refresh: 0,
      recordToSubmit: {},
      hasChanges: false,
      listaTipoServFilter: this.listaTipoServ,
      listaTipoFactFilter: this.listaTipoFact
    }
  },
  computed: {
    ...mapState('login', ['user']),
    ...mapState('tablasAux', ['listaTipoDoc', 'listaTipoFact', 'listaTipoServ'])
  },
  methods: {
    ...mapActions('login', ['desconectarLogin']),
    ...mapActions('clientes', ['loadDetallecliente', 'guardarDatosCliente']),
    ...mapActions('tabs', ['addTab']),
    filterTipoServ (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.listaTipoServFilter = this.listaTipoServ.filter(v => v.valor1.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterTipoFact (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.listaTipoFactFilter = this.listaTipoFact.filter(v => v.valor1.toLowerCase().indexOf(needle) > -1)
      })
    },
    openForm (otrosCambios) {
      this.addTab(['clientesMain', 'clientesMain', {}, 1])
    },
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    },
    confirmarCierre () {
      if (this.hasChanges) {
        this.$q.dialog({
          title: 'Aviso',
          message: '¿Desea guardar los cambios?',
          ok: true,
          cancel: true,
          persistent: true
        }).onOk(() => {
          this.guardarCliente()
          this.$emit('close')
        }).onCancel(() => {
          this.$emit('close')
        })
      } else { this.$emit('close') }
    },
    guardarCliente () {
      if (this.hasChanges) {
        this.$q.dialog({
          title: 'Confirmar cambios',
          message: '¿ Desea guardar los cambios ?',
          ok: true,
          cancel: true,
          persistent: true
        }).onOk(() => {
          this.guardarDatosCliente(this.cliente)
            .then(result => {
              this.hasChanges = false
              this.colorBotonSave = 'primary'
              this.$q.dialog({
                title: 'Se han guardado los cambios'
              })
            })
            .catch(error => {
              this.$q.dialog({
                message: error.message
              })
            })
        })
      }
    },
    cambiaDatosExpedicion (fechaEx) {
      this.hasChanges = true
      this.colorBotonSave = 'red'
      console.log('fechaExp', fechaEx)
      const fechaVal10 = date.formatDate(date.addToDate(fechaEx, { days: 10 }), 'YYYY-MM-DD')
      console.log('fechaVal10', fechaVal10)
      this.cliente.fechaValideZ = fechaVal10
      // console.log('fechaV', this.cliente.fechaValidez)
      // this.cliente.fechaValidez = date.formatDate(date.addToDate(fechaEx, { years: 10 }), 'DD/MM/YYYY')
    },
    cambiaDatos () {
      this.hasChanges = true
      this.colorBotonSave = 'red'
    }
  },
  mounted () {
    // loadDetalleCliente se le tiene que pasar el idCliente (contenido en keyValue)
    this.loadDetallecliente(this.keyValue)
      .then(response => {
        this.cliente = response.data[0]
      })
      .catch(error => {
        this.$q.dialog({ title: 'Error', message: error.response.statusText })
        this.desconectarLogin()
      })
  },
  components: {
    wgDate: wgDate
  }
}
</script>
