import React from 'react'
import moment from 'moment'
import Plyr from 'react-plyr'
import LazyLoad from 'react-lazyload'
import { withStyles } from '@material-ui/core/styles'
import Card from '@material-ui/core/Card'
import CardHeader from '@material-ui/core/CardHeader'
import CardMedia from '@material-ui/core/CardMedia'
import CardContent from '@material-ui/core/CardContent'
import CardActions from '@material-ui/core/CardActions'
import Typography from '@material-ui/core/Typography'
import { CardActionArea, IconButton, Collapse, Chip, Avatar } from '@material-ui/core'
import ExpandMoreIcon from '@material-ui/icons/ExpandMore'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

const styles = theme => ({
    card: {},
    media: {
        height: 0,
        paddingTop: '56.25%', // 16:9
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
    chip: {
        verticalAlign: 'middle',
    },
})

class FeedItem extends React.Component {
    constructor(props) {
        super(props)
        this.state = { showDebug: false, carouselIndex: 0 }
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

    hasVideo = item =>
        typeof item.videoProperties === 'object' && Object.keys(item.videoProperties).length > 0

    renderMedia = item => {
        const { classes } = this.props
        let imageCard = null
        let image = item.images.length > 0 ? item.images[this.state.carouselIndex] : null

        if (image) {
            imageCard = <CardMedia className={classes.media} image={image.url} title={item.title} />
        }

        if (this.hasVideo(item)) {
            return (
                <LazyLoad placeholder={imageCard}>
                    <Plyr className={'plyr-' + item.id} {...item.videoProperties} />
                </LazyLoad>
            )
        }

        if (image) {
            return imageCard
        }

        return null
    }

    render() {
        const { classes } = this.props
        const item = this.props.data

        return (
            <Card className={classes.card}>
                {this.hasVideo(item) && this.renderMedia(item)}
                <CardActionArea href={item.link} target="_blank">
                    {!this.hasVideo(item) && this.renderMedia(item)}
                    {item.title && <CardHeader title={item.title} />}
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
                                <FontAwesomeIcon icon={['fab', 'instagram']} />
                            </Avatar>
                        }
                        label={item.channel.label}
                    />
                    <Chip
                        className={classes.chip}
                        avatar={
                            <Avatar>
                                <FontAwesomeIcon icon={['far', 'clock']} />
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
