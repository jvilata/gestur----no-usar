<template>
  <q-page padding class="row q-pa-xl justify-center" style="padding-top: 100px">
    <!-- definimos una pagina de LOGIN que tiene un logo arriba y un formulario de empresa, usuario y password abajo -->
    <div class="row justify-center q-pa-lg">
        <q-img src="~assets/camping.jpg" style="width: 50vh"/>
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

export default {
  data () {
    return {
      user: {
        login: '',
        password: ''
      }
    }
  }
}
</script>
