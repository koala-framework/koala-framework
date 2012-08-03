//override to allow snapping to maxValue
//properly fixed in Ext 4
Ext.Slider.prototype.originalDoSnap = Ext.Slider.prototype.doSnap;
Ext.Slider.prototype.doSnap = function(value) {
    if (this.maxValue - value < this.increment / 2) {
        return parseInt(this.maxValue);
    }
    return Ext.Slider.prototype.originalDoSnap.call(this, value);
};
