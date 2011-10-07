Kwf.onContentReady(function()
{
    var checkboxes = Ext.query('div.kwfFormContainerFieldSet fieldset legend input');
    Ext.each(checkboxes, function(c) {
        c = Ext.get(c);
        if (!c.dom.checked) {
            c.up('fieldset').addClass('kwfFormContainerFieldSetCollapsed');
        }
        c.on('click', function() {
            if (this.dom.checked) {
                this.up('fieldset').removeClass('kwfFormContainerFieldSetCollapsed');
            } else {
                this.up('fieldset').addClass('kwfFormContainerFieldSetCollapsed');
            }
        }, c);
    });
});

