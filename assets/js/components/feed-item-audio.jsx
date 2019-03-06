import React from 'react'
import Plyr from 'react-plyr'

class FeedItemAudio extends React.Component {
    render() {
        return <Plyr className={'plyr-audio-' + this.props.item.id} type="audio" url={this.props.item.audio[0].url} />
    }
}

export default FeedItemAudio
