//override to allow snapping to maxValue
//properly fixed in Ext 4
Ext.Slider.prototpye.originalDoSnap = Ext.Slider.prototpye.doSnap;
Ext.Slider.prototpye.doSnap = function(value) {
    if (this.maxValue - value < this.increment / 2) {
        return this.maxValue;
    }
    return Ext.Slider.prototpye.originalDoSnap.call(this, value);
};
