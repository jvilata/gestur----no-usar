import { axiosInstance } from 'boot/axios.js'

const state = { }

const mutations = { }

const actions = {
  addTipo1 ({ commit }, record) {
    return axiosInstance.get('guardiac/bd_guardiac.php/guardarBDTipo1', { params: record }, { withCredentials: true })
  },
  addTipo2 ({ commit }, record) {
    return axiosInstance.get('guardiac/bd_guardiac.php/guardarBDTipoReg2', { params: record }, { withCredentials: true })
  },
  borrarTipo2 ({ commit }, id) {
    return axiosInstance.get('guardiac/bd_guardiac.php/deleteBDTipoReg2', { params: { id: id } }, { withCredentials: true })
  },
  findTipo1 ({ commit }, record) {
    return axiosInstance.get('guardiac/bd_guardiac.php/findGCTipo1Filter', { params: record }, { withCredentials: true })
  },
  findTipo2 ({ commit }, record) {
    return axiosInstance.get('guardiac/bd_guardiac.php/findGCTipoReg2Filter', { params: record }, { withCredentials: true })
  },
  generarArchivoGC ({ commit }) {
    return axiosInstance.get('guardiac/bd_guardiac.php/generarArchivoGC', { }, { withCredentials: true })
  },
  generarGC ({ commit }, record) {
    return axiosInstance.get('guardiac/bd_guardiac.php/generarListaGC', { params: record }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
