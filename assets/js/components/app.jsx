import AppBar from '@material-ui/core/AppBar'
import Toolbar from '@material-ui/core/Toolbar'
import Typography from '@material-ui/core/Typography'
import IconButton from '@material-ui/core/IconButton'
import MenuIcon from '@material-ui/icons/Menu'
import React from 'react'
import Feed from './feed.jsx'
import { withStyles } from '@material-ui/core/styles'
import Button from '@material-ui/core/Button'
import { Avatar, CssBaseline, Drawer, SwipeableDrawer, Grid, Chip, Fade } from '@material-ui/core'
import FeedFilter from './feed-filter.jsx'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

const styles = {
    root: {
        flexGrow: 1,
    },
    grow: {
        flexGrow: 1,
    },
    menuButton: {
        marginLeft: -12,
        marginRight: 20,
    },
    menuItem: {
        marginRight: 20,
    },
    association: {
        marginTop: '1em',
        marginRight: 20,
    },
    bcrAvatar: {
        margin: '2em auto',
        width: '90%',
        height: 'auto',
    },
}

class App extends React.Component {
    state = {
        loading: true,
        items: null,
        error: false,
        channels: null,
        updated: null,
        menuActive: true,
    }

    componentDidMount() {

        this.loadData()
        setInterval(() => this.loadData(), 30000)
    }

    channels = groupedItems => {
        let filter = {}
        let storedFilter = JSON.parse(localStorage.getItem('channels')) || {}

        groupedItems.map(group => {
            group.items.map(item => {
                if (!filter[item.channel.id]) {
                    filter[item.channel.id] = {
                        ...item.channel,
                        count: 0,
                        enabled:
                            storedFilter && storedFilter[item.channel.id]
                                ? storedFilter[item.channel.id].enabled
                                : true,
                    }
                }

                filter[item.channel.id].count++
            })
        })

        return filter
    }

    update = data => {
        this.setState({
            loading: false,
            error: false,
            items: data,
            channels: this.channels(data),
            updated: new Date(),
        })
    }

    handleError = error => {
        console.log(error)
        this.setState({
            loading: false,
            error: true,
        })
    }

    loadData = () => {
        this.setState({
            loading: true,
        })

        fetch('/feed.json')
            .then(response => response.json())
            .then(this.update)
            .catch(this.handleError)
    }

    toggleMenu = () => {
        this.setState({ menuActive: !this.state.menuActive })
    }
    toggleChannel = channelId => {
        if (!this.state.channels[channelId]) {
            return
        }

        this.setState({
            channels: {
                ...this.state.channels,
                [channelId]: {
                    ...this.state.channels[channelId],
                    enabled: !this.state.channels[channelId].enabled,
                },
            },
        })
    }
    render() {
        const { classes } = this.props

        this.state.channels && localStorage.setItem('channels', JSON.stringify(this.state.channels))

        return (
            <div>
                <CssBaseline />
                {this.state.channels && (
                    <SwipeableDrawer
                        open={this.state.menuActive}
                        onClose={() => this.setState({ menuActive: false })}
                        onOpen={() => this.setState({ menuActive: true })}
                    >
                        <FeedFilter
                            filter={this.state.channels}
                            onToggle={channelId => this.toggleChannel(channelId)}
                        />
                        <Fade timeout={10000} in>
                            <Avatar src="/img/der-bcr.jpg" className={classes.bcrAvatar} />
                        </Fade>
                    </SwipeableDrawer>
                )}
                <div className={classes.root}>
                    <AppBar color="default" position="static">
                        <Toolbar>
                            <IconButton
                                onClick={this.toggleMenu}
                                className={classes.menuButton}
                                color="inherit"
                                aria-label="Menu"
                            >
                                <MenuIcon />
                            </IconButton>
                            <Avatar className={classes.menuItem} src="/img/bcr-logo.png" />
                            <Typography
                                className={classes.menuItem}
                                variant="headline"
                                color="inherit"
                            >
                                BCR
                            </Typography>
                            <Typography
                                className={classes.menuItem}
                                variant="subheading"
                                color="textSecondary"
                            >
                                <i>unofficial</i> aggregator for all your BCR needs
                            </Typography>
                            {this.state.loading && (
                                <Typography variant="subheading" color="primary">
                                    loading...
                                </Typography>
                            )}

                            <div className={classes.grow} />

                            <Button color="inherit">
                                <img src="https://www.cornify.com/assets/cornifycorn.gif" />
                            </Button>
                        </Toolbar>
                    </AppBar>
                    <Grid container>
                        <div className={classes.grow} />
                        <Chip
                            className={classes.association}
                            variant="outlined"
                            icon={<FontAwesomeIcon size="2x" icon={['fab', 'hooli']} />}
                            label="no association"
                        />
                    </Grid>
                </div>
                {this.state.items && (
                    <Feed items={this.state.items} channels={this.state.channels} />
                )}
            </div>
        )
    }
}

export default withStyles(styles)(App)
