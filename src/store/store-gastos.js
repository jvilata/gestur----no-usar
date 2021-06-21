import { axiosInstance } from 'boot/axios.js'

const state = { }

const mutations = { }

const actions = {
  addGastos ({ commit }, gastos) {
    return axiosInstance.get('gastos/bd_gastos.php/guardarBD', { params: gastos }, { withCredentials: true })
  },
  borrarGastos ({ commit }, id) {
    return axiosInstance.get('gastos/bd_gastos.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  },
  findGastos ({ commit }, record) {
    return axiosInstance.get('gastos/bd_gastos.php/findGastosFilter', { params: record }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
