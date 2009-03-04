Vps.onContentReady(function()
{
    var MarqueeComponents = Ext.query('div.vpsMarqueeElements');
    Ext.each(MarqueeComponents, function(c) {
        var config = Ext.decode(Ext.query('> input.settings', c)[0].value);
        config.selectorRoot = new Ext.Element(c);
        var Marquee = new Vps.Marquee.Elements(config);
        Marquee.start();
    });
});

Ext.namespace("Vps.Marquee");
Vps.Marquee.Elements = function(cfg) {
    this.selector = cfg.selector;
    this.selectorRoot = cfg.selectorRoot;
    this.scrollAmount = cfg.scrollAmount;
    this.scrollDelay = cfg.scrollDelay;
    this.scrollDirection = cfg.scrollDirection;
};

Vps.Marquee.Elements.prototype = {

    _elSize: function(el)
    {
        if (this.scrollDirection == 'down' || this.scrollDirection == 'up') {
            return el.getHeight();
        } else {
            return el.getWidth();
        }
    },

    start: function() {
        this.sumSize = 0;
        this.elements = [];
        this.selectorRoot.query(this.selector).each(function(el) {
            el = new Ext.Element(el);
            this.elements.push({
                el: el,
                size: this._elSize(el),
            });
            this.sumSize += this._elSize(el);
        }, this);
        if (!this.elements.length) return;
        this.currentPosition = 0;
        this.doScroll.defer(this.scrollDelay, this);
    },

    doScroll: function() {
        this.currentPosition += this.scrollAmount;
        if (this.currentPosition > this.sumSize) this.currentPosition = 0;

        var offset = 0;
        this.elements.each(function(i) {
            var pos = offset - this.currentPosition;
            if (pos < -i.size) {
                pos = pos + this.sumSize;
            }
            if (this.scrollDirection == 'up') {
                i.el.setY(pos + this.selectorRoot.getY());
            } else if (this.scrollDirection == 'left') {
                i.el.setX(pos + this.selectorRoot.getX());
            } else {
                //TODO
            }
            offset += i.size;
        }, this);

        this.doScroll.defer(this.scrollDelay, this);
    }
};
