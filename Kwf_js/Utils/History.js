Ext2.ns('Kwf.History');
Kwf.History.init = function() {
    // whyever Ext2.History.init() doesn't do this itself...
    if (!document.getElementById('history-form')) {
        var form = Ext2.DomHelper.append(document.body, {
            tag: 'form', id: 'history-form', cls: 'x2-history-field', children: [
                { tag: 'input', type: 'hidden', id: 'x2-history-field' },
                { tag: 'iframe', id: 'x2-history-frame' }
            ]
        });
        Ext2.get(form).setDisplayed(false);
        Ext2.History.init();
    }
};
