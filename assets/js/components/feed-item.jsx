import React from 'react'
import {render} from 'react-dom'
import moment from 'moment'
import Plyr from 'react-plyr'
import { Button, Card, CardImg, CardText, CardBody, CardFooter, CardTitle, CardSubtitle } from 'reactstrap'

export default class FeedItem extends React.Component {
    constructor(props) {
        super(props)
    }

    render () {
        const item = this.props.data

        let media = null
        if (item.channel.video) {
            media = <Plyr className={"plyr-" + item.id} type={item.channel.videoType} videoId={item.id} />
        } else if (item.image) {
            media = <CardImg top width="100%" src={item.image} alt={item.title} />
        }

        return (<Card className="mb-4">
            { (item.id === localStorage.getItem('lastReadItem'))
                    ? null
                    : null
            }
            {media}
            <CardBody>
                <CardTitle>{item.title}</CardTitle>
                {!item.description ? null : <CardSubtitle>{item.description}</CardSubtitle>}
                <CardText className="text-right">
                    <Button outline color="secondary" onClick={() => window.open(item.link, '_blank') }><i className="fa fa-fire"/> auf geht's</Button>
                </CardText>
            </CardBody>
            <CardFooter className="text-muted">
                <i className={"fa fa-lg fa-" + item.channel.icon}></i> {item.channel.label},&nbsp;
                <i className="fa fa-clock-o"></i><span title={moment(item.published.date).format("DD.MMMM.YYYY HH:mm:ss")}> {moment(item.published.date).fromNow()}</span>
            </CardFooter>
        </Card>)
    }
}
