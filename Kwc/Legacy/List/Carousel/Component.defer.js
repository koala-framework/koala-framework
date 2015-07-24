var onReady = require('kwf/on-ready-ext2');

Ext2.namespace("Kwc.Legacy.List.Carousel");
Kwc.Legacy.List.Carousel.Component = Ext2.extend(Kwf.EyeCandy.List, {
    childSelector: '.listItem',
    _activeChangeLocked: false,
    _init: function() {
        this.plugins = [
            new Kwc.Legacy.List.Carousel.Carousel({
                numberShown: 3,
                moveElementSelector: '.listWrapper'
            }),
            new Kwc.Legacy.List.Carousel.NextPreviousLinks()
        ];

        Kwc.Legacy.List.Carousel.Component.superclass._init.call(this);
    },
    getActiveChangeLocked: function() {
        return this._activeChangeLocked;
    },
    setActiveChangeLocked: function(value) {
        this._activeChangeLocked = value;
    }
});



onReady.onRender('.kwcClass', function(el, config){

    el.setStyle('max-width', config.contentWidth+'px');
    var wrapper = el.child('.listWrapper') ? el.child('.listWrapper') : el.child('.imageWrapper');

    function responsiveContent(el) {
        if(wrapper.hasClass('listWrapper')) {
            el.query('.listItem').each(function(listItem){
                listItem = Ext2.get(listItem);
                if(el.getWidth() < (config.contentWidth-1)) {
                    el.removeClass('fullwidth');
                    var invertedWidth = el.getWidth() * -1;
                    wrapper.setLeft(invertedWidth);
                } else {
                    el.addClass('fullwidth');
                    wrapper.setLeft(-(config.contentWidth+40));
                }
                listItem.setWidth(el.getWidth());
            })

        } else if(wrapper.hasClass('imageWrapper')) {
            var imageItem = wrapper.child('.listItem');
            wrapper.setLeft(0);
            if(imageItem && imageItem.length){
                imageItem.setWidth(el.getWidth());
            }

        }
    }

    if(wrapper.hasClass('listWrapper')) {
        var stage = new Kwc.Legacy.List.Carousel.Component({
            el: el
        });
    }

    responsiveContent(el);

    onReady.onResize('.kwcClass', responsiveContent);

});
