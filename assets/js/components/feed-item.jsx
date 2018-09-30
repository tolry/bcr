import React from 'react'
import {render} from 'react-dom'
import moment from 'moment'
import Plyr from 'react-plyr'
import LazyLoad from 'react-lazyload'
import { Button, Card, CardImg, CardText, CardBody, CardFooter, CardTitle, CardSubtitle } from 'reactstrap'

export default class FeedItem extends React.Component {
    constructor(props) {
        super(props)
        this.state = {showDebug: false, carouselIndex: 0}
    }

    renderMedia (item) {
        let imageCard = null
        let image = (item.images.length > 0)
            ? item.images[this.state.carouselIndex]
            : null

        if (image) {
            imageCard = <CardImg top width="100%" src={image.url} alt={item.title} />
        }

        if (item.channel.video) {
            return (<LazyLoad placeholder={imageCard}>
                <Plyr className={"plyr-" + item.id} type={item.channel.videoType} videoId={item.id} />
            </LazyLoad>)
        }

        if (item.images.length > 1) {
            return <div>
                {imageCard}
                <div>
                    {item.images.map((image, index) => {
                        let css = {width: '80px', cursor: 'pointer'}
                        if (index !== this.state.carouselIndex) {
                            css.filter = 'blur(2px)';
                        }

                        return <img
                            key={'thumbnail-' + index}
                            class="img-thumbnail" style={css}
                            src={image.thumbnail}
                            onClick={() => this.setState({carouselIndex: index})} />
                    })}
                </div>
            </div>
        }

        if (image) {
            return imageCard
        }

        return null
    }

    render () {
        const item = this.props.data

        return (<Card className="mb-4">
            { (item.id === localStorage.getItem('lastReadItem'))
                    ? null
                    : null
            }
            {this.renderMedia(item)}
            <CardBody>
                {!item.title ? null : <CardTitle>{item.title}</CardTitle>}
                {!item.description ? null : <CardSubtitle>{item.description}</CardSubtitle>}
            </CardBody>
            <CardBody>
                <CardText className="text-right">
                    <Button tag="a" outline color="secondary" href={item.link} target="_blank"><i className="fa fa-fire"/> auf geht's</Button>
                </CardText>
            </CardBody>
            <CardFooter className="text-muted">
                <i className={"fa fa-lg fa-" + item.channel.icon}></i> {item.channel.label},&nbsp;
                <i className="fa fa-clock-o"></i><span title={moment(item.published.date).format("DD.MMMM.YYYY HH:mm:ss")}> {moment(item.published.date).fromNow()}</span>
                <i className="fa fa-code float-right" onClick={() => this.setState({showDebug: !this.state.showDebug})}></i>
            </CardFooter>
            {!item.debugInfo || !this.state.showDebug ? null : <pre><code>{JSON.stringify(item.debugInfo, null, 4)}</code></pre>}
        </Card>)
    }
}
