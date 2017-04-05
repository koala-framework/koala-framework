var kwfExtend = require('kwf/commonjs/extend');
var onReady = require('kwf/commonjs/on-ready');

Kwf.EyeCandy.List.Plugins.ActiveChanger.PlayPauseLink = kwfExtend(Kwf.EyeCandy.List.Plugins.Abstract, {
    init: function() {
        if (!this.interval) this.interval = 5000;

        this.changingByPlay = false;

        this.playPauseLink = this.list.el.createChild({
            tag: 'a',
            cls: 'listPlayPause',
            href: '#'
        });
        this._isPlaying = false;

        this.playPauseLink.on('click', function(ev) {
            ev.stopEvent();
            if (!this._isPlaying) {
                this._isPlaying = true;
                this.play();
            } else {
                this.pause();
            }
        }, this);

        if (this.autoPlay) {
            if (this.list.el.isVisible(true)) {
                this._isPlaying = true;
                this.play.defer(this.interval, this);
                this._playQueued = true;
            }
            onReady.onContentReady(function() {
                if (this._isPlaying && this.list.el.isVisible(true)) {
                    if (!this._playQueued) {
                        this.play.defer(this.interval, this);
                        this._playQueued = true;
                    }
                }
            }, { scope: this });
        }
    },

    render: function() {
        this.list.on('activeChanged', function(item) {
            if (!this.changingByPlay) {
                this.pause();
            }
        }, this);
    },

    play: function() {
        this._playQueued = false;
        if (this._isPlaying) {
            this.playPauseLink.removeClass('listIsPausing');
            this.playPauseLink.addClass('listIsPlaying');
            if (this.list.el.isVisible(true)) {
                this.play.defer(this.interval, this);
                this._playQueued = true;
                this.next();
            }
        }
    },

    pause: function() {
        this.playPauseLink.removeClass('listIsPlaying');
        this.playPauseLink.addClass('listIsPausing');
        this._isPlaying = false;
    },

    next: function() {
        this.changingByPlay = true;
        var item;
        if (this.list.getActiveItem() === this.list.getLastItem()) {
            item = this.list.getFirstItem();
        } else {
            item = this.list.getItem(this.list.getActiveItem().listIndex+1);
        }
        if (item) this.list.setActiveItem(item);
        this.changingByPlay = false;
    }
});
