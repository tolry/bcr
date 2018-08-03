import React from 'react';
import {render} from 'react-dom';

export default class FeedFilter extends React.Component {
    render () {
        return (
            <div className="d-flex justify-content-center">
                {
                    Object.keys(this.props.filter).map((key) => {
                        const channel = this.props.filter[key]

                        return (
                            <button key={channel.id} className={channel.enables ? 'btn btn-light' : 'btn btn-light text-muted'} onClick={() => this.props.callback(channel.id)}>
                                <i className={ channel.enabled ? "fa fa-check-circle-o text-success" : "fa fa-circle-o text-quiet"}></i>&nbsp;
                                {channel.label}&nbsp;
                                <i className={"fa fa-"+channel.icon}></i>
                            </button>
                        )
                    })
                }
            </div>
        )
    }
}
