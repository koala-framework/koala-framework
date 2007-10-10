/*
Ext.namespace('Vpc.Rte');
Vpc.Basic.Rte.Index = function(renderTo, config) {
    var form = new Vps.Auto.Form(renderTo, { controllerUrl: config.controllerUrl });
    form.on('loaded', function(o) {
        var field = this.form.findField('text');
        var toolbar = field.getToolbar();
        toolbar.addButton({
            tooltip: 'Bearbeiten',
            disabled: true,
            handler :
                function (o, e) {
                    alert('foo');
                },
            icon : '/assets/vps/images/silkicons/page_edit.png',
            cls: "x-btn-icon",
            scope   : this
        });

    }, form)
}
*/