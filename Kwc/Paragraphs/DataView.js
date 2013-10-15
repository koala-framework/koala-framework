Kwc.Paragraphs.DataView = Ext.extend(Ext.DataView, {
    autoHeight: true,
    multiSelect: true,
    overClass: 'x-view-over',
    itemSelector: 'div.paragraph-wrap',
    showToolbars: true,
    showDelete: true,
    showPosition: true,
    showCopyPaste: true,
    border: false,
    initComponent: function()
    {
        this.componentConfigs = {};

        this.addEvents('delete', 'edit', 'changeVisible', 'changeDeviceVisible', 'changePos',
            'addParagraphMenuShow', 'addParagraph', 'copyParagraph',
            'pasteParagraph', 'copyPasteMenuShow');
        this.tpl = new Ext.XTemplate(
            '<tpl for=".">',
                '<div class="paragraph-wrap<tpl if="!visible"> kwc-paragraph-invisible</tpl>" id="kwc-paragraphs-{id}" style="width:'+this.width+'px">',
                    '<div class="kwc-paragraphs-toolbar"></div>',
                    '<div class="webStandard kwc-paragraphs-preview">{preview}</div>',
                '</div>',
            '</tpl>',
            '<div class="x-clear"></div>'
        );

        //buffer callOnContentReady for better performance when changing multiple rows - which happens when
        //deleting and entry and all below have to be re-numbered
        this.callOnContentReadyTask = new Ext.util.DelayedTask(function() {
            Kwf.callOnContentReady(this.el, { newRender: true });
        }, this);

        Kwc.Paragraphs.DataView.superclass.initComponent.call(this);
    },

    onUpdate: function() {
        var ret = Kwc.Paragraphs.DataView.superclass.onUpdate.apply(this, arguments);
        this.updateToolbars();
        return ret;
    },
    onAdd: function() {
        var ret = Kwc.Paragraphs.DataView.superclass.onAdd.apply(this, arguments);
        this.updateToolbars();
        return ret;
    },
    refresh: function() {
        var ret = Kwc.Paragraphs.DataView.superclass.refresh.apply(this, arguments);
        this.updateToolbars();
        return ret;
    },
    updateToolbars: function()
    {
        this.callOnContentReadyTask.delay(10);

        if (!this.showToolbars) return;
        var nodes = this.getNodes();
        for (var i=0; i< nodes.length; i++) {
            var node = nodes[i];
            if (node.hasParagraphsToolbarRendered) {
                continue;
            }
            node.hasParagraphsToolbarRendered = true;
            var tbCt = Ext.get(node).down('.kwc-paragraphs-toolbar');
            var tb = new Ext.Toolbar({
                renderTo: tbCt
            });
            var record = this.getRecord(node);

            this.configureToolbar(tb, record);
        }
    },
    configureToolbar: function(tb, record) {
        tb.add({
            //text: record.get('visible') ? trlKwf('visible') : trlKwf('invisible'),
            tooltip: trlKwf('visibility'),
            scope: this,
            record: record,
            handler: function(btn) {
                this.fireEvent('changeVisible', btn.record);
            },
            icon : '/assets/silkicons/'+(record.get('visible') ? 'tick' : 'cross') + '.png',
            cls  : 'x-btn-icon'
        });
        if (this.showDeviceVisible) {
            var deviceVisibleMenu = {
                menu: [{
                    text: trlKwf('show on all devices'),
                    icon: '/assets/kwf/images/devices/showAll.png',
                    scope: this,
                    record: record,
                    handler: function(menu) {
                        this.fireEvent('changeDeviceVisible', menu.record, 'all');
                    }
                },{
                    text: trlKwf('hide on mobile devices'),
                    icon: '/assets/kwf/images/devices/smartphoneHide.png',
                    scope: this,
                    record: record,
                    handler: function(menu) {
                        this.fireEvent('changeDeviceVisible', menu.record, 'hideOnMobile');
                    }
                },{
                    text: trlKwf('only show on mobile devices'),
                    icon: '/assets/kwf/images/devices/smartphone.png',
                    scope: this,
                    record: record,
                    handler: function(menu) {
                        this.fireEvent('changeDeviceVisible', menu.record, 'onlyShowOnMobile');
                    }
                }],
                cls  : 'x-btn-icon'
            };
            if (record.get('device_visible') == 'onlyShowOnMobile') {
                deviceVisibleMenu.icon = '/assets/kwf/images/devices/smartphone.png';
            } else if (record.get('device_visible') == 'hideOnMobile') {
                deviceVisibleMenu.icon = '/assets/kwf/images/devices/smartphoneHide.png';
            } else if (record.get('device_visible') == 'all') {
                deviceVisibleMenu.icon = '/assets/kwf/images/devices/showAll.png';
            }
            tb.add(deviceVisibleMenu);
        }

        if (this.showPosition) {
            var posCombo = new Kwf.Form.ComboBox({
                listClass: 'kwc-paragraphs-pos-list',
                tpl: '<tpl for=".">' +
                    '<div class="x-combo-list-item<tpl if="visible"> visible</tpl><tpl if="!visible"> invisible</tpl>">'+
                        '{pos} - {component_name}'+
                    '</div>'+
                    '</tpl>',
                displayField: 'pos',
                valueField: 'pos',
                store: this.store,
                editable: false,
                width: 50,
                triggerAction: 'all',
                mode: 'local',
                record: record,
                listWidth: 100,
                listeners: {
                    scope: this,
                    changevalue: function(v, combo) {
                        if (v && combo.record.get('pos') != v) {
                            this.fireEvent('changePos', combo.record, v);
                            combo.blur();
                            combo.hasFocus = false; //ansonsten wird die list angezeigt nachdem daten geladen wurden
                        }
                    }
                }
            });
            posCombo.setValue(record.get('pos'));
            tb.add(posCombo);
        } else {
            tb.add(record.get('pos'));
        }
        if (this.showDelete) {
            tb.add({
                tooltip: trlKwf('delete'),
                scope: this,
                record: record,
                handler: function(btn) {
                    this.fireEvent('delete', btn.record);
                },
                icon : '/assets/silkicons/bin.png',
                cls  : 'x-btn-icon'
            });
        }
        if (record.get('edit_components').length == 1) {
            tb.add('-');
            tb.add({
                text: trlKwf('edit'),
                scope: this,
                record: record,
                handler: function(btn) {
                    this.fireEvent('edit', btn.record, Kwf.clone(btn.record.get('edit_components')[0]));
                },
                icon : '/assets/silkicons/application_edit.png',
                cls  : 'x-btn-text-icon'
            });
        } else if (record.get('edit_components').length > 1) {
            tb.add('-');
            var menu = [];
            record.get('edit_components').forEach(function(ec) {
                var cfg = this.componentConfigs[ec.componentClass+'-'+ec.type];
                menu.push({
                    text: cfg.title,
                    icon: cfg.icon,
                    scope: this,
                    record: record,
                    editComponent: ec,
                    handler: function(menu) {
                        this.fireEvent('edit', menu.record, Kwf.clone(menu.editComponent));
                    }
                });
            }, this);
            tb.add({
                text: trlKwf('edit'),
                menu: menu,
                icon : '/assets/silkicons/application_edit.png',
                cls  : 'x-btn-text-icon'
            });
        }
        if (this.components) {
            tb.add('-');
            tb.add(new Kwc.Paragraphs.AddParagraphButton({
                record: record,
                components: this.components,
                componentIcons: this.componentIcons,
                listeners: {
                    scope: this,
                    menushow: function(btn) {
                        this.fireEvent('addParagraphMenuShow', btn.record);
                    },
                    addParagraph: function(component) {
                        this.fireEvent('addParagraph', component);
                    }
                }
            }));
            if (this.showCopyPaste) {
                tb.add({
                    text: trlKwf('copy/paste'),
                    menu: [{
                        text: trlKwf('Copy Paragraph'),
                        icon: '/assets/silkicons/page_white_copy.png',
                        scope: this,
                        record: record,
                        handler: function(btn) {
                            this.fireEvent('copyParagraph', btn.record);
                        }
                    },{
                        text: trlKwf('Paste Paragraph'),
                        icon: '/assets/silkicons/page_white_copy.png',
                        scope: this,
                        handler: function() {
                            this.fireEvent('pasteParagraph');
                        }
                    }],
                    icon: '/assets/silkicons/page_white_copy.png',
                    cls  : 'x-btn-text-icon',
                    record: record,
                    listeners: {
                        scope: this,
                        menushow: function(btn) {
                            this.fireEvent('copyPasteMenuShow', btn.record);
                        }
                    }
                });
            }
        }
        tb.add('->');
        tb.add(record.get('component_name'));
        tb.add('<img src="'+record.get('component_icon')+'">');
    }
});
