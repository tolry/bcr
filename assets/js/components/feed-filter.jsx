import React from 'react'
import {
    List,
    ListItem,
    ListItemIcon,
    ListItemText,
    ListSubheader,
    Checkbox,
    ListItemSecondaryAction,
    Switch,
    withStyles,
} from '@material-ui/core'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

const styles = {
    root: {
        minWidth: 300,
    },
}

class FeedFilter extends React.Component {
    render = () => {
        const { classes } = this.props
        return (
            <List
                component="nav"
                className={classes.root}
                subheader={<ListSubheader>Channels</ListSubheader>}
            >
                {Object.keys(this.props.filter).map(key => {
                    const channel = this.props.filter[key]

                    return (
                        <ListItem button key={channel.id}>
                            <ListItemIcon>
                                <FontAwesomeIcon size="lg" icon={channel.icon} />
                            </ListItemIcon>
                            <ListItemText>{channel.label}</ListItemText>
                            <ListItemSecondaryAction>
                                <Switch
                                    checked={channel.enabled}
                                    color="primary"
                                    onChange={() => this.props.onToggle(channel.id)}
                                />
                            </ListItemSecondaryAction>
                        </ListItem>
                    )
                })}
            </List>
        )
    }
}

export default withStyles(styles)(FeedFilter)
