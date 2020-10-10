import Vue from 'vue'
import Vuex from 'vuex'
import login from './store-login'
import tabs from './store-tabs'
import clientes from './store-clientes'
import servicios from './store-servicios'
import estancias from './store-estancias'
import tablasAux from './store-tab-aux'
// import example from './module-example'

Vue.use(Vuex)

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Store instance.
 */

export default function (/* { ssrContext } */) {
  const Store = new Vuex.Store({
    modules: {
      login,
      tabs,
      clientes,
      servicios,
      estancias,
      tablasAux
    },

    // enable strict mode (adds overhead!)
    // for dev mode only
    strict: process.env.DEV
  })

  return Store
}
