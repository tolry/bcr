import React from 'react'
import Plyr from 'react-plyr'
import LazyLoad from 'react-lazyload'
import FeedItemImage from './feed-item-image'

class FeedItemVideo extends React.Component {
    render() {
        return (
            <LazyLoad placeholder={<FeedItemImage image={this.props.item.images[0]}/>}>
                <Plyr className={'plyr-' + this.props.item.id} {...this.props.item.videoProperties} />
            </LazyLoad>
        )
    }
}

export default FeedItemVideo
