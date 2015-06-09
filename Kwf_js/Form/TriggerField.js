Ext2.form.TriggerField.prototype.helpTextOffset = [10+13, 2]; //js ist supa :D

//borrowed from http://www.sencha.com/forum/showthread.php?84612-2.x2-TriggerField-width-problem-in-TabPanel-with-deferredRender-false
Ext2.form.TriggerField.override({
    defaultTriggerWidth: 17,

    onResize: function(w, h) {
        Ext2.form.TriggerField.superclass.onResize.call(this, w, h);
        var tw = this.getTriggerWidth();
        if(typeof w == 'number'){
            this.el.setWidth(this.adjustWidth('input', w - tw));
        }
        var elWidth = this.el.getWidth();
        this.wrap.setWidth(elWidth ? (elWidth + tw) : w);
    },

    getTriggerWidth: function() {
        var tw = this.trigger.getWidth();
        if (!this.hideTrigger && tw === 0) {
            tw = this.defaultTriggerWidth;
        }
        return tw;
    }
});

Ext2.form.TwinTriggerField.override({
    defaultTriggerWidth: 17,
    getTriggerWidth: function() {
        var tw = 0;
        Ext2.each(this.triggers, function(t, index) {
            var triggerIndex = 'Trigger' + (index + 1),
                w = t.getWidth();
            if (w === 0 && !this['hidden' + triggerIndex]) {
                tw += this.defaultTriggerWidth;
            } else {
                tw += w;
            }
        }, this);
        return tw;
    }
});
