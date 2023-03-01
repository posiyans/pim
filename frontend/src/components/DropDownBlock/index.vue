<template>
  <div>
    <div v-if="showMobileBlock">
      <div @click="showFilter = !showFilter">
        <slot name="header" v-bind:showFilter="showFilter">
          <div class="row cursor-pointer show-btn inline items-center q-px-sm no-wrap">
            <div>
              {{ labelBtn }}
            </div>
            <div>
              <q-icon
                name="arrow_drop_down"
                size="sm"
                :class="{ 'rotate-180': showFilter,'rotate-0': !showFilter}"
              />
            </div>
          </div>
        </slot>
      </div>
      <div class="overflow-hidden-y">
        <q-slide-transition>
          <div v-if="showFilter">
            <slot></slot>
          </div>
        </q-slide-transition>
      </div>

    </div>
    <div v-else>
      <slot></slot>
    </div>
  </div>
</template>

<script>

export default {
  props: {
    showLabel: {
      type: String,
      default: 'Показать фильтр'
    },
    hideLabel: {
      type: String,
      default: 'Спрятать фильтр'
    },
    onlyMobile: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      showFilter: false
    }
  },
  computed: {
    showMobileBlock() {
      return this.$q.platform.is.mobile || !this.onlyMobile
    },
    labelBtn() {
      return this.showFilter ? this.hideLabel : this.showLabel
    }
  }
}
</script>

<style scoped>
.show-btn:hover {
  background-color: #f5f7fa;
}

.rotate-180 {
  animation: rotate-180;
  animation-duration: 0.3s;
  animation-timing-function: linear;
  animation-fill-mode: forwards;
}

@keyframes rotate-180 {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(180deg);
  }
}

.rotate-0 {
  animation: rotate-0;
  animation-duration: 0.3s;
  animation-timing-function: linear;
  animation-fill-mode: forwards;
}

@keyframes rotate-0 {
  from {
    transform: rotate(180deg);
  }
  to {
    transform: rotate(0deg);
  }
}
</style>
