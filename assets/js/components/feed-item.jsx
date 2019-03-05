import React from 'react'
import moment from 'moment'
import { withStyles } from '@material-ui/core/styles'
import Card from '@material-ui/core/Card'
import CardHeader from '@material-ui/core/CardHeader'
import CardContent from '@material-ui/core/CardContent'
import CardActions from '@material-ui/core/CardActions'
import Typography from '@material-ui/core/Typography'
import { CardActionArea, IconButton, Collapse, Avatar } from '@material-ui/core'
import ExpandMoreIcon from '@material-ui/icons/ExpandMore'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import FeedItemImage from './feed-item-image'
import FeedItemAudio from './feed-item-audio'
import FeedItemVideo from './feed-item-video'
import FeedItemImageCarousel from './feed-item-image-carousel'
import { isDev } from '../isDev'

const styles = theme => ({
    card: {},
    actions: {
        display: 'flex',
    },
    expand: {
        transform: 'rotate(0deg)',
        marginLeft: 'auto',
        transition: theme.transitions.create('transform', {
            duration: theme.transitions.duration.shortest,
        }),
    },
    expandOpen: {
        transform: 'rotate(180deg)',
    },
})

class FeedItem extends React.Component {
    state = {
        showDebug: false,
        carouselIndex: 0,
    }

    hasVideo = item => item.videoProperties && Object.keys(item.videoProperties).length > 0
    hasAudio = item => item.audio && Object.keys(item.audio).length > 0
    hasJustSingleImage = item => !this.hasVideo(item) && item.images.length === 1
    hasSlideshow = item => !this.hasVideo(item) && item.images.length > 1

    render() {
        const { classes } = this.props
        const item = this.props.data

        return (
            <Card className={classes.card} elevation={5}>
                <CardHeader
                    avatar={
                        <Avatar>
                            <FontAwesomeIcon size="lg" icon={item.channel.icon} />
                        </Avatar>
                    }
                    subheader={
                        <span title={moment(item.published.date).format('DD.MMMM YYYY, HH:mm:ss')}>
                            posted {moment(item.published.date).fromNow()}
                        </span>
                    }
                    title={
                        <span>
                            {item.channel.label} <small>{item.channel.type}</small>
                        </span>
                    }
                />
                {this.hasVideo(item) && <FeedItemVideo item={item} />}
                {this.hasSlideshow(item) && <FeedItemImageCarousel item={item} />}
                <CardActionArea href={item.link} target="_blank">
                    {this.hasJustSingleImage(item) && <FeedItemImage image={item.images[0]} />}
                    {item.title && <CardHeader title={item.title} />}
                    {item.description && (
                        <CardContent>
                            {item.description && (
                                <Typography
                                    component="p"
                                    dangerouslySetInnerHTML={{
                                        __html: item.description,
                                    }}
                                />
                            )}
                        </CardContent>
                    )}
                </CardActionArea>
                {this.hasAudio(item) && <FeedItemAudio item={item} />}

                {isDev() && (
                    <CardActions>
                        <IconButton
                            onClick={() => this.setState({ showDebug: !this.state.showDebug })}
                            className={
                                this.state.showDebug ? `${classes.expand} ${classes.expandOpen}` : classes.expand
                            }
                        >
                            <ExpandMoreIcon />
                        </IconButton>
                    </CardActions>
                )}

                <Collapse in={this.state.showDebug} timeout="auto" unmountOnExit>
                    <Typography component="pre">
                        <Typography component="code">{JSON.stringify(item.debugInfo, null, 4)}</Typography>
                    </Typography>
                </Collapse>
            </Card>
        )
    }
}

export default withStyles(styles)(FeedItem)
