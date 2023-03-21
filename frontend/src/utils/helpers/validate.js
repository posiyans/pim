export function EmailValidate(value) {
  // eslint-disable-next-line
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/
  if (value.length === 0) {
    return true
  }
  if (!re.test(value)) {
    return 'Пожалуйста, введите действующий email'
  } else {
    return true
  }
}