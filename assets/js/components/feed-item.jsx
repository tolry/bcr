import React from 'react'
import moment from 'moment'
import { withStyles } from '@material-ui/core/styles'
import Card from '@material-ui/core/Card'
import CardHeader from '@material-ui/core/CardHeader'
import CardContent from '@material-ui/core/CardContent'
import CardActions from '@material-ui/core/CardActions'
import Typography from '@material-ui/core/Typography'
import { CardActionArea, IconButton, Collapse, Chip, Avatar } from '@material-ui/core'
import ExpandMoreIcon from '@material-ui/icons/ExpandMore'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import FeedItemImage from './feed-item-image'
import FeedItemVideo from './feed-item-video'
import FeedItemImageCarousel from './feed-item-image-carousel';

const styles = theme => ({
    card: {},
    cardHeader: {
        fontSize: '1.2em',
    },
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

    renderCarousel(item) {
        if (item.images.length <= 1) {
            return
        }

        return (
            <div
                className="d-flex flex-column align-items-center"
                style={{ position: 'absolute', right: '-100px' }}
            >
                {item.images.map((image, index) => {
                    let css = { width: '80px', cursor: 'pointer' }
                    if (index !== this.state.carouselIndex) {
                        css.filter = 'blur(2px)'
                    }

                    return (
                        <img
                            key={'thumbnail-' + index}
                            className="img-thumbnail m-1"
                            style={css}
                            src={image.thumbnail}
                            onClick={() => this.setState({ carouselIndex: index })}
                        />
                    )
                })}
            </div>
        )
    }

    hasVideo = item => item.videoProperties && Object.keys(item.videoProperties).length > 0
    hasJustSingleImage = item => !this.hasVideo(item) && item.images.length === 1
    hasSlideshow = item => !this.hasVideo(item) && item.images.length > 1

    render() {
        const { classes } = this.props
        const item = this.props.data

        return (
            <Card className={classes.card}>
                {this.hasVideo(item) && <FeedItemVideo item={item} />}
                {this.hasSlideshow(item) && <FeedItemImageCarousel item={item} />}
                <CardActionArea href={item.link} target="_blank">
                    {this.hasJustSingleImage(item) && <FeedItemImage image={item.images[0]} />}
                    {item.title && <CardHeader className={classes.cardHeader} title={item.title} />}
                    {item.description && (
                        <CardContent>
                            <Typography
                                component="p"
                                dangerouslySetInnerHTML={{
                                    __html: item.description,
                                }}
                            />
                        </CardContent>
                    )}
                </CardActionArea>
                <CardActions>
                    <Chip
                        avatar={
                            <Avatar>
                                <FontAwesomeIcon size="lg" icon={item.channel.icon} />
                            </Avatar>
                        }
                        label={item.channel.label}
                    />
                    <Chip
                        avatar={
                            <Avatar>
                                <FontAwesomeIcon icon={'clock'} />
                            </Avatar>
                        }
                        title={moment(item.published.date).format('DD.MMMM.YYYY HH:mm:ss')}
                        label={moment(item.published.date).fromNow()}
                    />
                    <IconButton
                        onClick={() => this.setState({ showDebug: !this.state.showDebug })}
                        className={
                            this.state.showDebug
                                ? `${classes.expand} ${classes.expandOpen}`
                                : classes.expand
                        }
                    >
                        <ExpandMoreIcon />
                    </IconButton>
                </CardActions>
                <Collapse in={this.state.showDebug} timeout="auto" unmountOnExit>
                    <Typography component="pre">
                        <Typography component="code">
                            {JSON.stringify(item.debugInfo, null, 4)}
                        </Typography>
                    </Typography>
                </Collapse>
            </Card>
        )
    }
}

export default withStyles(styles)(FeedItem)
