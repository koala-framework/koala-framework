Ext.namespace('Vpc.Basic.Text');
Vpc.Basic.Text.StylesEditor = Ext.extend(Ext.Window,
{
    title: trlVps('Edit Styles'),
    modal: true,
    width: 650,
    height: 400,
    layout: 'fit',
    closeAction: 'hide',
    initComponent: function()
    {
        this.block = new Vpc.Basic.Text.StylesEditorTab({
                title: trlVps('Block-Styles'),
                controllerUrl: this.blockStyleUrl
            });
        this.inline = new Vpc.Basic.Text.StylesEditorTab({
                title: trlVps('Inline-Styles'),
                controllerUrl: this.inlineStyleUrl
            });
        this.items = new Ext.TabPanel({
            items: [this.block, this.inline],
            activeTab: 0
        });

        Vpc.Basic.Text.StylesEditor.superclass.initComponent.call(this);
    },
    applyBaseParams: function(params) {
        this.block.applyBaseParams(params);
        this.inline.applyBaseParams(params);
    },
    show: function() {
        this.block.load();
        this.inline.load();
        Vpc.Basic.Text.StylesEditor.superclass.show.call(this);
    }
/*
        this.form.on('renderform', function() {
            this.form.getForm().items.each(function(i) {
                if (i.isFormField) {
                    i.on('change', function(field, newValue, oldValue) {
                        this._reloadPreview();
                    }, this);
                }
            }, this);
        }, this);
        this.form.on('loadform', this._reloadPreview, this);

        this.preview = new Ext.Panel({
            region: 'south',
            width: 150,
            split: true,
            html: 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.'
        });
    _reloadPreview: function()
    {
        var styles = '';
        var values = {};
        this.form.getForm().items.each(function(i) {
            if (i.isFormField && i.getValue()) {
                if (i instanceof Vps.Form.ColorField) {
                    values[i.getName()] = '#'+i.getValue();
                } else if (i instanceof Ext.form.NumberField) {
                    values[i.getName()] = i.getValue()+'px';
                } else {
                    values[i.getName()] = i.getValue();
                }
            }
        });
        for (var property in values) {
            if (property != 'name' && property != 'tag'
                && property != 'className') {
                styles += property.replace('_', '-');
                styles += ': ';
                styles += values[property];
                styles += '; ';
            }
        }
        var html = '<'+values.tag+' style="'+styles+'">Lorem ipsum dolor sit amet, ';
        html += 'consectetuer adipiscing elit.</'+values.tag+'>';
        this.preview.body.update(html);
    }
*/
});

Ext.reg('vpc.basic.text.styleseditor', Vpc.Basic.Text.StylesEditor);

