Ext.Panel.prototype.mabySubmit = function(cb, options) {
    var ret = true;
    if (this.items) {
        this.items.each(function(i) {
            if (i.mabySubmit && !i.mabySubmit(cb, options)) {
                ret = false;
                return true;
            }
        }, this);
    }
    return ret;
};

/*
 * Fix von: http://extjs.com/forum/showthread.php?p=224621#post224621
 * Fehler trat auf im IE7 in Stargate bei folgender Kombination:
 * Panel -> TabPanel -> FormPanel -> Columns -> Column -> TextArea
 * Wenn die Textarea scrollbar ist und man hin und her scrollt kommt es
 * vor, dass man bei der äußeren scrollbar nicht nach ganz oben scrollen
 * kann und somit auch nicht mehr speichern kann.
 */
Ext.Panel.override({
    setAutoScroll: function() {
        if (this.rendered && this.autoScroll) {
            var el = this.body || this.el;
            if (el) {
                el.setOverflow('auto');
                // Following line required to fix autoScroll
                el.dom.style.position = 'relative';
            }
        }
    }
});
