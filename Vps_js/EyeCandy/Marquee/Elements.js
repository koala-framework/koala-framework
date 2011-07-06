Vps.onContentReady(function()
{
    var MarqueeComponents = Ext.query('div.vpsMarqueeElements');
    Ext.each(MarqueeComponents, function(c) {
        if (!c.vpsMarqueeInitDone) {
            var config = Ext.decode(Ext.query('> input.settings', c)[0].value);
            config.selectorRoot = new Ext.Element(c);
            var Marquee = new Vps.Marquee.Elements(config);
            Marquee.start();
            c.vpsMarqueeInitDone = true;
        }
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
            el = new Ext.Element(el);
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
            if(name == "vpsMarqueePosition"){
                return value;
            }
        }
        return null;
    },

    // private
    setCookie : function(value){
        //TODO: möglicherweise mal mehrere marquees auf einer seite ermöglichen
        document.cookie = "vpsMarqueePosition="+value+"; path=/; domain="+document.location.host+";";
    }
};
