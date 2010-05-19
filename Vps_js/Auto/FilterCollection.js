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
	first : true,
	
    applyToTbar : function(tbar)
    {
        this.each(function(f) {
        	first = this.first;
            if (f.right) {
                tbar.add('->');
                f.label += ' ';
            } else if(first && tbar.length > 0) {
                tbar.add('-');
            }
            if (first && !f.label) f.label = 'Filter:';
            if (f.label) {
                if (!first) {
                    f.label = '  '+f.label;
                }
                tbar.add(f.label);
            } else {
                if (!first) {
                    tbar.add('  ');
                }
            }
            f.getToolbarItem().each(function(i) {
                tbar.add(i);
            });
            this.first = false;
        }, this);
    }
});
