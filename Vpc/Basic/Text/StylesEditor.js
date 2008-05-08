Ext.namespace('Vpc.Basic.Text');
Vpc.Basic.Text.StylesEditor = Ext.extend(Ext.Window,
{
    title: trlVps('Edit Styles'),
    modal: true,
    width: 600,
    height: 400,
    layout: 'fit',
    closeAction: 'hide',
    initComponent: function()
    {
        this.inlineForm = new Vps.Auto.FormPanel({
            controllerUrl: '/admin/component/edit/Vpc_Basic_Text_InlineStyle',
            region: 'center',
            autoLoad: true
        });
        this.blockForm = new Vps.Auto.FormPanel({
            controllerUrl: '/admin/component/edit/Vpc_Basic_Text_BlockStyle',
            region: 'center',
            autoLoad: true
        });
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
        */

        this.inlineGrid = new Vps.Auto.GridPanel({
            controllerUrl: '/admin/component/edit/Vpc_Basic_Text_InlineStyles',
            region: 'west',
            width: 250,
            split: true,
            autoLoad: false,
            bindings: [this.inlineForm]
        });
        this.blockGrid = new Vps.Auto.GridPanel({
            controllerUrl: '/admin/component/edit/Vpc_Basic_Text_BlockStyles',
            region: 'west',
            width: 250,
            split: true,
            autoLoad: false,
            bindings: [this.blockForm]
        });

        /*
        this.preview = new Ext.Panel({
            region: 'south',
            width: 150,
            split: true,
            html: 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.'
        });
        */
        this.items = new Ext.TabPanel({
            items: [{
                title: trlVps('Block-Styles'),
                layout: 'border',
                items: [this.blockGrid, this.blockForm]
            },{
                title: trlVps('Inline-Styles'),
                layout: 'border',
                items: [this.inlineGrid, this.inlineForm]
            }],
            activeTab: 0
        });

        Vpc.Basic.Text.StylesEditor.superclass.initComponent.call(this);
    },
    applyBaseParams: function(params) {
        this.blockGrid.applyBaseParams(params);
        this.blockForm.applyBaseParams(params);
        this.inlineGrid.applyBaseParams(params);
        this.inlineForm.applyBaseParams(params);
    },
    show: function() {
        this.blockGrid.load();
        this.blockGrid.clearSelections()
        this.blockForm.disable();
        this.inlineGrid.load();
        this.inlineGrid.clearSelections()
        this.inlineForm.disable();
        Vpc.Basic.Text.StylesEditor.superclass.show.call(this);
    }
/*
    _reloadPreview: function()
    {
        var styles = '';
        var values = {};
        debugger;
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
        console.log(styles);
    }
*/
});

Ext.reg('vpc.basic.text.styleseditor', Vpc.Basic.Text.StylesEditor);

