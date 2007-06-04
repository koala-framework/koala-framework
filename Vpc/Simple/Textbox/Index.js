Ext.namespace('Vpc.Simple', 'Vpc.Simple.Textbox');
Vpc.Simple.Textbox.Index = function(renderTo, config) {
    var handleSave = function()
    {
        var handleFailure = function() {
            alert('Failure.');
        }
        
        form.submit({
            invalid: handleFailure,
            failure: handleFailure,
            scope: this
        });
    }

    var form = new Ext.form.Form({
        url:'/admin/component/ajaxSaveData',
        labelAlign: '',
        labelWidth: '75',
        buttonAlign: 'left',
        baseParams: { id: config.id }
    });
    
    form.add(
        new Ext.form.TextArea({
            name: 'content',
            fieldLabel: 'Text',
            width:300,
            height:200,
            allowBlank:false,
            value: config.content
        })
    );
    form.addButton('Save', handleSave, this);
    
    form.render(renderTo);
}
