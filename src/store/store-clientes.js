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
  comboListaClientes ({ commit }, objFilter) {
    return axiosInstance.get('clientes/bd_clientes.php/findFilter', { params: objFilter }, { withCredentials: true })
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
