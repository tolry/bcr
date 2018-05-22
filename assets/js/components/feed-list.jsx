import React from 'react';
import FeedItem from './feed-item.jsx';
import {render} from 'react-dom';

export default class FeedList extends React.Component {

    constructor (props) {
        super(props)

        this.state = {
            expanded: false,
        }
    }

    toggle (e) {
        e.preventDefault()

        this.setState({
            expanded: !this.state.expanded,
        })
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

        const items = (filteredItems.length > limit && !this.state.expanded)
            ? filteredItems.slice(0, limit)
            : filteredItems

        const feedItems = items.map((item) => {
            return (
                <FeedItem key={item.id} data={item} />
            );
        });

        return (
            <div className="pb-4">
                <h4>
                    <i className="fa fa-calendar"></i>&nbsp;
                    {this.props.published} [{filteredItems.length} posts]
                </h4>
                {feedItems}
                { (filteredItems.length > limit)
                        ? (
                            <button onClick={(e) => this.toggle(e)} type="button" className="text-center btn btn-sm btn-light btn-block">
                                <i className={this.state.expanded ? 'fa fa-chevron-up' : 'fa fa-chevron-down'}></i>
                                &nbsp;{ this.state.expanded ? 'gruselig, das war\'s schon?' : 'puh, da kommt noch mehr?'}&nbsp;
                                <i className={this.state.expanded ? 'fa fa-chevron-up' : 'fa fa-chevron-down'}></i>
                            </button>
                        )
                        : ''
                }
            </div>
        )
    }
}
