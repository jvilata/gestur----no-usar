import { axiosInstance } from 'boot/axios.js'

const state = { }

const mutations = { }

const actions = {
  addGastos ({ commit }, gastos) {
    return axiosInstance.get('caja/bd_caja.php/guardarBD', { params: gastos }, { withCredentials: true })
  },
  borrarGastos ({ commit }, id) {
    return axiosInstance.get('caja/bd_caja.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  },
  findCuadreCaja ({ commit }, record) {
    return axiosInstance.get('caja/bd_caja.php/findCajaFilter', { params: record }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
