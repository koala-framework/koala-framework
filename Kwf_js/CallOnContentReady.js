Kwf.callOnContentReady = function(el, options) {
    if (!options) options = {};
    if (Ext.Element && el instanceof Ext.Element) el = el.dom;
    if (Kwf.callOnContentReadyJQuery) Kwf.callOnContentReadyJQuery(el, options);
    if (Kwf.callOnContentReadyExt) Kwf.callOnContentReadyExt(el, options);
};
