Kwf.Form.HtmlEditor.Styles = function(config) {
    Ext2.apply(this, config);

    this.editStyles = new Ext2.Action({
        testId: 'editStyles',
        icon: '/assets/silkicons/style_edit.png',
        handler: function() {
            this.stylesEditorDialog.show();
        },
        scope: this,
        tooltip: {
            cls: 'x2-html-editor-tip',
            title: trlKwf('Edit Styles'),
            text: trlKwf('Modify and Create Styles.')
        },
        cls: 'x2-btn-icon',
        clickEvent: 'mousedown',
        tabIndex: -1
    });

    if (this.stylesEditorConfig) {
        this.stylesEditorDialog = Ext2.ComponentMgr.create(this.stylesEditorConfig);
        this.stylesEditorDialog.on('hide', this._reloadStyles, this);
    }
};

Ext2.extend(Kwf.Form.HtmlEditor.Styles, Ext2.util.Observable, {
    stylesIdPattern: null,
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.on('initialize', this.onInit, this, {delay: 1, single: true});
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
        this.cmp.afterMethod('updateToolbar', this.updateToolbar, this);
        this.cmp.afterMethod('setValue', this.setValue, this);
        this.cmp.afterMethod('onRender', this.onRender, this);
        this.cmp.afterMethod('toggleSourceEdit', this.toggleSourceEdit, this);

        this.select = {};
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
        this._renderStylesSelect('block');
        this._renderStylesSelect('inline');
        tb.tr = tb.originalTr;
    },

    // private
    updateToolbar: function()
    {
        if (this.select.block.select) {
            var v = 'blockdefault';
            this.styles.forEach(function(style) {
                if (style.type == 'block' && this.cmp.formatter.match(style.id)) {
                    v = style.id;
                }
            }, this);
            this.select.block.select.setValue(v);
        }
        if (this.select.inline.select) {
            var v = 'inlinedefault';
            this.styles.forEach(function(style) {
                if (style.type == 'inline' && this.cmp.formatter.match(style.id)) {
                    v = style.id;
                }
            }, this);
            this.select.inline.select.setValue(v);
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
        Ext2.Ajax.request({
            params: {
                componentId: this.cmp.componentId
            },
            url: this.cmp.controllerUrl+'/json-styles',
            success: function(response, options, result) {
                this.styles = result.styles;
                this.registerStyles();
                this._renderStylesSelect('block');
                this._renderStylesSelect('inline');
                if (this.cmp.activated) this.cmp.updateToolbar();
            },
            scope: this
        });
    },

    filterStylesByType: function(type)
    {
        var ret = [];
        this.styles.forEach(function(s) {
            if (s.type == type) ret.push(s);
        }, this);
        return ret;
    },
    _renderStylesSelect: function(type)
    {
        if (!this.select[type]) {
            this.select[type] = {};
        }
        var select = this.select[type];
        if (!select.select) {
            select.select = new Kwf.Form.ComboBox({
                testId: type+'StyleSelect',
                editable: false,
                triggerAction: 'all',
                forceSelection: true,
                tpl: '<tpl for="."><div class="x2-combo-list-item kwfUp-webStandard kwcText"><{tagName:htmlEncode} class="{className:htmlEncode}">{name:htmlEncode}</{tagName:htmlEncode}></div></tpl>',
                mode: 'local',
                width: 150,
                listWidth: 450,
                store: new Ext2.data.JsonStore({
                    autoDestroy: true,
                    fields: ['id', 'name', 'tagName', 'className'],
                    data: this.filterStylesByType(type)
                })
            });
            select.select.on('select', function(combo) {
                var val = combo.getValue();
                combo.blur();
                combo.triggerBlur(); //hack für ext hack: da wir den focus in einen anderen frame setzen bekommt die combobox das nicht mit
                                     //mit diesem aufruf wird ihr gesagt dass sie keinen focus mehr hat
                                     //(ansonsten triggert das focus event beim nächsten mal nicht)

                //bookmark der im on focus gesetzt wurde wiederherstellen
                this.cmp.tinymceEditor.selection.moveToBookmark(this.beforeFocusBookmark);
                this.beforeFocusBookmark = null;
                this.cmp.focus();
                this.styles.forEach(function(style) {
                    if (style.type == type) {
                        this.cmp.formatter.remove(style.id);
                    }
                }, this);
                this.cmp.formatter.apply(val);
                this.cmp.deferFocus();
                this.cmp.updateToolbar();
            }, this, {delay: 1}); //delay ist notwendig da sonst der focus erneut beim select landet wenn ein item angeklickt wird
            select.select.on('focus', function() {
                //bookmark der aktuellen selection merken bevor der focus genommen wird. im IE wird sonst der cursor
                //immer ganz nach vorne gesetzt wenn die selection colapsed ist
                this.beforeFocusBookmark = this.cmp.tinymceEditor.selection.getBookmark(1);
            }, this);
            var tb = this.cmp.getToolbar();
            tb.tr = tb.stylesTr;
            var text = (type == 'block' ? trlKwf('Block') : trlKwf('Inline'));
            var offset = (type == 'block' ? 0 : 3);
            select.toolbarText = tb.insert(offset+0, text+':');
            select.toolbarItem = tb.insert(offset+1, select.select);
            select.separator = tb.insert(offset+2, '-');
            tb.tr = tb.originalTr;
        } else {
            select.select.store.loadData(this.filterStylesByType(type));
        }

        if (this.filterStylesByType(type).length > 1) {
            select.toolbarText.show();
            select.toolbarItem.show();
            select.separator.show();
        } else {
            select.toolbarText.hide();
            select.toolbarItem.hide();
            select.separator.hide();
        }
    }

});
