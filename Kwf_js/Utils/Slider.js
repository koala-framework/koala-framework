//override to allow snapping to maxValue
//properly fixed in Ext 4
Ext2.Slider.prototype.originalDoSnap = Ext2.Slider.prototype.doSnap;
Ext2.Slider.prototype.doSnap = function(value) {
    if (this.maxValue - value < this.increment / 2) {
        return parseInt(this.maxValue);
    }
    return Ext2.Slider.prototype.originalDoSnap.call(this, value);
};
