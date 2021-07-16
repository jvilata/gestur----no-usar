import { axiosInstance } from 'boot/axios.js'

const state = {
}

const mutations = {
}

const actions = {
  addTarifa ({ commit }, serv) {
    return axiosInstance.get('servicios/bd_tarifas.php/guardarBD', { params: serv }, { withCredentials: true })
  },
  deleteTarifa ({ commit }, serv) {
    return axiosInstance.get('servicios/bd_tarifas.php/deleteBD', { params: serv }, { withCredentials: true })
  },
  loadListaTarifas ({ commit }) {
    return axiosInstance.get('servicios/bd_tarifas.php/findTarifasFilter', { }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
