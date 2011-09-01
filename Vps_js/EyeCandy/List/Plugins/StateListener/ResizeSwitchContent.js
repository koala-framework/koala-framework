Vps.EyeCandy.List.Plugins.StateListener.ResizeSwitchContent = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    //animationConfig = {}
    //sizes = { state: {width:123, height:123}}
    init: function() {
        this.list.on('childStateChanged', this._change, this);
    },
    render: function() {
        // saves old states as follows: this.oldState[item.id] = 'state'
        this.oldState = {};

        this.list.items.each(function(item) {
            var state = item.getState();
            item.oldState = state;

            // deactivate others
            for (var i in this.sizes) {
                for (var j in this.sizes[i].contentElements) {
                    var contentElement = item.el.child(this.sizes[i].contentElements[j].selector);

                    if (!this.sizes[i].contentElements[j].width) {
                        this.sizes[i].contentElements[j].width = contentElement.getWidth();
                    }
                    if (!this.sizes[i].contentElements[j].height) {
                        this.sizes[i].contentElements[j].height = contentElement.getHeight();
                    }

                    if (i != state) {
                        contentElement.setDisplayed(false);
                    }
                }
            }

            // activate self
            for (var j in this.sizes[state].contentElements) {
                var contentElement = item.el.child(this.sizes[state].contentElements[j].selector);
                contentElement.setDisplayed(true);
            }
        }, this);
    },
    _change: function(item) {
        var state = item.getState();

        // measure old elements
        var contentElementsOldSizes = {};
        for (var j in this.sizes[item.oldState].contentElements) {
            var contentElement = item.el.child(this.sizes[item.oldState].contentElements[j].selector);
            contentElementsOldSizes[j] = contentElement.getSize();
        }

        // hide old elements
        for (var i in this.sizes) {
            if (i != state) {
                for (var j in this.sizes[i].contentElements) {
                    var contentElement = item.el.child(this.sizes[i].contentElements[j].selector);
                    contentElement.setDisplayed(false);
                }
            }
        }

        // display new elements
        for (var j in this.sizes[state].contentElements) {
            var contentElement = item.el.child(this.sizes[state].contentElements[j].selector);
            contentElement.setDisplayed(true);
        }

        // get the biggest element to later animate the item
        var maxWidth = 0;
        var maxHeight = 0;
        item.el.select('*').each(function(childEl) {
            if (childEl.getWidth() > maxWidth) maxWidth = childEl.getWidth();
            if (childEl.getHeight() > maxHeight) maxHeight = childEl.getHeight();
        }, this);
        
        // set the inner content elements to the old size and animate them to their original size
        for (var j in this.sizes[state].contentElements) {
            var contentElement = item.el.child(this.sizes[state].contentElements[j].selector);

            contentElement.setSize(contentElementsOldSizes[j].width, contentElementsOldSizes[j].height);
            contentElement.setSize(this.sizes[state].contentElements[j].width, this.sizes[state].contentElements[j].height, true);
        }

        item.el.setSize(
            maxWidth+item.el.getPadding('lr'),
            maxHeight+item.el.getPadding('tb'),
            true
        );
        
        item.oldState = state;
    }
});
