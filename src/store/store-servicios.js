import { axiosInstance } from 'boot/axios.js'

const state = {
}

const mutations = {
}

const actions = {
  addServicios ({ commit }, serv) {
    return axiosInstance.get('servicios/bd_servicios.php/guardarBD', { params: serv }, { withCredentials: true })
  },
  borrarServicio ({ commit }, id) {
    return axiosInstance.get('servicios/bd_servicios.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
