import { axiosInstance, headerFormData } from 'boot/axios.js'
import querystring from 'querystring'

const state = {
  listaClientes: []
}

const mutations = {
  loadListaClientes (state, lista) {
    state.listaClientes = lista
  }
}

const actions = {
  loadListaClientes ({ commit }, objFilter) {
    return axiosInstance.get('clientes/bd_clientes.php/findFilter', { params: objFilter }, { withCredentials: true })
  },
  comboListaClientes ({ commit }) {
    axiosInstance.get('clientes/bd_clientes.php/comboListaClientes', {}, { withCredentials: true }) // tipo acciones
      .then((response) => {
        if (response.data.length === 0) {
          this.dispatch('mensajeLog/addMensaje: No existen datos', { root: true })
        } else {
          commit('loadListaClientes', response.data)
        }
      })
      .catch(error => {
        this.dispatch('mensajeLog/addMensaje' + error, { root: true })
      })
  },
  loadTablaAux ({ commit }, tabAux) { // tabAux: { codTabla: x, mutation: 'mutation' }
  },
  loadDetallecliente ({ commit }, id) {
    return axiosInstance.get('clientes/bd_clientes.php/findFilter', { params: { id: id } }, { withCredentials: true })
  },
  guardarDatosCliente ({ commit }, cliente) {
    return axiosInstance.post('clientes/bd_clientes.php/guardarBD', querystring.stringify(cliente), headerFormData)
  },
  borrarCliente ({ commit }, id) {
    return axiosInstance.get('clientes/bd_clientes.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
