import { axiosInstance } from 'boot/axios.js'

const state = {
}

const mutations = {
}

const actions = {
  addPrereservas ({ commit }, serv) {
    return axiosInstance.get('prereservas/bd_prereservas.php/guardarBD', { params: serv }, { withCredentials: true })
  },
  borrarPrereservas ({ commit }, id) {
    return axiosInstance.get('prereservas/bd_prereservas.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  },
  devolverPrereservas ({ commit }, record) {
    return axiosInstance.get('prereservas/bd_prereservas.php/devolverPrereserva', { params: record }, { withCredentials: true })
  },
  loadPrereservas ({ commit }, objFilter) {
    return axiosInstance.get('prereservas/bd_prereservas.php/findPrereservasFilter', { params: objFilter }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
