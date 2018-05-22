import '../css/app.scss';

import React from 'react';
import Feed from './components/feed.jsx';
import {render} from 'react-dom';

class App extends React.Component {
    render () {
        return <Feed />
    }
}

render(<App/>, document.getElementById('app'));
