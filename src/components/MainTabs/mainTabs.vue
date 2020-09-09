<template>
    <div>
    <q-card class="flat">
        <q-tabs
          v-model="ltab"
          dense
          class="text-grey"
          active-color="primary"
          indicator-color="primary"
          align="justify"
          narrow-indicator
        >
          <q-route-tab v-for="(tab, index) in tabs" :key="index" :name="tab.params.id" :label="tab.label" :to="tab"  />
        </q-tabs>

        <q-separator />
        <q-tab-panels v-model="ltab" animated>
          <q-tab-panel v-for="(tab, index) in tabs" :key="index" :name="tab.params.id"  class="q-pa-none">
            <router-view
              @changeTab="(value) => updateTabData([tab, value])"
              @close="removeTab(tab)"
              @contadorAcciones="(value) => $emit('contadorAcciones', value)"/>
          </q-tab-panel>
        </q-tab-panels>
    </q-card>
    </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
export default {
  data () {
    return {
      ltab: '',
      done: false
    }
  },
  computed: {
    ...mapState('tabs', ['tabs'])
  },
  methods: {
    ...mapActions('tabs', ['addTab', 'updateTabData', 'removeTab'])
  }
}
</script>
