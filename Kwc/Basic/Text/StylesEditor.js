Ext2.namespace('Kwc.Basic.Text');
Kwc.Basic.Text.StylesEditor = Ext2.extend(Ext2.Window,
{
    title: trlKwf('Edit Styles'),
    modal: true,
    width: 650,
    height: 400,
    layout: 'fit',
    closeAction: 'hide',
    initComponent: function()
    {
        this.block = new Kwc.Basic.Text.StylesEditorTab({
                title: trlKwf('Block-Styles'),
                controllerUrl: this.blockStyleUrl
            });
        this.inline = new Kwc.Basic.Text.StylesEditorTab({
                title: trlKwf('Inline-Styles'),
                controllerUrl: this.inlineStyleUrl
            });
        this.items = new Ext2.TabPanel({
            items: [this.block, this.inline],
            activeTab: 0
        });

        Kwc.Basic.Text.StylesEditor.superclass.initComponent.call(this);
    },
    applyBaseParams: function(params) {
        this.block.applyBaseParams(params);
        this.inline.applyBaseParams(params);
    },
    show: function() {
        this.block.load();
        this.inline.load();
        Kwc.Basic.Text.StylesEditor.superclass.show.call(this);
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

        this.preview = new Ext2.Panel({
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
                if (i instanceof Kwf.Form.ColorField) {
                    values[i.getName()] = '#'+i.getValue();
                } else if (i instanceof Ext2.form.NumberField) {
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

Ext2.reg('kwc.basic.text.styleseditor', Kwc.Basic.Text.StylesEditor);

