import React from 'react'
import { render } from 'react-dom'
import App from './components/app.jsx'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faClock, faRss } from '@fortawesome/free-solid-svg-icons'
import { faInstagram, faFlickr, faYoutube, faTwitter, faHooli } from '@fortawesome/free-brands-svg-icons'

library.add(faClock, faInstagram, faFlickr, faYoutube, faTwitter, faRss, faHooli)

render(<App />, document.getElementById('app'))
