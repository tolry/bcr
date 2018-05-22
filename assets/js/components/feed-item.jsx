import React from 'react';
import {render} from 'react-dom';
import moment from 'moment'

export default class FeedItem extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            expanded: false,
        }
    }

    toggle () {
        this.setState({
            expanded: !this.state.expanded,
        })
    }

    render () {
        const item = this.props.data

        return (
            <div className="row pb-4">
                { (item.id === localStorage.getItem('lastReadItem'))
                    ? null
                    : null
                }
                <div className="col-2">
                    <div className="card-text text-muted" title={"channel: " + item.channel.label}>
                        <i className={"fa fa-lg fa-" + item.channel.icon}></i> {item.channel.label}<br/>
                        <i className="fa fa-clock-o"></i><span title={moment(item.published.date).format("DD.MMMM.YYYY HH:mm:ss")}> {moment(item.published.date).fromNow()}</span>
                    </div>
                </div>
                <div className="col-7">
                    <h5 className="card-title text-truncate" title={item.title}>
                        <a href={item.link} target="_blank">
                            {item.title}
                        </a>
                    </h5>
                    <div className="card-text text-muted">{item.description}</div>
                </div>
                {!item.image ? null :
                    <div className="col-3">
                        <img src={item.image} alt={item.title} className={this.state.expanded ? "card-img-top" : "card-img-top card-img-top-cropped"} onClick={() => this.toggle()} />
                    </div>
                }
            </div>
        )
    }
}
