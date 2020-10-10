import { axiosInstance } from 'boot/axios.js' // headerFormData
// state: accesibles en lectura desde componentes a traves de ...mapState('login', ['user'])
const state = {
  loggingIn: false,
  loginError: null,
  loginSuccessful: false,
  user: {}// { id, nombre, login, password }
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
  },
  desconectar: (state) => {
    state.user = {}
  }
}

// actions: accesibles desde componentes a traves de ...mapActions('login', ['doLogin'])
const actions = {
  doLogin ({ commit }, loginData) {
    commit('loginStart')
    axiosInstance.get('personal/bd_personal.php/login', { params: loginData }, { withCredentials: true })
      .then((response) => {
        if (!response.data[0].id) { throw new Error('Credenciales incorrectas') }
        commit('setUser', { login: loginData.login, pers: response.data[0] })
        // LocalStorage.set('email', loginData.email)
        // LocalStorage.set('password', loginData.password)

        this.$router.push('/sinTabs')
      })
      .catch(error => {
        commit('loginStop', error) // .response.data.error
      })
  },
  desconectarLogin ({ commit }) {
    // cerramos todos los tabs y redirigimos al login
    axiosInstance.get('personal/bd_personal.php/logout', { }, { withCredentials: true })
      .then((response) => {
        this.dispatch('tabs/removeAllTabs', [], { root: true })
        commit('loginStop', 'Introduzca credenciales')
        commit('desconectar')
      })
      .catch(error => {
        commit('loginStop', error) // .response.data.error
      })
    this.$router.push('/')
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
