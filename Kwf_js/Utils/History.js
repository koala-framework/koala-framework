Ext.ns('Kwf.History');
Kwf.History.init = function() {
    // whyever Ext.History.init() doesn't do this itself...
    if (!document.getElementById('history-form')) {
        var form = Ext.DomHelper.append(document.body, {
            tag: 'form', id: 'history-form', cls: 'x-history-field', children: [
                { tag: 'input', type: 'hidden', id: 'x-history-field' },
                { tag: 'iframe', id: 'x-history-frame' }
            ]
        });
        Ext.get(form).setDisplayed(false);
        Ext.History.init();
    }
};
