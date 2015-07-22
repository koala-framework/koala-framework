var onReady = require('kwf/on-ready');

onReady.onContentReady(function()
{
    var MarqueeComponents = Ext2.query('div.kwfMarqueeElements');
    Ext2.each(MarqueeComponents, function(c) {
        if (!c.kwfMarqueeInitDone) {
            var config = Ext2.decode(Ext2.query('> input.settings', c)[0].value);
            config.selectorRoot = new Ext2.Element(c);
            var Marquee = new Kwf.Marquee.Elements(config);
            Marquee.start();
            c.kwfMarqueeInitDone = true;
        }
    });
});

Ext2.namespace("Kwf.Marquee");
Kwf.Marquee.Elements = function(cfg) {
    this.selector = cfg.selector;
    this.selectorRoot = cfg.selectorRoot;
    this.scrollAmount = cfg.scrollAmount;
    this.scrollDelay = cfg.scrollDelay;
    this.scrollDirection = cfg.scrollDirection;
};

Kwf.Marquee.Elements.prototype = {
    paused: false,
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
            el = new Ext2.Element(el);
            this.elements.push({
                el: el,
                size: this._elSize(el)
            });
            this.sumSize += this._elSize(el);
        }, this);
        if (!this.elements.length) return;
        this.selectorRoot.on('mouseover', function() {
            this.paused = true;
        }, this);
        this.selectorRoot.on('mouseout', function() {
            this.paused = false;
        }, this);
        this.currentPosition = parseInt(this.readCookie()) || 0;
        this.doScroll();
    },

    doScroll: function() {
        if (!this.paused) {
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

            this.currentPosition += this.scrollAmount;
            if (this.currentPosition > this.sumSize) this.currentPosition = 0;
        }
        this.doScroll.defer(this.scrollDelay, this);

        this.setCookie(this.currentPosition);
    },

    // private
    readCookie : function(){
        var c = document.cookie + ";";
        var re = /\s?(.*?)=(.*?);/g;
        var matches;
        while((matches = re.exec(c)) != null){
            var name = matches[1];
            var value = matches[2];
            if(name == "kwfMarqueePosition"){
                return value;
            }
        }
        return null;
    },

    // private
    setCookie : function(value){
        //TODO: möglicherweise mal mehrere marquees auf einer seite ermöglichen
        document.cookie = "kwfMarqueePosition="+value+"; path=/; domain="+document.location.host+";";
    }
};
