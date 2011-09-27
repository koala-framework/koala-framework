Vps.onContentReady(function()
{
    var checkboxes = Ext.query('div.vpsFormContainerFieldSet fieldset legend input');
    Ext.each(checkboxes, function(c) {
        c = Ext.get(c);
        c.on('click', function() {
            if (this.dom.checked) {
                this.up('fieldset').removeClass('vpsFormContainerFieldSetCollapsed');
            } else {
                this.up('fieldset').addClass('vpsFormContainerFieldSetCollapsed');
            }
        }, c);
    });
});

