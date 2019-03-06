import React from 'react'
import FeedList from './feed-list.jsx'
import FeedFilter from './feed-filter.jsx'
import { render } from 'react-dom'
import moment from 'moment'
import { Grid, Typography, withStyles, Avatar } from '@material-ui/core'

const styles = {
    root: {
        maxWidth: 500,
        margin: '1em auto',
    },
}

class Feed extends React.Component {
    render() {
        const { classes } = this.props

        return (
            <div className={classes.root}>
                <Grid justify="center" container spacing={24}>
                    {this.props.items.map(group => {
                        return (
                            <FeedList
                                key={group.published}
                                published={group.published}
                                items={group.items}
                                filter={this.props.channels}
                            />
                        )
                    })}
                </Grid>
            </div>
        )
    }
}

export default withStyles(styles)(Feed)
