
const routes = [
  {
    path: '/',
    component: () => import('layouts/LayoutLogin.vue'),
    children: [
      { path: '', component: () => import('pages/PageLogin.vue') }
    ]
  },
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: 'sinTabs',
        component: () => import('components/MainTabs/sinTabs.vue')
      },
      {
        path: 'mainTabs',
        component: () => import('components/MainTabs/mainTabs.vue'),
        children: [
          {
            path: 'clientesMain',
            name: 'clientesMain',
            component: () => import('components/Clientes/clientesMain.vue'),
            props: true
          },
          {
            path: 'estanciasMain',
            name: 'estanciasMain',
            component: () => import('components/EstanciasReservas/estanciasMain.vue'),
            props: true
          },
          {
            path: 'estanciasForm/:id',
            name: 'estanciasForm',
            component: () => import('components/EstanciasReservas/estanciasForm.vue'),
            props: true
          },
          {
            path: 'dashboard',
            name: 'dashboard',
            component: () => import('components/Dashboard/dashboardMain.vue'),
            props: true,
            children: [
              { path: 'gridEstadisSem', name: 'gridEstadisSem', component: () => import('components/Dashboard/dashboardGridEstadisSem.vue'), props: true },
              { path: 'gridEstadisMen', name: 'gridEstadisMen', component: () => import('components/Dashboard/dashboardGridEstadisMensual.vue'), props: true },
              { path: 'evolucionFacturacionMes', name: 'evolucionFacturacionMes', component: () => import('components/Dashboard/dashboardEvolucionFacturacionMes.vue'), props: true },
              { path: 'evolucionFacturacionAnyos', name: 'evolucionFacturacionAnyos', component: () => import('components/Dashboard/dashboardEvolucionFacturacionAnyo.vue'), props: true }
            ]
          },
          {
            path: 'calendarioMain',
            name: 'calendarioMain',
            component: () => import('components/Calendario/calendarioMain.vue'),
            props: true
          },
          {
            path: 'gastosCajaMain',
            name: 'gastosCajaMain',
            component: () => import('components/GastosCaja/gastosCajaMain.vue'),
            props: true
          },
          {
            path: 'cuadreCajaMain',
            name: 'cuadreCajaMain',
            component: () => import('components/CuadreCaja/cuadreCajaMain.vue'),
            props: true
          },
          {
            path: 'serviciosMain',
            name: 'serviciosMain',
            component: () => import('components/Servicios/serviciosMain.vue'),
            props: true
          },
          {
            path: 'tarifasMain',
            name: 'tarifasMain',
            component: () => import('components/TarifasServicios/tarifasMain.vue'),
            props: true
          },
          {
            path: 'discoGCMain',
            name: 'discoGCMain',
            component: () => import('components/GuardiaCivil/discoGCMain.vue'),
            props: true
          },
          {
            path: 'usuariosMain',
            name: 'usuariosMain',
            component: () => import('components/Usuarios/usuariosMain.vue'),
            props: true
          },
          {
            path: 'tablasMain',
            name: 'tablasMain',
            component: () => import('components/TablasAuxiliar/tablasMain.vue'),
            props: true
          },
          {
            path: 'editCliente',
            name: 'editCliente',
            component: () => import('components/Clientes/editCliente.vue'),
            props: true
          }
        ]
      }
    ]
  }
]
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes
