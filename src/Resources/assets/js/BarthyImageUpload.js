/**
 * Barthy Admin Event
 * @param {CustomEvent} event
 */
function onInitFormEvent (event) {
  // do sth
}

function ready () {
  // do sth

  document.addEventListener('init-form', onInitFormEvent)
}

function completed () {
  document.removeEventListener('DOMContentLoaded', completed)
  window.removeEventListener('load', completed)
  ready()
}

if (document.readyState !== 'loading') {
  window.setTimeout(ready)
} else {
  document.addEventListener('DOMContentLoaded', completed)
  window.addEventListener('load', completed)
}
