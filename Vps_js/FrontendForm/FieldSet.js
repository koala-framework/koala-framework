Vps.onContentReady(function()
{
    var checkboxes = Ext.query('div.vpsFormContainerFieldSet fieldset legend input');
    Ext.each(checkboxes, function(c) {
        c = Ext.get(c);
        if (!c.dom.checked) {
            c.up('fieldset').addClass('vpsFormContainerFieldSetCollapsed');
        }
        c.on('click', function() {
            if (this.dom.checked) {
                this.up('fieldset').removeClass('vpsFormContainerFieldSetCollapsed');
            } else {
                this.up('fieldset').addClass('vpsFormContainerFieldSetCollapsed');
            }
        }, c);
    });
});

