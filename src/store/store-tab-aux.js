import { axiosInstance } from 'boot/axios.js'

const state = {
  listaSINO: [{ id: '1', desc: 'SI' }, { id: '0', desc: 'NO' }],
  listaTipoDoc: [],
  listaTipoFact: [],
  listaTipoServ: [],
  listaTipoEstancia: [],
  listaTipoTarifa: [],
  listaTiposGasto: [],
  datosEstablecimiento: []
}

const mutations = {
  loadTipoDoc (state, tiposDoc) {
    state.listaTipoDoc = tiposDoc
  },
  loadTipoFact (state, tiposFac) {
    state.listaTipoFact = tiposFac
  },
  loadTipoServ (state, tiposServ) {
    state.listaTipoServ = tiposServ
  },
  loadTipoEstancia (state, tiposEst) {
    state.listaTipoEstancia = tiposEst
  },
  loadTipoTarifa (state, tiposTarifa) {
    state.listaTipoTarifa = tiposTarifa
  },
  loadTiposGasto (state, tiposGasto) {
    state.listaTiposGasto = tiposGasto
  },
  loadDatosEstablecimiento (state, datos) {
    datos.forEach(element => {
      if (element.codElemento === '1') { state.datosEstablecimiento.path = element.valor1 }
      if (element.codElemento === '2') { state.datosEstablecimiento.contador = element.valor1 } // nº archivo
      if (element.codElemento === '3') { state.datosEstablecimiento.codEstablecimiento = element.valor1 }
      if (element.codElemento === '4') { state.datosEstablecimiento.nomEstablecimiento = element.valor1 }
    })
    state.datosEstablecimiento.nomArchivo = state.datosEstablecimiento.path + state.datosEstablecimiento.codEstablecimiento + state.datosEstablecimiento.contador
  }
}

const actions = {
  loadTablasAux ({ commit }) {
    this.dispatch('tablasAux/loadTablaAux', { codTabla: 1, mutation: 'loadTipoDoc' })
    this.dispatch('tablasAux/loadTablaAux', { codTabla: 7, mutation: 'loadTipoFact' })
    this.dispatch('tablasAux/loadTablaAux', { codTabla: 4, mutation: 'loadTipoServ' })
    this.dispatch('tablasAux/loadTablaAux', { codTabla: 2, mutation: 'loadTipoEstancia' })
    this.dispatch('tablasAux/loadTablaAux', { codTabla: 3, mutation: 'loadTipoTarifa' })
    this.dispatch('tablasAux/loadTablaAux', { codTabla: 8, mutation: 'loadTiposGasto' })
    this.dispatch('tablasAux/loadTablaAux', { codTabla: 5, mutation: 'loadDatosEstablecimiento' })
  },
  loadTablaAux ({ commit }, tabAux) { // tabAux: { codTabla: x, mutation: 'mutation' }
    axiosInstance.get(`tablaAuxiliar/bd_tablaAuxiliar.php/findTablaAuxFilter?codTabla=${tabAux.codTabla}`, {}, { withCredentials: true }) // tipo acciones
      .then((response) => {
        if (response.data.length === 0) {
          this.dispatch('mensajeLog/addMensaje', tabAux.mutation + 'No existen datos', { root: true })
        } else {
          commit(tabAux.mutation, response.data)
        }
      })
      .catch(error => {
        this.dispatch('mensajeLog/addMensaje', tabAux.mutation + error, { root: true })
      })
  },
  borrarTablaAux ({ commit }, id) {
    return axiosInstance.get('tablaAuxiliar/bd_tablaAuxiliar.php/deleteBD', { params: { id: id } }, { withCredentials: true })
  },
  addTablaAux ({ commit }, tabla) {
    return axiosInstance.get('tablaAuxiliar/bd_tablaAuxiliar.php/guardarBD', { params: tabla }, { withCredentials: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
