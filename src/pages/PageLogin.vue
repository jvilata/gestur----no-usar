<template>
  <q-page padding class="row q-pa-xl justify-center" style="padding-top: 100px">
    <!-- definimos una pagina de LOGIN que tiene un logo arriba y un formulario de empresa, usuario y password abajo -->
   
      <div class="row justify-center q-pa-lg">
        <q-img src="~assets/camping.jpg" style="width: 40vh"/>
      </div>
      <q-card>
        <div class="text-subtitle2 q-pt-md q-pl-md">Identificación de usuario</div>
        <form class="q-pa-md">
            <q-input name="email" v-model="user.email" label="Usuario" type="text" style="font-size: 16px"/>
            <q-input name="password" autocomplete="password" v-model="user.password" type="password" label="Password" style="font-size: 16px"/>
            <div class="row justify-center q-pa-md">
              <q-btn type="submit" rounded color="primary" class="full-width" label="Entrar"/>
            </div>
          </form>
      </q-card>
  </q-page>
</template>

<script>
import { mapActions, mapState } from 'vuex'
export default {
  data () {
    return {
      user: {
        login: '',
        password: '',
      }
    }
  },
  computed: {
    // accedo a VUEX al store/store-login a estos states y aqui los puedo usar como si fueran variables locales
    // ...mapState('login', [
    //   'loggingIn',
    //   'loginError',
    //   'loginSuccessful'
    // ]),
    
  },
  methods: {
    // accedo a VUEX al store/store-login a la action doLogin y aqui la podemos invocar como this.doLogin
    // ...mapActions('login', ['doLogin']),
    // ...mapActions('tablasAux', ['loadTablasAux']),
    
  },
  mounted () {
    //this.loadTablasAux()
    
  }
}
</script>

