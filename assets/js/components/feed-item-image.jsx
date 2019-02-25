import React from 'react'
import { CardMedia, withStyles } from '@material-ui/core'

const styles = theme => ({
    media: {
        height: 0,
        paddingTop: '56.25%', // 16:9
    },
})

class FeedItemImage extends React.Component {
    render() {
        return (
            <CardMedia
                className={this.props.classes.media}
                image={this.props.image.url}
            />
        )
    }
}

export default withStyles(styles)(FeedItemImage)
