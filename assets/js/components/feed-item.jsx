import React from 'react'
import {render} from 'react-dom'
import moment from 'moment'
import Plyr from 'react-plyr'
import LazyLoad from 'react-lazyload'
import { Button, Card, CardImg, CardText, CardBody, CardFooter, CardTitle, CardSubtitle } from 'reactstrap'

export default class FeedItem extends React.Component {
    constructor(props) {
        super(props)
        this.state = {showDebug: false}
    }

    renderMedia (item) {
        if (item.image) {
            const image = <CardImg top width="100%" src={item.image} alt={item.title} />
        }

        if (item.channel.video) {
            return (<LazyLoad placeholder={image}>
                <Plyr className={"plyr-" + item.id} type={item.channel.videoType} videoId={item.id} />
            </LazyLoad>)
        }

        if (item.image) {
            return image
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
