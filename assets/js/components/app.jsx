import AppBar from '@material-ui/core/AppBar'
import Toolbar from '@material-ui/core/Toolbar'
import Typography from '@material-ui/core/Typography'
import IconButton from '@material-ui/core/IconButton'
import MenuIcon from '@material-ui/icons/Menu'
import React from 'react'
import Feed from './feed.jsx'
import { withStyles } from '@material-ui/core/styles'
import Button from '@material-ui/core/Button'
import { Avatar, CssBaseline, Drawer, SwipeableDrawer } from '@material-ui/core'
import FeedFilter from './feed-filter.jsx'

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
}

class App extends React.Component {
    state = {
        loading: true,
        items: null,
        error: false,
        channelFilter: null,
        updated: null,
        menuActive: true,
    }

    componentDidMount() {
        this.loadData()
        setInterval(() => this.loadData(), 30000)
    }

    channels = groupedItems => {
        let filter = {}

        let storedFilter = {}
        try {
            storedFilter = JSON.parse(localStorage.getItem('channels'))
        } catch (e) {}

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
        localStorage.setItem('channels', JSON.stringify(this.state.channels))

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
                            <Avatar src="/img/der-bcr.jpg" />
                            <Typography variant="h6" color="inherit" className={classes.grow}>
                                BCR {this.state.loading && <small>loading...</small>}
                            </Typography>
                            <Button color="inherit">Login</Button>
                        </Toolbar>
                    </AppBar>
                </div>
                {this.state.items && (
                    <Feed items={this.state.items} channels={this.state.channels} />
                )}
            </div>
        )
    }
}

export default withStyles(styles)(App)
