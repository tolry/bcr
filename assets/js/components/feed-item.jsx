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

    renderCarousel (item) {
        if (item.images.length <= 1) {
            return
        }

        return <div className="d-flex flex-column align-items-center" style={{ position: 'absolute', right: '-100px' }}>
                {item.images.map((image, index) => {
                    let css = {width: '80px', cursor: 'pointer'}
                    if (index !== this.state.carouselIndex) {
                        css.filter = 'blur(2px)';
                    }

                    return <img
                        key={'thumbnail-' + index}
                        className="img-thumbnail m-1" style={css}
                        src={image.thumbnail}
                        onClick={() => this.setState({carouselIndex: index})} />
                })}
        </div>
    }

    renderMedia (item) {
        let imageCard = null
        let image = (item.images.length > 0)
            ? item.images[this.state.carouselIndex]
            : null

        if (image) {
            imageCard = <CardImg top width="100%" src={image.url} alt={item.title} />
        }

        if (item.videoProperties.length !== 0) {
            return (<LazyLoad placeholder={imageCard}>
                <Plyr className={"plyr-" + item.id} {...item.videoProperties} />
            </LazyLoad>)
        }

        if (image) {
            return imageCard
        }

        return null
    }

    render () {
        const item = this.props.data
        const isNew = false

        return (<div>
            {this.renderCarousel(item)}
            <Card className="mb-4">
                {isNew ? <div className="ribbon ribbon-top-left"><span><i className="fa fa-star"></i></span></div> : null}
                {(item.id === localStorage.getItem('lastReadItem'))
                        ? null
                        : null
                }
                {this.renderMedia(item)}
                <CardBody>
                    <div style={{ float: 'right' }} className="m-1">
                        <Button tag="a" outline color="secondary" href={item.link} target="_blank"><i className="fa fa-fire"/> auf geht's</Button>
                    </div>
                    {!item.title ? null : <CardTitle>{item.title}</CardTitle>}
                    {!item.description ? null : <CardSubtitle dangerouslySetInnerHTML={{ __html: item.description }} />}
                </CardBody>
                <CardFooter className="text-muted">
                    <i className={"fa fa-lg fa-" + item.channel.icon}></i> {item.channel.label},&nbsp;
                    <i className="fa fa-clock-o"></i><span title={moment(item.published.date).format("DD.MMMM.YYYY HH:mm:ss")}> {moment(item.published.date).fromNow()}</span>
                    <i className="fa fa-code float-right" onClick={() => this.setState({showDebug: !this.state.showDebug})}></i>
                </CardFooter>
                {!item.debugInfo || !this.state.showDebug ? null : <pre><code>{JSON.stringify(item.debugInfo, null, 4)}</code></pre>}
            </Card>
        </div>)
    }
}
