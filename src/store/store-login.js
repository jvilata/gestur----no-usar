import { axiosInstance } from 'boot/axios.js' // headerFormData
// state: accesibles en lectura desde componentes a traves de ...mapState('login', ['user'])
const state = {
  loggingIn: false,
  loginError: null,
  loginSuccessful: false,
  user: {}// { codEmpresa, nomEmpresa, user: {email, idPersonal }, pers: { id, codEmpresa, nombre, nombreAbreviado, email }}
}

const mutations = {
  loginStart: state => { state.loggingIn = true },
  loginStop: (state, errorMessage) => {
    state.loggingIn = false
    state.loginError = errorMessage
    state.loginSuccessful = !errorMessage
  },
  setUser: (state, updated) => {
    Object.assign(state.user, updated)
  }
}

// actions: accesibles desde componentes a traves de ...mapActions('login', ['doLogin'])
const actions = {
  doLogin ({ commit }, loginData) {
    commit('loginStart')
    axiosInstance.get('personal/bd_personal.php/login', { params: loginData }, { withCredentials: true })
      .then((response) => {
        console.log('ok')
        if (!response.data[0].id) { throw new Error('Credenciales incorrectas') }
        commit('setUser', response.data[0])
        this.$router.push('/sinTabs')
      })
      .catch(error => {
        commit('loginStop', error) // .response.data.error
      })
  },
  desconectarLogin ({ commit }) {
    // cerramos todos los tabs y redirigimos al login
    this.dispatch('tabs/removeAllTabs', [], { root: true })
    commit('loginStop', 'Introduzca credenciales')
    commit('desconectar')
    this.$router.push('/')
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
