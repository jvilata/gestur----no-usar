<template>
  <div>
    <q-item clickable v-ripple class="q-ma-xs q-pa-xs bg-blue-grey-1 text-grey-8" >
      <q-item-section>
        <q-item-label class="text-h6 q-ml-lg">
            {{ `Cliente - ${ keyValue }` }}
        </q-item-label>
      </q-item-section>
      <q-btn icon="save"  class="q-ma-xs" :color="colorBotonSave" dense @click="guardarCliente"/>
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
          <q-input label="Fecha Nacimiento" class="col-xs-7 col-sm-3" clearable outlined stack-label
            mask="##-##-####"
           :value="cliente.fechaNacimiento===null?null:cliente.fechaNacimiento.substr(8,2)+'-'+cliente.fechaNacimiento.substr(5,2)+'-'+cliente.fechaNacimiento.substr(0,4)"
           @input="v=>{cambiaDatos();cliente.fechaNacimiento=(v===null?null:v.substr(6,4)+'-'+v.substr(3,2)+'-'+v.substr(0,2)+' 00:00:00')}">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="fechaNac">
                  <wgDate
                      @input="$refs.fechaNac.hide()"
                      v-model="cliente.fechaNacimiento" />
              </q-popup-proxy>
              </q-icon>
          </template>
          </q-input>
          <q-input outlined clearable label="Nacionalidad" v-model="cliente.nacionalidad" class="col-xs-5 col-sm-3" />
        </div>
        <div class="row q-mb-sm">
          <q-input label="Fecha Expedición" class="col-xs-6 col-sm-6" clearable outlined stack-label
            :value="cliente.fechaExpedicion===null?null:cliente.fechaExpedicion.substr(8,2)+'-'+cliente.fechaExpedicion.substr(5,2)+'-'+cliente.fechaExpedicion.substr(0,4)"
            mask="##-##-####"
            @input="v=>cliente.fechaExpedicion=(v===null?null:v.substr(6,4)+'-'+v.substr(3,2)+'-'+v.substr(0,2)+' 00:00:00')"
            @blur="cambiaDatosExpedicion(cliente.fechaExpedicion)">
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
           <q-input label="Fecha Validez" class="col-xs-6 col-sm-6" clearable outlined stack-label
            @blur="cambiaDatos"
            :value="cliente.fechaValidez===null?null:cliente.fechaValidez.substr(8,2)+'-'+cliente.fechaValidez.substr(5,2)+'-'+cliente.fechaValidez.substr(0,4)"
            mask="##-##-####"
            @input="v=>cliente.fechaValidez=(v===null?null:v.substr(6,4)+'-'+v.substr(3,2)+'-'+v.substr(0,2)+' 00:00:00')">
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
                    :options="listaServiciosPeriodicos"
                    option-value="id"
                    option-label="descripcionCorta"
                    emit-value
                    map-options
                    @blur="cambiaDatos"
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
                    :options="listaTipoFact"
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
                    <!-- <q-input label="C.Banco" outlined v-model="cliente.codBanco" class="col-xs-3 col-sm-3 q-pr-sm" @blur="cambiaDatos"/>
                    <q-input label="C.Sucursal" outlined v-model="cliente.codSucursal" class="col-xs-3 col-sm-3 q-pr-sm" @blur="cambiaDatos"/>
                    <q-input label="DC" outlined v-model="cliente.digitosControl" class="col-xs-2 col-sm-2 q-pr-sm" @blur="cambiaDatos"/> -->
                    <q-input label="Cuenta / IBAN" outlined v-model="cliente.cuentaBancaria" class="col-xs-4 col-sm-4 q-pr-sm" @blur="cambiaDatos"/>
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
        fechaValidez: '',
        fechaNacimiento: ''
      },
      colorBotonSave: 'primary',
      refresh: 0,
      hasChanges: false,
      listaTipoServFilter: this.listaTipoServ,
      listaTipoFactFilter: this.listaTipoFact
    }
  },
  computed: {
    ...mapState('login', ['user']),
    ...mapState('tablasAux', ['listaTipoDoc', 'listaTipoFact', 'listaTipoServ']),
    ...mapState('servicios', ['listaServiciosPeriodicos'])
  },
  methods: {
    ...mapActions('login', ['desconectarLogin']),
    ...mapActions('clientes', ['loadDetallecliente', 'guardarDatosCliente', 'comboListaClientes']),
    ...mapActions('tabs', ['addTab']),
    openForm (otrosCambios) {
      this.addTab(['clientesMain', 'clientesMain', {}, 1])
    },
    formatDate (date1) {
      return date.formatDate(date1, 'DD/MM/YYYY')
    },
    confirmarCierre () {
      /* if (this.hasChanges) {
        this.$q.dialog({
          title: 'Aviso',
          message: '¿Desea guardar los cambios?',
          ok: true,
          cancel: true,
          persistent: true
        }).onOk(() => { */
      this.guardarCliente()
      this.$emit('close')
      /*  }).onCancel(() => {
          this.$emit('close')
        })
      } else { this.$emit('close') } */
    },
    guardarCliente () {
      /* if (this.hasChanges) {
        this.$q.dialog({
          title: 'Confirmar cambios',
          message: '¿ Desea guardar los cambios ?',
          ok: true,
          cancel: true,
          persistent: true
        }).onOk(() => { */
      this.guardarDatosCliente(this.cliente)
        .then(result => {
          this.hasChanges = false
          this.colorBotonSave = 'primary'
          this.$q.notify('Se han guardado los cambios')
          this.comboListaClientes() // refrescamos state listaClientes
        })
        .catch(error => {
          this.$q.dialog({
            message: error.message
          })
        })
      /*  })
      } */
    },
    cambiaDatosExpedicion (fechaEx) {
      this.hasChanges = true
      this.colorBotonSave = 'red'
      const year = parseInt(fechaEx.substring(0, 4)) + 10
      this.cliente.fechaValidez = year + fechaEx.substring(4, 19)
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
