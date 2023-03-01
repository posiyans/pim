'use strict'
export default function readFileInputEventAsArrayBuffer(event, callback) {
  const file = event
  const reader = new FileReader()
  reader.onload = function(loadEvent) {
    var arrayBuffer = loadEvent.target.result
    callback(arrayBuffer)
  }
  reader.readAsArrayBuffer(file)
}
