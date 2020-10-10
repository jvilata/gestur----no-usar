<template>
  <div>
    <q-item clickable v-ripple @click="expanded = !expanded" class="q-ma-xs q-pa-xs bg-blue-grey-1 text-grey-8" >
          <q-item-section avatar>
            <q-icon name="person_add" size="md"/>
          </q-item-section>
          <q-item-section class="text-center">
            <q-item-label class="text-h6 ">
              Nuevo Cliente
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
          <q-input outlined autogrow label="Email" :value="cliente.email" class="col-xs-7 col-sm-7" />
          <q-input outlined autogrow label="Matrícula" :value="cliente.matricula" class="col-xs-5 col-sm-5" />
        </div>
        <div class="row q-mb-sm">
          <q-input outlined autogrow label="Dirección" :value="cliente.direccion" class="col-xs-12 col-sm-8" />
          <q-input outlined autogrow label="Población" :value="cliente.poblacion" class="col-xs-8 col-sm-4" />
          <q-input outlined autogrow label="C.Postal" :value="cliente.cpostal" class="col-xs-4 col-sm-2" />
          <q-input outlined autogrow label="Provincia" :value="cliente.provincia" class="col-xs-8 col-sm-6" />
          <q-input outlined autogrow label="País" :value="cliente.pais" class="col-xs-4 col-sm-4" />
        </div>
        <div class="row q-mb-sm">
          <q-input outlined readonly label="Teléfonos" :value="cliente.telefonos" class="col-xs-12 col-sm-12" />
        </div>
        <div class="row q-mb-sm">
          <q-input outlined :value="cliente.tipoDoc" label="Tipo Doc." class="col-xs-5 col-sm-2"/>
          <q-input outlined label="DNI/Pasaporte" :value="cliente.nroDoc" class="col-xs-7 col-sm-4" />
          <q-input outlined label="Fecha Nacimiento" :value="cliente.fechaNacimiento" class="col-xs-8 col-sm-3"/>
          <q-input outlined label="Nacionalidad" :value="cliente.nacionalidad" class="col-xs-4 col-sm-3" />
        </div>
        <div class="row q-mb-sm">
          <q-input outlined readonly label="Fecha Expedición" :value="cliente.fechaExpedicion" class="col-xs-6 col-sm-6" />
          <q-input outlined readonly label="Fecha Validez" :value="cliente.fechaValidez" class="col-xs-6 col-sm-6" />
        </div>
        <div class="row q-mb-sm">
          <q-expansion-item
            expand-separator
            icon="home"
            label="Cliente con Servicio Periódico"
            caption="Más info"
            style="width: 100%"
          >
            <q-card>
              <q-card-section  class="q-pt-none q-pl-xs q-pr-xs">
                <div class="row q-mb-sm">
                  <q-select standout="bg-green-3" outlined label="Tipo Servicio Periódico" :value="tipoServicio" @input="v => tipoServicio = v" :options="optionsServ" class="col-xs-12 col-sm-3" />
                  <q-select standout="bg-green-3" outlined label="Tipo Facturación" :value="tipoFacturacion" @input="v => tipoFacturacion = v" :options="optionsFact" class="col-xs-7 col-sm-3" />
                  <q-input outlined label="Precio" class="col-xs-5 col-sm-3" />
                </div>
                <div class="row q-mb-sm">
                  <div class="row">Cuenta Bancaria</div>
                  <div class="row">
                    <q-input outlined class="col-xs-3 col-sm-3 q-pr-sm" />
                    <q-input outlined class="col-xs-3 col-sm-3 q-pr-sm" />
                    <q-input outlined class="col-xs-2 col-sm-3 q-pr-sm" />
                    <q-input outlined class="col-xs-4 col-sm-3 q-pr-sm" />
                  </div>
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

export default {
  props: ['value', 'id', 'keyValue'],
  data () {
    return {
      tipoServicio: null,
      tipoFacturacion: null,
      optionsServ: ['Caravana', 'Bungalow Madera', 'Bungalow Fibra', 'Molino', 'Apartamento'],
      optionsFact: ['Mensual', 'Semanal', 'Diaria', 'Anual'],
      cliente: {
        nombre: '',
        apellido1: '',
        apellido2: '',
        email: '',
        matricula: '',
        direccion: '',
        poblacion: '',
        cpostal: '',
        provincia: '',
        pais: '',
        tipoDoc: '',
        nroDoc: '',
        fechaNacimiento: '',
        nacionalidad: '',
        fechaExpedicion: '',
        fechaValidez: ''
      }
    }
  },
  computed: {
    ...mapState('login', ['user'])
  },
  methods: {
    ...mapActions('login', ['desconectarLogin']),
    ...mapActions('clientes', ['guardarDatosCliente']),
    // formatDate (date1) {
    //   console.log(this.cliente.fechaNacimiento)
    //   if (date1 !== '' || date1 !== undefined) {
    //     date1 = date.extractDate(date1, 'YYYY-MM-DD HH:mm:ss')
    //     return date.formatDate(date1, 'DD/MM/YYYY')
    //   } else return 'Fecha no registrada'
    // },
    guardarCliente () {
      this.cliente.id = 0
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
  }
}
</script>
