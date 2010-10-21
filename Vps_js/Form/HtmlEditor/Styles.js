Vps.Form.HtmlEditor.Styles = function(config) {
    Ext.apply(this, config);

    this.editStyles = new Ext.Action({
        testId: 'editStyles',
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
        this.registerStyles();
    },

    setValue: function(v) {
        if (v && v.componentId) {
            if (this.stylesIdPattern) {
                var m = v.componentId.match(this.stylesIdPattern);
                m = m ? m[0] : null;
                if (this.ownStylesParam != m) {
                    this.ownStylesParam = m;
                    this._reloadStyles();
                }
            }
        }
        if (this.stylesEditorDialog) {
            this.stylesEditorDialog.applyBaseParams({
                componentId: this.cmp.componentId,
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
            var v = 'blockdefault';
            this.styles.forEach(function(style) {
                if (style.type == 'block' && this.cmp.formatter.match(style.id)) {
                    v = style.id;
                }
            }, this);
            this.blockStylesSelect.setValue(v);
        }
        if (this.inlineStylesSelect) {
            var v = 'inlinedefault';
            this.styles.forEach(function(style) {
                if (style.type == 'inline' && this.cmp.formatter.match(style.id)) {
                    v = style.id;
                }
            }, this);
            this.inlineStylesSelect.setValue(v);
        }
    },

    registerStyles: function() {
        this.styles.forEach(function(style) {
            var s = {};
            s[style.type] = style.tagName;
            if (style.className) s.classes = style.className;
            this.cmp.formatter.register(style.id, s);
        }, this);
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
                componentId: this.cmp.componentId
            },
            url: this.cmp.controllerUrl+'/json-styles',
            success: function(response, options, result) {
                this.styles = result.styles;
                this.registerStyles();
                this._renderInlineStylesSelect();
                this._renderBlockStylesSelect();
                if (this.cmp.activated) this.cmp.updateToolbar();
            },
            scope: this
        });
    },

    _renderInlineStylesSelect: function() {
        var stylesLength = 0;
        this.styles.forEach(function(style) { if (style.type=='inline') stylesLength++; }, this);
        if (stylesLength > 1) {
            if (!this.inlineStylesSelect) {
                this.inlineStylesSelect = new Vps.Form.ComboBox({
                    editable: false,
                    triggerAction: 'all',
                    forceSelection: true,
                    tpl: '<tpl for="."><div class="x-combo-list-item webStandard vpcText"><{tagName} class="{className}">{name}</{tagName}></div></tpl>',
                    mode: 'local',
                    width: 65,
                    store: new Ext.data.JsonStore({
                        autoDestroy: true,
                        fields: ['id', 'name', 'tagName', 'className'],
                        data: this.filterStylesByType('inline')
                    })
                });
                this.inlineStylesSelect.on('select', function() {
                    this.inlineStylesSelect.blur();
                    this.inlineStylesSelect.triggerBlur();
                    this.cmp.tinymceEditor.selection.moveToBookmark(this.beforeFocusBookmark);
                    this.beforeFocusBookmark = null;
                    this.cmp.focus();
                    var v = this.inlineStylesSelect.getValue();
                    this.styles.forEach(function(style) {
                        if (style.type == 'inline') {
                            this.cmp.formatter.remove(style.id);
                        }
                    }, this);
                    this.cmp.formatter.apply(v);
                    this.cmp.deferFocus();
                    this.cmp.updateToolbar();
                }, this, {delay: 1});
                this.inlineStylesSelect.on('focus', function() {
                    this.beforeFocusBookmark = this.cmp.tinymceEditor.selection.getBookmark(1);
                }, this);
                var offs = 0;
                if (this.blockStylesToolbarItem && !this.blockStylesToolbarItem.hidden) {
                    offs = 3;
                }
                var tb = this.cmp.getToolbar();
                tb.tr = tb.stylesTr;
                this.inlineStylesToolbarText = tb.insert(offs, trlVps('Inline')+':');
                this.inlineStylesToolbarItem = tb.insert(offs+1, this.inlineStylesSelect);
                this.inlineStylesSeparator = tb.insert(offs+2, '-');
                tb.tr = tb.originalTr;
            } else {
                this.inlineStylesToolbarText.show();
                this.inlineStylesToolbarItem.show();
                this.inlineStylesSeparator.show();
                this.inlineStylesSelect.store.loadData(this.filterStylesByType('inline'));
            }
        } else {
            if (this.inlineStylesSelect) {
                this.inlineStylesToolbarText.hide();
                this.inlineStylesToolbarItem.hide();
                this.inlineStylesSeparator.hide();
            }
        }
    },
    filterStylesByType: function(type)
    {
        var ret = [];
        this.styles.forEach(function(s) {
            if (s.type == type) ret.push(s);
        }, this);
        return ret;
    },
    _renderBlockStylesSelect: function() {
        var stylesLength = 0;
        this.styles.forEach(function(style) { if (style.type=='block') stylesLength++; }, this);
        if (stylesLength > 1) {
            if (!this.blockStylesSelect) {
                this.blockStylesSelect = new Vps.Form.ComboBox({
                    editable: false,
                    triggerAction: 'all',
                    forceSelection: true,
                    tpl: '<tpl for="."><div class="x-combo-list-item webStandard vpcText"><{tagName} class="{className}">{name}</{tagName}></div></tpl>',
                    mode: 'local',
                    width: 65,
                    store: new Ext.data.JsonStore({
                        autoDestroy: true,
                        fields: ['id', 'name', 'tagName', 'className'],
                        data: this.filterStylesByType('block')
                    })
                });
                this.blockStylesSelect.on('select', function() {
                    this.blockStylesSelect.blur();
                    this.blockStylesSelect.triggerBlur();
                    this.cmp.tinymceEditor.selection.moveToBookmark(this.beforeFocusBookmark);
                    this.beforeFocusBookmark = null;
                    this.cmp.focus();
                    var v = this.blockStylesSelect.getValue();
                    this.styles.forEach(function(style) {
                        if (style.type == 'block') {
                            this.cmp.formatter.remove(style.id);
                        }
                    }, this);
                    this.cmp.formatter.apply(v);
                    this.cmp.deferFocus();
                    this.cmp.updateToolbar();
                }, this, {delay: 1});
                this.blockStylesSelect.on('focus', function() {
                    this.beforeFocusBookmark = this.cmp.tinymceEditor.selection.getBookmark(1);
                }, this);
                var tb = this.cmp.getToolbar();
                tb.tr = tb.stylesTr;
                this.blockStylesToolbarText = tb.insert(0, trlVps('Block')+':');
                this.blockStylesToolbarItem = tb.insert(1, this.blockStylesSelect);
                this.blockStylesSeparator = tb.insert(2, '-');
                tb.tr = tb.originalTr;
            } else {
                this.blockStylesToolbarText.show();
                this.blockStylesToolbarItem.show();
                this.blockStylesSeparator.show();
                this.inlineStylesSelect.store.loadData(this.filterStylesByType('block'));
            }
        } else {
            if (this.blockStylesSelect) {
                this.blockStylesToolbarText.hide();
                this.blockStylesToolbarItem.hide();
                this.blockStylesSeparator.hide();
            }
        }
    }

});