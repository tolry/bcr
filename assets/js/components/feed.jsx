import React from 'react'
import FeedItem from './feed-item.jsx'
import { Grid, withStyles } from '@material-ui/core'

const styles = {
    root: {
        maxWidth: 500,
        margin: '1em auto',
    },
}

class Feed extends React.Component {
    render() {
        const { classes } = this.props
        const items = this.props.items.filter(item => {
            if (!this.props.channels[item.channel.id]) {
                return true
            }

            return this.props.channels[item.channel.id].enabled
        })

        return (
            <div className={classes.root}>
                <Grid container justify="center" spacing={24}>
                    {items.map(item => {
                        return (
                            <Grid item key={item.id} xs={12}>
                                <FeedItem key={item.id} data={item} />
                            </Grid>
                        )
                    })}
                </Grid>
            </div>
        )
    }
}

export default withStyles(styles)(Feed)
