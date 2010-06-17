Ext.namespace('Vpc.Basic.Text.Trl');
Vpc.Basic.Text.Trl.CopyButton = Ext.extend(Ext.form.Field, {
    defaultAutoCreate : {tag: "input", type: "hidden"},
    initComponent: function() {
        Vpc.Basic.Text.Trl.CopyButton.superclass.initComponent.call(this);
    },
    afterRender: function() {
        Vpc.Basic.Text.Trl.CopyButton.superclass.afterRender.apply(this, arguments);
        this.button = new Ext.Button({
            text: trlVps('Adopt'),
            renderTo: this.el.parent(),
            icon: '/assets/silkicons/page_white_copy.png',
            cls: 'x-btn-text-icon',
            scope: this,
            enabled: false,
            handler: function() {
                var masterHtml = this.ownerCt.findByType('showfield')[0].getRawValue();
                this.ownerCt.ownerCt.findByType('htmleditor')[0].setValue(masterHtml);
                this.ownerCt.ownerCt.findByType('htmleditor')[0].tidyHtml(); //damit link korrigiert werden
            }
        });
    }
});
Ext.reg('vpc.basic.text.trl.copybutton', Vpc.Basic.Text.Trl.CopyButton);
