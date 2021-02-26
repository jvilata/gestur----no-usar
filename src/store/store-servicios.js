import { axiosInstance } from 'boot/axios.js'

const state = {
  listaServicios: []
}

const mutations = {
  loadListaServicios (state, tiposServ) {
    state.listaServicios = tiposServ
  }
}

const actions = {
  addServicios ({ commit }, serv) {
    return axiosInstance.get('servicios/bd_servicios.php/guardarBD', { params: serv }, { withCredentials: true })
  },
  borrarServicio ({ commit }, id) {
    return axiosInstance.get('servicios/bd_servicios.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  },
  loadListaServicios ({ commit }) {
    return axiosInstance.get('servicios/bd_servicios.php/findServiciosFilter', { }, { withCredentials: true })
      .then(response => {
        commit('loadListaServicios', response.data)
      })
      .catch(error => {
        this.dispatch('mensajeLog/addMensaje', 'loadListaServicios' + error, { root: true })
      })
  },
  calcularTarifa ({ commit }, record) {
    return axiosInstance.get('servicios/bd_servicios.php/calcularTarifaServicio', { params: record }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
