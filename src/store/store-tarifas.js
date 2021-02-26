import { axiosInstance } from 'boot/axios.js'

const state = {
  listaTarifas: []
}

const mutations = {
  loadListaTarifas (state, tarifas) {
    state.listaTarifas = tarifas
  }
}

const actions = {
  addTarifa ({ commit }, serv) {
    return axiosInstance.get('tarifas/bd_tarifas.php/guardarBD', { params: serv }, { withCredentials: true })
  },
  loadListaTarifas ({ commit }) {
    return axiosInstance.get('servicios/bd_servicios.php/findServiciosFilter', { }, { withCredentials: true })
      .then(response => {
        commit('loadListaTarifas', response.data)
      })
      .catch(error => {
        this.dispatch('mensajeLog/addMensaje', 'loadListaTarifas' + error, { root: true })
      })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
