import { axiosInstance } from 'boot/axios.js'

const state = {
}

const mutations = {
}

const actions = {
  findViajerosFilter ({ commit }, record) {
    return axiosInstance.get('estancias/bd_viajeros.php/findViajerosFilter', { params: record }, { withCredentials: true })
  },
  addViajero ({ commit }, viajero) {
    return axiosInstance.get('estancias/bd_viajeros.php/guardarBD', { params: viajero }, { withCredentials: true })
  },
  borrarViajero ({ commit }, id) {
    return axiosInstance.get('estancias/bd_viajeros.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
