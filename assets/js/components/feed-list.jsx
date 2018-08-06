import React from 'react';
import FeedItem from './feed-item.jsx';
import {render} from 'react-dom';

export default class FeedList extends React.Component {

    constructor (props) {
        super(props)
    }

    render () {
        const limit = 9

        const filteredItems = this.props.items.filter((item) => {
            if (!this.props.filter[item.channel.id]) {
                return true
            }

            return this.props.filter[item.channel.id].enabled
        })

        if (filteredItems.length === 0) {
            return <div />
        }

        const feedItems = filteredItems.map((item) => {
            return (
                <FeedItem key={item.id} data={item} />
            );
        });

        return (
            <div className="pb-4">
                <h4 className="text-center">
                    <i className="fa fa-calendar"></i>&nbsp;
                    {this.props.published} [{filteredItems.length} posts]
                </h4>
                {feedItems}
            </div>
        )
    }
}
