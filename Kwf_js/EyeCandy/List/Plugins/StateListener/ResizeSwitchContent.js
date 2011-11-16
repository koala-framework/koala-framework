Kwf.EyeCandy.List.Plugins.StateListener.ResizeSwitchContent = Ext.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    //animationConfig = {}
    //sizes = { state: {width:123, height:123}}
    init: function() {
        this.list.on('childStateChanged', this._change, this);
    },
    render: function() {
        // saves old states as follows: this.oldState[item.id] = 'state'
        this.oldState = {};

        this.list.items.each(function(item) {
            var currentState = item.getState();
            this.oldState[item.id] = currentState;

            // hide all
            // and measure the original sizes
            for (var i in this.sizes) {
                for (var j in this.sizes[i].contentElements) {
                    var contentElement = item.el.child(this.sizes[i].contentElements[j].selector);
                    if (!contentElement) continue;
                    contentElement.enableDisplayMode('block');
                    contentElement.show(); //show to mesure correctly
                    if (!contentElement.ResizeSwitchContentOriginalWidth) {
                        contentElement.ResizeSwitchContentOriginalWidth = contentElement.getWidth();
                    }
                    if (!contentElement.ResizeSwitchContentOriginalHeight) {
                        contentElement.ResizeSwitchContentOriginalHeight = contentElement.getHeight();
                    }
                    contentElement.hide(); //hide by default, correct one will be activated below
                }
            }

            // measure the max sizes
            for (var i in this.sizes) {
                //show all elements for this state (to be able to measure)
                for (var j in this.sizes[i].contentElements) {
                    var contentElement = item.el.child(this.sizes[i].contentElements[j].selector);
                    if (!contentElement) continue;
                    contentElement.show();
                }
                var maxWidth = 0;
                var maxHeight = 0;
                item.el.select('*').each(function(childEl) {
                    if (childEl.isVisible(true)) {
                        if (childEl.getWidth() > maxWidth) maxWidth = childEl.getWidth();
                        if (childEl.getHeight() > maxHeight) maxHeight = childEl.getHeight();
                    }
                }, this);
                if (!this.sizes[i].maxSize) this.sizes[i].maxSize = {};
                this.sizes[i].maxSize[item.id] = { width: maxWidth, height: maxHeight };

                //hide all elements for this state (to be able to measure the next state)
                for (var j in this.sizes[i].contentElements) {
                    var contentElement = item.el.child(this.sizes[i].contentElements[j].selector);
                    if (!contentElement) continue;
                    contentElement.hide();
                }
            }

            // show elements for current state
            for (var j in this.sizes[currentState].contentElements) {
                var contentElement = item.el.child(this.sizes[currentState].contentElements[j].selector);
                if (!contentElement) continue;
                contentElement.show();
            }

        }, this);
    },
    _change: function(item) {
        var state = item.getState();
        var oldState = this.oldState[item.id];

        // measure old elements
        var contentElementsOldSizes = {};
        for (var j in this.sizes[oldState].contentElements) {
            var contentElement = item.el.child(this.sizes[oldState].contentElements[j].selector);
            if (!contentElement) continue;
            contentElementsOldSizes[j] = contentElement.getSize();
        }

        // hide old elements
        for (var i in this.sizes) {
            if (i != state) {
                for (var j in this.sizes[i].contentElements) {
                    var contentElement = item.el.child(this.sizes[i].contentElements[j].selector);
                    if (!contentElement) continue;
                    contentElement.hide();
                }
            }
        }

        // display new elements
        for (var j in this.sizes[state].contentElements) {
            var contentElement = item.el.child(this.sizes[state].contentElements[j].selector);
            if (!contentElement) continue;
            contentElement.show();
        }

        // set the inner content elements to the old size and animate them to their original size
        for (var j in this.sizes[state].contentElements) {
            var contentElement = item.el.child(this.sizes[state].contentElements[j].selector);
            if (!contentElement) continue;
            if (this.sizes[state].contentElements[j].animate === false) continue;

            contentElement.setSize(contentElementsOldSizes[j].width, contentElementsOldSizes[j].height);
            contentElement.setSize(contentElement.ResizeSwitchContentOriginalWidth, contentElement.ResizeSwitchContentOriginalHeight, true);
        }

        var maxWidth = this.sizes[state].maxSize[item.id].width;
        var maxHeight = this.sizes[state].maxSize[item.id].height;

        item.el.setSize(
            maxWidth+item.el.getPadding('lr'),
            maxHeight+item.el.getPadding('tb'),
            true
        );

        this.oldState[item.id] = state;
    }
});
