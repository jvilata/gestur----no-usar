import { axiosInstance } from 'boot/axios.js'

const state = {
  listaServicios: [],
  listaServiciosPeriodicos: []
}

const mutations = {
  loadListaServicios (state, tiposServ) {
    state.listaServicios = tiposServ
    state.listaServiciosPeriodicos = tiposServ.filter(row => row.tipoServicio === '8')
  }
}

const actions = {
  addServicios ({ commit }, serv) {
    return axiosInstance.get('servicios/bd_servicios.php/guardarBD', { params: serv }, { withCredentials: true })
  },
  borrarServicio ({ commit }, id) {
    return axiosInstance.get('servicios/bd_servicios.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  },
  loadListaServicios ({ commit }, objFilter) {
    return axiosInstance.get('servicios/bd_servicios.php/findServiciosFilter', { params: objFilter }, { withCredentials: true })
  },
  loadListaServiciosMut ({ commit }) {
    axiosInstance.get('servicios/bd_servicios.php/findServiciosFilter', { }, { withCredentials: true })
      .then((response) => {
        if (response.data.length === 0) {
          this.dispatch('mensajeLog/addMensaje', 'loadListaServicios' + 'No existen datos', { root: true })
        } else {
          commit('loadListaServicios', response.data)
        }
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
