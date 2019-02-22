import React from 'react';
import FeedItem from './feed-item.jsx';
import { Grid } from '@material-ui/core'

export default class FeedList extends React.Component {
    render() {
        const filteredItems = this.props.items.filter((item) => {
            if (!this.props.filter[item.channel.id]) {
                return true
            }

            return this.props.filter[item.channel.id].enabled
        })

        return filteredItems.map((item) => {
            return <Grid key={item.id} item xs={12}>
                <FeedItem key={item.id} data={item} />
            </Grid>
        })
    }
}
