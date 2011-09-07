Vps.EyeCandy.List.Plugins.Scroll = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    numberShown: 5,
    moveElementSelector: false, //if false list.el, else list.child(moveElementSelector)
    _currentPosition: 0,
    _previousButton: null,
    _nextButton: null,

    _moveActive: false,
    init: function() {
        Ext.applyIf(this, {
            animationConfig: { concurrent: true, duration: 0.25 }
        });
        this.moveElement = this.list.el;
        if (this.moveElementSelector) {
            this.moveElement = this.moveElement.child(this.moveElementSelector);
        }

        if (this.list.items.length > this.numberShown) {
            for(var i=this.numberShown; i<this.list.getItems().length; ++i) {
                this.list.getItem(i).el.hide();
            }
        }
    },
    render: function() {
        if (this.list.items.length > this.numberShown) {
            this._previousButton = this.list.el.createChild({
                tag: 'a',
                cls: 'scrollPrevious',
                href: '#'
            });
            this._previousButton.on('click', function(ev) {
                ev.stopEvent();
                this.onMovePrevious();
            }, this);

            this._nextButton = this.list.el.createChild({
                tag: 'a',
                cls: 'scrollNext',
                href: '#'
            });
            this._nextButton.on('click', function(ev) {
                ev.stopEvent();
                this.onMoveNext();
            }, this);

            if (this._currentPosition <= 0) {
                this._previousButton.addClass('scrollInactive');
            }
            if (this._currentPosition >= this._getMaxPosition()) {
                this._nextButton.addClass('scrollInactive');
            }
        }
    },
    _getMaxPosition: function() {
        if (this.numberShown >= this.list.items.length) return 0;
        return this.list.items.length - this.numberShown;
    },

    onMoveNext: function() {
        if (this._currentPosition >= this._getMaxPosition()) return;
        if (this._moveActive) return;
        this._moveActive = true;

        // fade out first one
        var cfg = Ext.applyIf({
            useDisplay: false
        }, this.animationConfig);
        this.list.getItem(this._currentPosition).el.fadeOut(cfg);

        // fade in new one
        this.list.getItem(this._currentPosition + this.numberShown).el.fadeIn(cfg);

        var firstElWidth = this.list.getItem(this._currentPosition).getWidthIncludingMargin();

        var cfg = Ext.applyIf({
            callback: function() {
                this._moveActive = false;
                if (this._previousButton) this._previousButton.removeClass('scrollInactive');
            },
            scope: this
        }, this.animationConfig);
        this.moveElement.move('left', firstElWidth, cfg);

        if (this._currentPosition+1 >= this._getMaxPosition()) {
            if (this._nextButton) this._nextButton.addClass('scrollInactive');
        }

        this._currentPosition += 1;
    },

    onMovePrevious: function() {
        if (this._currentPosition <= 0) return;
        if (this._moveActive) return;
        this._moveActive = true;

        // fade in first
        var cfg = Ext.applyIf({
            useDisplay: false
        }, this.animationConfig);
        this.list.getItem(this._currentPosition-1).el.fadeIn(cfg);

        // fade out last
        this.list.getItem((this._currentPosition-1) + this.numberShown).el.fadeOut(cfg);
        var cfg = Ext.applyIf({
            callback: function() {
                this._moveActive = false;
                if (this._nextButton) this._nextButton.removeClass('scrollInactive');
            },
            scope: this
        });

        this.moveElement.move('right', this.list.getItem(this._currentPosition-1).getWidthIncludingMargin(), cfg);

        if (this._currentPosition-1 <= 0) {
            if (this._previousButton) this._previousButton.addClass('scrollInactive');
        }

        this._currentPosition -= 1;
    }
});
