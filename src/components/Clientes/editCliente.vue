<template>
  <div>
    <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-xs q-pa-xs bg-blue-grey-1 text-grey-8" >
          <q-item-section avatar>
            <q-icon name="edit" size="md"/>
          </q-item-section>
          <q-item-section class="text-center">
            <q-item-label class="text-h6 ">
              {{ `CLIENTE - ${ keyValue }` }}
            </q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-btn
            @click="$emit('close')" flat round dense icon="close"/>
          </q-item-section>
    </q-item>
    <q-card flat>
      <q-card-section  class="q-pt-none q-pl-xs q-pr-xs">
        <div class="row q-mb-sm">
          <q-input autogrow outlined label="Nombre" v-model="cliente.nombre" class="col-xs-4 col-sm-4"/>
          <q-input autogrow outlined label="Primer Apellido" v-model="cliente.apellido1" class="col-xs-4 col-sm-4"/>
          <q-input autogrow outlined label="Segundo Apellido" v-model="cliente.apellido2" class="col-xs-4 col-sm-4"/>
        </div>
        <div class="row q-mb-sm">
          <q-input outlined autogrow label="Email" v-model="cliente.email" class="col-xs-7 col-sm-7" />
          <q-input outlined autogrow label="Matrícula" v-model="cliente.matricula" class="col-xs-5 col-sm-5" />
        </div>
        <div class="row q-mb-sm">
          <q-input outlined autogrow label="Dirección" v-model="cliente.direccion" class="col-xs-12 col-sm-8" />
          <q-input outlined autogrow label="Población" v-model="cliente.poblacion" class="col-xs-8 col-sm-4" />
          <q-input outlined autogrow label="C.Postal" v-model="cliente.cpostal" class="col-xs-4 col-sm-2" />
          <q-input outlined autogrow label="Provincia" v-model="cliente.provincia" class="col-xs-8 col-sm-6" />
          <q-input outlined autogrow label="País" v-model="cliente.pais" class="col-xs-4 col-sm-4" />
        </div>
        <div class="row q-mb-sm">
          <q-input outlined label="Teléfonos" v-model="cliente.telefonos" class="col-xs-12 col-sm-12" />
        </div>
        <div class="row q-mb-sm">
          <q-select
          class="col-xs-5 col-sm-2"
          label="Tipo Doc."
          stack-label
          outlined
          v-model="cliente.tipoDoc"
          :options="listaDoc"
          map-options
          option-value="codElemento"
          option-label="valor1"
          emit-value
        />
          <q-input outlined label="DNI/Pasaporte" v-model="cliente.nroDoc" class="col-xs-7 col-sm-4" />
          <q-input
            label="Fecha Nacimiento"
            class="col-xs-6 col-sm-3"
            clearable
            outlined
            stack-label
            :value="formatDate(cliente.fechaNacimiento)">
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
          <q-input outlined label="Nacionalidad" v-model="cliente.nacionalidad" class="col-xs-4 col-sm-3" />
        </div>
        <div class="row q-mb-sm">
          <q-input
            label="Fecha Expedición"
            class="col-xs-6 col-sm-6"
            clearable
            outlined
            stack-label
            :value="formatDate(cliente.fechaExpedicion)">
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
           <q-input
            label="Fecha Validez"
            class="col-xs-6 col-sm-6"
            clearable
            outlined
            stack-label
            :value="formatDate(cliente.fechaValidez)">
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
            icon="home"
            label="Cliente con Servicio Periódico"
            caption="Más info"
            style="width: 100%"
            header-class="bg-green-1"
          >
            <q-card>
              <q-card-section  class="q-pt-none q-pl-xs q-pr-xs">
                <div class="row q-mb-sm">
                  <q-select  outlined label="Tipo Servicio Periódico" v-model="cliente.tipoServicioPeriodico" @input="v => tipoServicio = v" :options="optionsServ" class="col-xs-12 col-sm-5" />
                  <q-select
                    standout="bg-green-3"
                    class="col-xs-12 col-sm-4"
                    label="Tipo Fact."
                    stack-label
                    outlined
                    v-model="cliente.tipoFacturacion"
                    :options="listaFac"
                    map-options
                    option-value="codElemento"
                    option-label="valor1"
                    emit-value
                  />
                  <q-input outlined label="Precio" v-model="cliente.precio" class="col-xs-5 col-sm-2" />
                </div>
                <div class="row q-mt-xs">
                  Cuenta Bancaria
                </div>
                <div class="row">
                    <q-input label="Cod. Banco" outlined v-model="cliente.codBanco" class="col-xs-3 col-sm-3 q-pr-sm" />
                    <q-input label="Cod. Sucursal" outlined v-model="cliente.codSucursal" class="col-xs-3 col-sm-3 q-pr-sm" />
                    <q-input label="DC" outlined v-model="cliente.digitosControl" class="col-xs-2 col-sm-2 q-pr-sm" />
                    <q-input label="Cuenta" outlined v-model="cliente.cuentaBancaria" class="col-xs-4 col-sm-4 q-pr-sm" />
                </div>
              </q-card-section>
            </q-card>
         </q-expansion-item>
        </div>
      </q-card-section>
      <q-card-actions class="justify-center">
        <q-btn
          @click="guardarCliente"
          color="primary"
          label="Guardar"
          style="height: 50px; width: 120px"/>
      </q-card-actions>
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
      listaDoc: [],
      listaFac: [],
      tipoServicio: null,
      tipoFacturacion: null,
      optionsServ: ['Caravana', 'Bungalow Madera', 'Bungalow Fibra', 'Molino', 'Apartamento'],
      cliente: {}
    }
  },
  computed: {
    ...mapState('login', ['user']),
    ...mapState('tablasAux', ['listaTipoDoc', 'listaTipoFact'])
  },
  methods: {
    ...mapActions('login', ['desconectarLogin']),
    ...mapActions('clientes', ['loadDetallecliente', 'guardarDatosCliente']),
    ...mapActions('tabs', ['addTab']),
    openForm (otrosCambios) {
      this.addTab(['clientesMain', 'clientesMain', {}, 1])
    },
    formatDate (date1) {
      // if (date1 !== '' || date1 !== undefined || date1 !== '0000-00-00 00:00:00') {
      //   date1 = date.extractDate(date1, 'YYYY-MM-DD HH:mm:ss')
      return date.formatDate(date1, 'DD/MM/YYYY')
      // } else return 'Fecha no registrada'
    },
    guardarCliente () {
      this.guardarDatosCliente(this.cliente)
        .then(result => {
          this.$q.dialog({
            message: 'Se han guardado los cambios.'
          })
        })
        .catch(error => {
          this.$q.dialog({
            message: error.message
          })
        })
    }
  },
  mounted () {
    this.listaDoc = this.listaTipoDoc
    this.listaFac = this.listaTipoFact
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
