import React from 'react'
import FeedList from './feed-list.jsx'
import FeedFilter from './feed-filter.jsx'
import {render} from 'react-dom'
import moment from 'moment'

export default class Feed extends React.Component {
    constructor (props) {
        super(props)

        this.state = {
            loading: true,
            items: null,
            error: false,
            channelFilter: null,
            updated: null,
        }
    }

    componentDidMount () {
        this.loadData(true)
    }

    channelFilter (groupedItems) {
        let filter = {}
        let storedFilter = JSON.parse(localStorage.getItem('channelFilter'))

        groupedItems.map((group) => {
            group.items.map((item) => {
                if (!filter[item.channel.id]) {
                    filter[item.channel.id] = item.channel
                    filter[item.channel.id].count = 0
                    filter[item.channel.id].enabled = (storedFilter && storedFilter[item.channel.id])
                        ? storedFilter[item.channel.id].enabled
                        : true
                }

                filter[item.channel.id].count++
            })
        })

        return filter
    }

    loadData (first) {
        this.setState({
            loading: true
        })

        fetch("/feed.json")
            .then(res => res.json())
            .then(
                (result) => {
                    console.table(result)
                    if (first) {
                        localStorage.setItem('lastReadItem', result[0].items[0].id)
                    }

                    this.setState({
                        loading: false,
                        items: result,
                        channelFilter: this.channelFilter(result),
                        updated: new Date(),
                    })
                },
                // Note: it's important to handle errors here
                // instead of a catch() block so that we don't swallow
                // exceptions from actual bugs in components.
                (error) => {
                    this.setState({
                        loading: false,
                        error: true
                    })
                }
            )

        setTimeout(() => this.loadData(false), 30000)
    }

    toggleFilter (channelId) {
        const filter = this.state.channelFilter

        if (!filter[channelId]) {
            return
        }

        filter[channelId].enabled = !filter[channelId].enabled

        this.setState({
            channelFilter: filter
        })
    }

    renderLoading () {
        return (
            <div className="text-center">
                <i className="fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i>
                <span className="sr-only">loading...</span>
            </div>
        )
    }

    renderError () {
        return (
            <div className="text-center">
                <i className="fa fa-exclamation-circle text-danger fa-lg"></i>&nbsp;
                error loading feed(s)
            </div>
        )
    }

    render () {
        if (this.state.error) {
            return this.renderError()
        }

        if (this.state.loading && !this.state.items) {
            return this.renderLoading()
        }

        localStorage.setItem('channelFilter', JSON.stringify(this.state.channelFilter))

        const feedLists = this.state.items.map((group) => {
            return (
                <FeedList key={group.published} published={group.published} items={group.items} filter={this.state.channelFilter} />
            )
        })

        return (
            <div>
                <div className="text-muted text-center">last update&nbsp;
                    <span>{ this.state.loading
                        ? (<i className="fa fa-circle-o-notch fa-spin fa-lg fa-fw"></i>)
                        : (<span><i className="fa fa-clock-o fa-lg"></i> { moment(this.state.updated).format('HH:mm') }</span>)
                    }</span>
                </div>
                <div className="container pt-4">
                    <div className="row justify-content-center">
                        <div className="col col-3">
                            <FeedFilter filter={this.state.channelFilter} callback={(channelId) => this.toggleFilter(channelId)} />
                        </div>
                        <div className="col col-sm-9 col-lg-6">
                            {feedLists}
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}
