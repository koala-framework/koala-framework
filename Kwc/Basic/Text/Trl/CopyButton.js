Ext2.namespace('Kwc.Basic.Text.Trl');
Kwc.Basic.Text.Trl.CopyButton = Ext2.extend(Ext2.form.Field, {
    defaultAutoCreate : {tag: "input", type: "hidden"},
    initComponent: function() {
        Kwc.Basic.Text.Trl.CopyButton.superclass.initComponent.call(this);
    },
    afterRender: function() {
        Kwc.Basic.Text.Trl.CopyButton.superclass.afterRender.apply(this, arguments);
        this.button = new Ext2.Button({
            text: trlKwf('Adopt'),
            renderTo: this.el.parent(),
            icon: KWF_BASE_URL+'/assets/silkicons/page_white_copy.png',
            cls: 'x2-btn-text-icon',
            scope: this,
            enabled: false,
            handler: function() {
                var masterHtml = this.ownerCt.findByType('showfield')[0].getRawValue();
                var editor = this.ownerCt.ownerCt.findByType('htmleditor')[0];
                editor.setValue(masterHtml);
                editor.plugins.each(function(p) {
                    if (p instanceof Kwf.Form.HtmlEditor.Tidy) {
                        p.tidyHtml(); //corrects links
                    }
                }, this);
            }
        });
    }
});
Ext2.reg('kwc.basic.text.trl.copybutton', Kwc.Basic.Text.Trl.CopyButton);
