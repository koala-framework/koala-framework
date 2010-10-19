Vps.Form.HtmlEditor.Styles = function(config) {
    Ext.apply(this, config);

    this.editStyles = new Ext.Action({
        icon: '/assets/silkicons/style_edit.png',
        handler: function() {
            this.stylesEditorDialog.show();
        },
        scope: this,
        tooltip: {
            cls: 'x-html-editor-tip',
            title: trlVps('Edit Styles'),
            text: trlVps('Modify and Create Styles.')
        },
        cls: 'x-btn-icon',
        clickEvent: 'mousedown',
        tabIndex: -1
    });

    if (this.stylesEditorConfig) {
        this.stylesEditorDialog = Ext.ComponentMgr.create(this.stylesEditorConfig);
        this.stylesEditorDialog.on('hide', this._reloadStyles, this);
    }
};

Ext.extend(Vps.Form.HtmlEditor.Styles, Ext.util.Observable, {
    stylesIdPattern: null,
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.on('initialize', this.onInit, this, {delay: 1, single: true});
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
        this.cmp.afterMethod('updateToolbar', this.updateToolbar, this);
        this.cmp.afterMethod('setValue', this.setValue, this);
        this.cmp.afterMethod('onRender', this.onRender, this);
        this.cmp.afterMethod('toggleSourceEdit', this.toggleSourceEdit, this);
    },

    toggleSourceEdit : function(sourceEditMode) {
        //re-enable items that are possible in sourceedit
        if (this.stylesEditorToolbarItem) this.stylesEditorToolbarItem.enable();
    },

    onRender: function(ct, position) {
        //re-enable items that are possible for not-yet-active editor
        if (this.stylesEditorToolbarItem) this.stylesEditorToolbarItem.enable();
    },

    onInit: function() {
        var num = 0;
        for(var i in this.inlineStyles) {
            var selector = i.split('.');
            var tag = selector[0];
            var className = selector[1];
            this.cmp.formatter.register('inline'+num, {
                inline: tag,
                classes: className
            });
            ++num;
        }

        num = 0;
        for(var i in this.blockStyles) {
            var selector = i.split('.');
            var tag = selector[0];
            var className = selector[1];
            this.cmp.formatter.register('block'+num, {
                block: tag,
                classes: className
            });
            ++num;
        }
    },
    setValue: function(v) {
        if (v && v.componentId) {
            this.componentId = v.componentId;
            if (this.stylesIdPattern) {
                var m = this.componentId.match(this.stylesIdPattern);
                m = m ? m[0] : null;
                if (this.ownStylesParam != m) {
                    this.ownStylesParam = m;
                    this._reloadStyles();
                }
            }
        }
        if (this.stylesEditorDialog) {
            if (this.ownStylesParam) {
                this.stylesEditorDialog.master.hide();
            } else {
                this.stylesEditorDialog.master.show();
            }
            this.stylesEditorDialog.applyBaseParams({
                componentId: this.componentId,
                componentClass: this.cmp.componentClass
            });
        }
    },

    // private
    afterCreateToolbar: function()
    {
        var tb = this.cmp.getToolbar();
        var table = document.createElement('table');
        table.cellspacing = '0';
        tb.stylesTr = table.appendChild(document.createElement('tbody'))
                    .appendChild(document.createElement('tr'));
        tb.stylesTr.appendChild(document.createElement('td'));
        tb.el.appendChild(table);
        tb.tr = tb.stylesTr;
        tb.originalTr = tb.tr;
        if (this.stylesEditorDialog) {
            this.stylesEditorToolbarItem = tb.insert(0, this.editStyles);
        }
        this._renderInlineStylesSelect();
        this._renderBlockStylesSelect();
        tb.tr = tb.originalTr;
    },

    // private
    updateToolbar: function()
    {
        if (this.blockStylesSelect) {
            this.inlineStylesSelect.dom.value = 'p';
            var num = 0;
            for(var i in this.blockStyles) {
                if (this.cmp.formatter.match('block'+num)) {
                    this.blockStylesSelect.dom.value = i;
                }
                num++;
            }
        }
        if (this.inlineStylesSelect) {
            this.inlineStylesSelect.dom.value = 'span';
            var num = 0;
            for(var i in this.inlineStyles) {
                if (this.cmp.formatter.match('inline'+num)) {
                    this.inlineStylesSelect.dom.value = i;
                }
                num++;
            }
        }
    },

    createInlineStylesOptions : function(){
        var buf = [];
        for (var i in this.inlineStyles) {
            buf.push(
                '<option value="',i,'"',
                    (i == 'span' ? ' selected="true">' : '>'),
                    this.inlineStyles[i],
                '</option>'
            );
        }
        return buf.join('');
    },

    createBlockStylesOptions : function(){
        var buf = [];
        for (var i in this.blockStyles) {
            buf.push(
                '<option value="',i,'"',
                    (i == 'p' ? ' selected="true">' : '>'),
                    this.blockStyles[i],
                '</option>'
            );
        }
        return buf.join('');
    },


    _onSelectBlockStyle: function() {
        var v = this.blockStylesSelect.dom.value;
        var num = 0;
        for(var i in this.blockStyles) {
            this.cmp.formatter.remove('block'+num);
            ++num;
        }

        num = 0;
        for(var i in this.blockStyles) {
            if (i == v) {
                this.cmp.formatter.apply('block'+num);
                break;
            }
            ++num;
        }
        this.cmp.deferFocus();
        this.cmp.updateToolbar();
    },
    _onSelectInlineStyle: function() {
        var v = this.inlineStylesSelect.dom.value;
        var num = 0;
        for(var i in this.inlineStyles) {
            this.cmp.formatter.remove('inline'+num);
            ++num;
        }

        num = 0;
        for(var i in this.inlineStyles) {
            if (i == v) {
                this.cmp.formatter.apply('inline'+num);
                break;
            }
            ++num;
        }
        this.cmp.deferFocus();
        this.cmp.updateToolbar();
    },
    _reloadStyles: function() {
        var reloadCss = function(doc) {
            var href = doc.location.protocol+'/' + '/'+
                        doc.location.hostname + this.cmp.stylesCssFile;
            href = href.split('?')[0];
            var links = doc.getElementsByTagName("link");
            for (var i = 0; i < links.length; i++) {
                var l = links[i];
                if (l.type == 'text/css' && l.href
                    && l.href.split('?')[0] == href) {
                    l.parentNode.removeChild(l);
                }
            }
            var s = doc.createElement('link');
            s.setAttribute('type', 'text/css');
            s.setAttribute('href', href+'?'+Math.random());
            s.setAttribute('rel', 'stylesheet');
            doc.getElementsByTagName("head")[0].appendChild(s);
        };
        reloadCss.call(this, document);
        if (this.cmp.doc) reloadCss.call(this, this.cmp.doc);
        Ext.Ajax.request({
            params: {
                componentId: this.componentId
            },
            url: this.cmp.controllerUrl+'/json-styles',
            success: function(response, options, result) {
                this.inlineStyles = result.inlineStyles;
                this.blockStyles = result.blockStyles;
                this._renderInlineStylesSelect();
                this._renderBlockStylesSelect();
                if (this.cmp.activated) this.cmp.updateToolbar();
                //TODO re-register styles to formatter
            },
            scope: this
        });
    },

    _renderInlineStylesSelect: function() {
        var stylesLength = 0;
        for (var i in this.inlineStyles) stylesLength++;
        if (this.inlineStyles && stylesLength > 1) {
            if (!this.inlineStylesSelect) {
                this.inlineStylesSelect = this.cmp.getToolbar().el.createChild({
                    tag:'select',
                    cls:'x-font-select',
                    html: this.createInlineStylesOptions()
                });
                this.inlineStylesSelect.on('change', this._onSelectInlineStyle, this);
                var offs = 0;
                if (this.blockStylesToolbarItem && !this.blockStylesToolbarItem.hidden) {
                    offs = 3;
                }
                var tb = this.cmp.getToolbar();
                tb.tr = tb.stylesTr;
                this.inlineStylesToolbarText = tb.insert(offs, trlVps('Inline')+':');
                this.inlineStylesToolbarItem = tb.insert(offs+1, this.inlineStylesSelect.dom);
                this.inlineStylesSeparator = tb.insert(offs+2, '-');
                tb.tr = tb.originalTr;
            } else {
                this.inlineStylesToolbarText.show();
                this.inlineStylesToolbarItem.show();
                this.inlineStylesSeparator.show();
                this.inlineStylesSelect.update(this.createInlineStylesOptions());
            }
        } else {
            if (this.inlineStylesSelect) {
                this.inlineStylesToolbarText.hide();
                this.inlineStylesToolbarItem.hide();
                this.inlineStylesSeparator.hide();
            }
        }
    },
    _renderBlockStylesSelect: function() {
        var stylesLength = 0;
        for (var i in this.blockStyles) stylesLength++;
        if (this.blockStyles && stylesLength > 1) {
            if (!this.blockStylesSelect) {
                this.blockStylesSelect = this.cmp.getToolbar().el.createChild({
                    tag:'select',
                    cls:'x-font-select',
                    html: this.createBlockStylesOptions()
                });
                this.blockStylesSelect.on('change', this._onSelectBlockStyle, this);
                var tb = this.cmp.getToolbar();
                tb.tr = tb.stylesTr;
                this.blockStylesToolbarText = tb.insert(0, trlVps('Block')+':');
                this.blockStylesToolbarItem = tb.insert(1, this.blockStylesSelect.dom);
                this.blockStylesSeparator = tb.insert(2, '-');
                tb.tr = tb.originalTr;
            } else {
                this.blockStylesToolbarText.show();
                this.blockStylesToolbarItem.show();
                this.blockStylesSeparator.show();
                this.blockStylesSelect.update(this.createBlockStylesOptions());
            }
        } else {
            if (this.blockStylesSelect) {
                this.blockStylesToolbarText.hide();
                this.blockStylesToolbarItem.hide();
                this.blockStylesSeparator.hide();
            }
        }
    },

});