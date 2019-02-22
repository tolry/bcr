import React from 'react'
import { render } from 'react-dom'
import App from './components/app.jsx'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faClock } from '@fortawesome/free-regular-svg-icons'
import { faInstagram, faFlickr, faYoutube } from '@fortawesome/free-brands-svg-icons'

console.log(faClock, faInstagram)

library.add(faClock, faInstagram, faFlickr, faYoutube)

render(<App />, document.getElementById('app'))
