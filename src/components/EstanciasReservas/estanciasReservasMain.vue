<template>
    <div style="height: calc(100vh - 113px)">
      <q-item clickable v-ripple class="q-ma-xs q-pa-xs bg-blue-grey-1 text-grey-8" >
        <q-item-section avatar>
          <q-icon name="event_available" size="md"/>
        </q-item-section>
        <q-item-section class="text-center">
          <q-item-label class="text-h6 ">
            {{ nomFormulario }}
          </q-item-label>
        </q-item-section>
        <q-item-section side>
          <q-btn
          @click="$emit('close')" flat round dense icon="close"/>
        </q-item-section>
      </q-item>

    </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
//   props: ['value', 'id', 'keyValue'],

  data () {
    return {
      nomFormulario: 'Estancias/Reservas',
      expanded: false
    }
  },

  methods: {
    ...mapActions('login', ['desconectarLogin'])
  },

  destroyed () {
    this.$emit('changeTab', { idTab: this.value.idTab, filterRecord: this.filterRecord })
  }
}
</script>
