import React from 'react'
import {
    withStyles,
    MobileStepper,
    Button,
    IconButton,
    Typography,
    CardContent,
    CardActionArea,
} from '@material-ui/core'
import FeedItemImage from './feed-item-image'
import ChevronLeft from '@material-ui/icons/ChevronLeft'
import ChevronRight from '@material-ui/icons/ChevronRight'

const styles = theme => ({
    root: {
        flexGrow: 1,
    },
    media: {
        height: 0,
        paddingTop: '56.25%', // 16:9
    },
})

class FeedItemImageCarousel extends React.Component {
    state = {
        index: 0,
    }

    render() {
        const { images } = this.props.item
        const { index } = this.state

        return (
            <div>
                <FeedItemImage image={images[index]} />
                {images[index].label && (
                    <CardActionArea href={images[index].link || this.props.item.link} target="_blank">
                        <CardContent>
                            <Typography variant="caption">{images[index].label}</Typography>
                        </CardContent>
                    </CardActionArea>
                )}
                <MobileStepper
                    steps={images.length}
                    position="static"
                    activeStep={index}
                    nextButton={
                        <IconButton onClick={this.next}>
                            <ChevronRight />
                        </IconButton>
                    }
                    backButton={
                        <IconButton onClick={this.back}>
                            <ChevronLeft />
                        </IconButton>
                    }
                />
            </div>
        )
    }

    next = () => this.setState({ index: (this.state.index + 1) % this.props.item.images.length })
    back = () => this.setState({ index: Math.max(0, this.state.index - 1) })
}

export default withStyles(styles)(FeedItemImageCarousel)
