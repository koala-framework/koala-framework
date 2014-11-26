Kwf.EyeCandy.List.Plugins.Scroll = Ext2.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    numberShown: 5,
    moveElementSelector: false, //if false list.el, else list.child(moveElementSelector)
    createMoveElementSelectorWrapper: false,
    _currentPosition: 0,
    _previousButton: null,
    _nextButton: null,

    _moveActive: false,
    init: function() {
        Ext2.applyIf(this, {
            animationConfig: { concurrent: true, duration: 0.25 }
        });
        this.moveElement = this.list.el;
        if (this.moveElementSelector) {
            this.moveElement = this.moveElement.child(this.moveElementSelector);
        }

        if (this.createMoveElementSelectorWrapper) {
            var wrapper = this.moveElement.parent().createChild({
                tag: 'div', cls: 'listPluginScrollMoveWrapper'
            });
            wrapper.insertBefore(this.moveElement);
            this.moveElement.appendTo(wrapper);
        }
        this.list.on('activeChanged', this.onActivate, this);
    },
    onActivate: function(item)
    {
        var previousPosition = this._currentPosition;
        while (item.listIndex > this._currentPosition + this.numberShown - 1) {
            this._currentPosition++;
        }
        while (item.listIndex < this._currentPosition) {
            this._currentPosition--;
        }
        if (previousPosition != this._currentPosition) {
            this.onPositionChanged();
        }
    },
    onPositionChanged: function() {
        var x = this._initialX;
        this.list.items.each(function(item) {
            if (item.listIndex >= this._currentPosition) return false;
            x -= item.getWidthIncludingMargin();
        }, this);
        this.moveElement.setX(x, true);

        if (this._currentPosition >= this._getMaxPosition()) {
            if (this._nextButton) this._nextButton.addClass('scrollInactive');
        } else {
            if (this._nextButton) this._nextButton.removeClass('scrollInactive');
        }
        if (this._currentPosition <= 0) {
            if (this._previousButton) this._previousButton.addClass('scrollInactive');
        } else {
            if (this._previousButton) this._previousButton.removeClass('scrollInactive');
        }
    },
    render: function() {
        this._initialX = this.moveElement.getX();
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
        this._currentPosition += 1;
        this.onPositionChanged();
    },

    onMovePrevious: function() {
        if (this._currentPosition <= 0) return;
        this._currentPosition -= 1;
        this.onPositionChanged();
    }
});
