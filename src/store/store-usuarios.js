import { axiosInstance } from 'boot/axios.js'

const state = {
  listaUsuarios: []
}

const mutations = {
  loadListaUsuarios (state, usuario) {
    state.listaUsuarios = usuario
  }
}

const actions = {
  addUsuario ({ commit }, usuario) {
    return axiosInstance.get('usuarios/bd_usuarios.php/guardarBD', { params: usuario }, { withCredentials: true })
  },
  borrarUsuario ({ commit }, id) {
    return axiosInstance.get('usuarios/bd_usuarios.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  },
  loadListaUsuarios ({ commit }) {
    return axiosInstance.get('usuarios/bd_usuarios.php/findUsuariosFilter', { }, { withCredentials: true })
      .then(response => {
        commit('loadListaUsuarios', response.data)
      })
      .catch(error => {
        this.dispatch('mensajeLog/addMensaje', 'loadListaUsuarios' + error, { root: true })
      })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
