import request from 'src/utils/request'

export function getTelegramBotToken() {
  return request({
    url: '/api/setting/get-telegram-bot-token',
    method: 'get'
  })
}

export function getTelegramBotInfo() {
  return request({
    url: '/api/telegram/get-bot-info',
    method: 'get'
  })
}

export function updateTelegramBotToken(data) {
  return request({
    url: '/api/setting/update-telegram-bot-token',
    method: 'post',
    data
  })
}

export function getMailSetting() {
  return request({
    url: '/api/setting/get-mail',
    method: 'get'
  })
}

export function updateMailSetting(data) {
  return request({
    url: '/api/setting/update-mail',
    method: 'post',
    data
  })
}

export function sendTestEmail(data) {
  return request({
    url: '/api/setting/send-test-mail',
    method: 'post',
    data
  })
}

export function changeTwoFactorEnable(data) {
  return request({
    url: '/api/setting/change-two-factor-enable',
    method: 'post',
    data
  })
}