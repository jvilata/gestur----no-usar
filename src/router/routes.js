
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
            path: 'estanciasReservasMain',
            name: 'estanciasReservasMain',
            component: () => import('components/EstanciasReservas/estanciasReservasMain.vue'),
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
