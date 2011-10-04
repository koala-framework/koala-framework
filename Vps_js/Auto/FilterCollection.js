Ext.namespace('Vps.Auto');
Vps.Auto.FilterCollection = function(filters, scope)
{
    Vps.Auto.FilterCollection.superclass.constructor.call(this);

    for (var i = 0; i < filters.length; i++) {
        var f = filters[i];
        if (!Vps.Auto.Filter[f.type]) {
            throw "Unknown filter.type: "+f.type;
        }
        var type = Vps.Auto.Filter[f.type];
        delete f.type;
        var filterField = new type(f);
        this.add(filterField);
    }
};

Ext.extend(Vps.Auto.FilterCollection, Ext.util.MixedCollection, {
    applyToTbar : function(tbar, limit, offset)
    {
        var limitCount = 0;
        var offsetCount = 0;
        var first = true;

        this.each(function(f) {
            offsetCount += 1;
            if (offset && offsetCount <= offset) return;

            limitCount += 1;
            if (limit && limitCount > limit) return;

            if (f.right) {
                tbar.add('->');
                f.label += ' ';
            } else if(first && tbar.length > 0) {
                tbar.add('-');
            }
            if (first && !f.label) f.label = trlVps('Filter')+':';
            if (f.label) {
                if (!first) {
                    f.label = ' '+f.label;
                }
                tbar.add(f.label);
            } else {
                if (!first) {
                    tbar.add(' ');
                }
            }
            f.getToolbarItem().each(function(i) {
                tbar.add(i);
            });
            first = false;
        }, this);
    }
});
