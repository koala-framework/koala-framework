Vps.EyeCandy.List.Plugins.Carousel = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    nextSelector: 'a.carouselNext',
    previousSelector: 'a.carouselPrevious',
    numberShown: 5,
    //animationConfig: { concurrent: true, duration: 0.25 },

    _moveActive: false,
    init: function() {
        Ext.applyIf(this, {
            animationConfig: { concurrent: true, duration: 0.25 }
        });
    },
    render: function() {
        this.list.el.parent().query(this.nextSelector).each(function(el) {
            Ext.fly(el).on('click', function(ev) {
                ev.stopEvent();
                this.onMoveNext();
            }, this);
        }, this);
        this.list.el.parent().query(this.previousSelector).each(function(el) {
            Ext.fly(el).on('click', function(ev) {
                ev.stopEvent();
                this.onMovePrevious();
            }, this);
        }, this);
        for(var i=this.numberShown; i<this.list.getItems().length; ++i) {
            this.list.getItem(i).el.hide();
        }

        if (this.list.items <= this.numberShown) {
            this.list.el.parent().query(this.previousSelector).hide();
            this.list.el.parent().query(this.nextSelector).hide();
        }
    },

    onMoveNext: function() {
        if (this._moveActive) return;
        this._moveActive = true;

        // fade out first one
        var cfg = Ext.applyIf({
            useDisplay: false
        }, this.animationConfig);
        this.list.getItem(0).el.fadeOut(cfg);

        // fade in new one
        this.list.getItem(this.numberShown).el.fadeIn(cfg);

        var firstElWidth = this.list.getItem(0).getWidthIncludingMargin();

        var cfg = Ext.applyIf({
            callback: function() {

                // push moved left element to back
                this.list.getItem(0).el.insertAfter(this.list.getLastItem().el); //move element
                this.list.items.push(this.list.items.shift()); //adapt array

                //adapt listIndex property
                var idx = 0;
                this.list.items.each(function(i) {
                    i.listIndex = idx++;
                }, this);

                // left von wrapper wieder setzen
                this.list.el.move('right', firstElWidth, false);

                this._moveActive = false;
            },
            scope: this
        }, this.animationConfig);
        this.list.el.move('left', firstElWidth, cfg);
    },

    onMovePrevious: function()
    {
        if (this._moveActive) return;
        this._moveActive = true;

        // rechts rausgeschobenes element vorn dran
        this.list.getLastItem().el.insertBefore(this.list.getItem(0).el);
        this.list.items.unshift(this.list.items.pop());

        //adapt listIndex property
        var idx = 0;
        this.list.items.each(function(i) {
            i.listIndex = idx++;
        }, this);

        // left von wrapper setzen
        this.list.el.move('left', this.list.getItem(0).getWidthIncludingMargin(), false);

        // fade in first
        var cfg = Ext.applyIf({
            useDisplay: false
        }, this.animationConfig);
        this.list.getItem(0).el.fadeIn(cfg);

        // fade out last
        this.list.getItem(this.numberShown).el.fadeOut(cfg);
        var cfg = Ext.applyIf({
            callback: function() {
                this._moveActive = false;
            },
            scope: this
        });
        this.list.el.move('right', this.list.getItem(0).getWidthIncludingMargin(), cfg);
    }

});
