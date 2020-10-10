import { axiosInstance } from 'boot/axios.js'

const state = {
}

const mutations = {
}

const actions = {
  addEstancia ({ commit }, est) {
    return axiosInstance.get('estancias/bd_estancias.php/guardarBD', { params: est }, { withCredentials: true })
  },
  borrarEstancia ({ commit }, id) {
    return axiosInstance.get('estancias/bd_estancias.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
