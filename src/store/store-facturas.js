import { axiosInstance } from 'boot/axios.js'

const state = {
}

const mutations = {
}

const actions = {
  addFactura ({ commit }, est) {
    return axiosInstance.get('facturas/bd_facturas.php/guardarBD', { params: est }, { withCredentials: true })
  },
  borrarFactura ({ commit }, id) {
    return axiosInstance.get('facturas/bd_facturas.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
