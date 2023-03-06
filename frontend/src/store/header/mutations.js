export function setTitle(state, val) {
  state.title = val
}

export function toggleLeftDrawer(state) {
  console.log('ckckc')
  state.leftDrawerOpen = !state.leftDrawerOpen
}

export function setCountReadNoReport(state, val) {
  state.countNoReadReport = val
}

