import { axiosInstance } from 'boot/axios.js'

const state = {
}

const mutations = {
}

const actions = {
  findEstancia ({ commit }, record) {
    return axiosInstance.get('estancias/bd_estancias.php/findEstanciasFilter', { params: record }, { withCredentials: true })
  },
  findLinEstancias ({ commit }, record) {
    return axiosInstance.get('estancias/bd_estancias.php/findLinEstanciasFilter', { params: record }, { withCredentials: true })
  },
  addEstancia ({ commit }, est) {
    return axiosInstance.get('estancias/bd_estancias.php/guardarBD', { params: est }, { withCredentials: true })
  },
  borrarEstancia ({ commit }, id) {
    return axiosInstance.get('estancias/bd_estancias.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  },
  generarFactura ({ commit }, est) {
    return axiosInstance.get('estancias/bd_estancias.php/generarFactura', { params: est }, { withCredentials: true })
  },
  // reservas
  addReserva ({ commit }, record) {
    return axiosInstance.get('estancias/bd_estancias.php/guardarReservasBD/', { params: record }, { withCredentials: true })
  },
  borrarReserva ({ commit }, id) {
    return axiosInstance.get('estancias/bd_estancias.php/deleteReservasBD', { params: { id: id } }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
