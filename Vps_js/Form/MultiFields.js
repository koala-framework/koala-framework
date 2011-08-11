Vps.Form.MultiFields = Ext.extend(Ext.Panel, {
    minEntries: 1,
    position: true,
    initComponent : function() {
        Vps.Form.MultiFields.superclass.initComponent.call(this);

        this.hiddenCountValue = new Vps.Form.MultiFieldsHidden({
            name: this.name,
            multiFieldsPanel: this
        });
        this.add(this.hiddenCountValue);

        this.groups = [];
    },

    enableRecursive: function() {
        Vps.Form.MultiFields.superclass.enableRecursive.call(this);
        this.groups.each(function(g) {
            g.item.enableRecursive();
        });
    },

    disableRecursive: function() {
        Vps.Form.MultiFields.superclass.disableRecursive.call(this);
        this.groups.each(function(g) {
            g.item.disableRecursive();
        });
    },

    // private
    onRender : function(ct, position){
        Vps.Form.MultiFields.superclass.onRender.call(this, ct, position);

        if (!this.maxEntries || !this.minEntries || this.maxEntries != this.minEntries) {
            this.addGroupButton = new Vps.Form.MultiFieldsAddButton({
                multiFieldsPanel: this,
                renderTo: this.body
            }, position);
        }

        for (var i = 0; i < this.minEntries; i++) {
            this.addGroup();
        }
    },

    // private
    addGroup : function()
    {
        var items = [];
        if (!this.maxEntries || !this.minEntries || this.maxEntries != this.minEntries) {
            var deleteButton = new Vps.Form.MultiFieldsDeleteButton({
                multiFieldsPanel: this
            });
            items.push(deleteButton);
            if (this.position) {
                var upButton = new Vps.Form.MultiFieldsUpButton({
                    multiFieldsPanel: this
                });
                items.push(upButton);
                var downButton = new Vps.Form.MultiFieldsDownButton({
                    multiFieldsPanel: this
                });
                items.push(downButton);
            }
        }

        this.multiItems.each(function(i) {
            items.push(i);
        });

        var item = this.add({
            layout: 'form',
            border: false,
            items: items
        });
        if (deleteButton) deleteButton.groupItem = item;
        if (upButton) upButton.groupItem = item;
        if (downButton) downButton.groupItem = item;
        this.doLayout();

        item.cascade(function(i) {
            if (i.title && i.title.match(/\{(\w+)\}/)) {
                i.replaceTitle = i.title;
                if (RegExp.$1 != 0) i.replaceTitleField = RegExp.$1;
            }
        }, this);

        this.hiddenCountValue._findFormFields(item, function(i) {
            i.setDefaultValue();
            i.clearInvalid();
        });

        if (this.multiItems[this.multiItems.length-1].xtype == 'fieldset') {
            if (upButton && upButton.el) {
                upButton.el.applyStyles('clear: right; left: 0;');
            } else if (upButton) {
                upButton.style += ' clear: right; left: 0;';
            }
            if (downButton && downButton.el) {
                downButton.el.applyStyles('clear: right; left: 0;');
            } else if (downButton) {
                downButton.style += ' clear: right; left: 0;';
            }
        }

        //firefox schiebt den button ned nach unten
        if(this.addGroupButton) this.addGroupButton.hide();
        if(this.addGroupButton) this.addGroupButton.show.defer(100, this.addGroupButton);

        if (this.disabled) {
            item.disableRecursive();
        } else {
            item.enableRecursive();
        }

        this.groups.push({
            item: item,
            deleteButton: deleteButton,
            upButton: upButton,
            downButton: downButton
        });

        this.updateButtonsState();

        return item;
    },

    updateButtonsState: function(values) {
        if (this.addGroupButton) {
            if (this.maxEntries && this.groups.length >= this.maxEntries) {
                this.addGroupButton.disable();
            } else {
                this.addGroupButton.enable();
            }
            if (this.multiItems[this.multiItems.length-1].xtype == 'fieldset') {
                if (this.groups.length) {
                    this.addGroupButton.el.setStyle('top', '-19px');
                } else {
                    this.addGroupButton.el.setStyle('top', '0');
                }
            }
        }
        for (var i = 0; i < this.groups.length; i++) {
            var g = this.groups[i];
            if (g.upButton && i == 0) {
                g.upButton.disable();
            } else if (g.upButton) {
                g.upButton.enable();
            }
            if (g.downButton && i == this.groups.length-1) {
                g.downButton.disable();
            } else if (g.downButton) {
                g.downButton.enable();
            }
            if (g.deleteButton && this.minEntries >= this.groups.length) {
                g.deleteButton.disable();
            } else if (g.deleteButton) {
                g.deleteButton.enable();
            }
            g.item.cascade(function(item) {
                if (item.replaceTitle) {
                    var title = item.replaceTitle;
                    title = title.replace(/\{0\}/, i+1);
                    if (item.replaceTitleField) {
                        var exp = /\{\w+\}/;
                        if (values && values[i]) {
                            title = title.replace(exp, values[i][item.replaceTitleField]);
                        } else {
                            title = item.title;
                            if (exp.test(title)) title = trlVps('New Entry');
                        }
                    }
                    item.setTitle(title);
                }
            }, this);
        }
    }
});
Ext.reg('multifields', Vps.Form.MultiFields);

Vps.Form.MultiFieldsDeleteButton = Ext.extend(Ext.BoxComponent,  {
    // private
    onRender : function(ct, position){
        this.el = ct.createChild({
            tag: 'a',
            html: '<img src="/assets/silkicons/delete.png" />',
            href: '#',
            style: 'float: right; position: relative; z-index: 10; left: -20px; top: 1px;'
        }, position);
        this.el.on('click', function(e) {
            e.stopEvent();
            if (this.disabled) return;
            var p = this.multiFieldsPanel;
            for(var i=0; i < p.groups.length; i++) {
                var g = p.groups[i];
                if (g.item == this.groupItem) {
                    p.remove(g.item);
                    p.groups.splice(i, 1);
                    p.doLayout();
                    break;
                }
            }
            //workaround für Firefox problem wenn eintrag gelöscht wird verschwindet add-Button
            if(p.addGroupButton) p.addGroupButton.hide();
            if(p.addGroupButton) p.addGroupButton.show.defer(1, p.addGroupButton);
            if(p.addGroupButton) p.updateButtonsState();
        }, this);
    }
});
Vps.Form.MultiFieldsUpButton = Ext.extend(Ext.BoxComponent,  {
    // private
    onRender : function(ct, position){
        this.el = ct.createChild({
            tag: 'a',
            html: '<img src="/assets/silkicons/arrow_up.png" />',
            href: '#',
            style: 'float: right; position: relative; z-index: 10; left: -20px; top: 1px;'
        }, position);
        this.el.on('click', function(e) {
            e.stopEvent();
            if (this.disabled) return;
            var p = this.multiFieldsPanel;
            for(var i=0; i < p.groups.length; i++) {
                var g = p.groups[i];
                if (g.item == this.groupItem) {
                    g.item.getEl().insertBefore(p.groups[i-1].item.getEl());
                    p.groups.splice(i-1, 2, p.groups[i], p.groups[i-1]);
                    //wenn reihenfolge geaendert wurde muss feld dirty sein
                    //einfach die anzahl faken
                    p.hiddenCountValue.originalCount = -1;
                    break;
                }
            }
            p.updateButtonsState();
        }, this);
    }
});
Vps.Form.MultiFieldsDownButton = Ext.extend(Ext.BoxComponent,  {
    // private
    onRender : function(ct, position){
        this.el = ct.createChild({
            tag: 'a',
            html: '<img src="/assets/silkicons/arrow_down.png" />',
            href: '#',
            style: 'float: right; position: relative; z-index: 10; left: -20px; top: 1px;'
        }, position);
        this.el.on('click', function(e) {
            e.stopEvent();
            if (this.disabled) return;
            var p = this.multiFieldsPanel;
            for(var i=0; i < p.groups.length; i++) {
                var g = p.groups[i];
                if (g.item == this.groupItem) {
                    if (!p.groups[i+2] && p.addGroupButton) {
                        g.item.getEl().insertBefore(p.addGroupButton.getEl());
                    } else {
                        g.item.getEl().insertBefore(p.groups[i+2].item.getEl());
                    }
                    p.groups.splice(i, 2, p.groups[i+1], p.groups[i]);
                    //wenn reihenfolge geaendert wurde muss feld dirty sein
                    //einfach die anzahl faken
                    p.hiddenCountValue.originalCount = -1;
                    break;
                }
            }
            p.updateButtonsState();
        }, this);
    }
});
Vps.Form.MultiFieldsAddButton = Ext.extend(Ext.BoxComponent,  {
    // private
    onRender : function(ct, position){
        this.el = ct.createChild({
            tag: 'a',
            html: '<img src="/assets/silkicons/add.png" />',
            href: '#',
            style: 'float: right; position: relative; z-index: 10; left: -20px; top: 1px;'
        }, position);
        this.el.on('click', function(e) {
            e.stopEvent();
            if (this.disabled) return false;
            var item = this.multiFieldsPanel.addGroup();
            var breakIt = false;
            item.cascade(function(i) {
                if (!breakIt && i.isFormField && i.isVisible()) {
                    i.focus();
                    //return false funktioniert nicht, workaround:
                    breakIt = true;
                }
            }, this);
        }, this);
    }
});

Vps.Form.MultiFieldsHidden = Ext.extend(Ext.form.Hidden, {
    _initFields: function(cnt) {
        var gp = this.multiFieldsPanel;
        if (cnt < gp.minEntries) cnt = gp.minEntries;
        if (cnt > gp.maxEntries) cnt = gp.maxEntries;
        for (var i = gp.groups.length; i < cnt; i++) {
            gp.addGroup();
        }
        for (var i = gp.groups.length; i > cnt; i--) {
            var g = gp.groups[i-1];
            gp.remove(g.item);
            gp.remove(g.deleteButton);
            gp.remove(g.upButton);
            gp.remove(g.downButton);
            gp.groups.splice(i-1, 1);
        }
    },
    setValue : function(value) {
    	var gp = this.multiFieldsPanel;
        if (!value instanceof Array) throw new 'ohje, value ist kein array - wos mochma do?';
        this._initFields(value.length);
        for (var i = 0; i < gp.groups.length; i++) {
            if (value[i]) {
                gp.groups[i].id = value[i].id;
            } else {
                gp.groups[i].id = null;
            }
            this._findFormFields(gp.groups[i].item, function(item) {
                if (value[i]) {
                    for (var j in value[i]) {
                        if (item.name == j) {
                            item.setValue(value[i][j]);
                            return;
                        }
                    }
                }
            });
        }
        gp.updateButtonsState(value);

        this.value = value;
    },
    getValue : function() {
        var ret = [];
        var gp = this.multiFieldsPanel;
        for (var i = 0; i < gp.groups.length; i++) {
            var g = gp.groups[i];
            var row = {};
            row.id = g.id;
            this._findFormFields(g.item, function(item) {
                row[item.name] = item.getValue();
            });
            ret.push(row);
        }
        return ret;
    },
    _findFormFields: function(item, fn, scope) {
        if (item.isFormField) {
            fn.call(scope || this, item);
        }
        if (item.items) {
            item.items.each(function(i) {
                return this._findFormFields(i, fn, scope);
            }, this);
        }
    },
    validate : function() {
        var valid = true;
        var gp = this.multiFieldsPanel;
        gp.groups.each(function(g) {
            this._findFormFields(g.item, function(f) {
                if (!f.validate()) {
                    valid = false;
                }
            }, this);
        }, this);
        return valid;
    },
    resetDirty: function() {
        var gp = this.multiFieldsPanel;
        gp.groups.each(function(g) {
            this._findFormFields(g.item, function(f) {
                f.resetDirty();
            }, this);
        }, this);
        this.originalCount = gp.groups.length;
        this.originalValue = this.value;
    },
    clearValue: function() {
        Vps.Form.MultiFieldsHidden.superclass.resetDirty.call(this);
        var gp = this.multiFieldsPanel;
        this._initFields(gp.minEntries);
        gp.groups.each(function(g) {
            this._findFormFields(g.item, function(f) {
                f.clearValue();
            }, this);
        }, this);
        this.originalCount = gp.groups.length;
        this.originalValue = '';
    },
    setDefaultValue: function() {
        var gp = this.multiFieldsPanel;
        this._initFields(gp.minEntries);
        gp.groups.each(function(g) {
            this._findFormFields(g.item, function(f) {
                f.setDefaultValue();
            }, this);
        }, this);
        this.originalCount = gp.groups.length;
        this.originalValue = '';
    },
    isDirty : function() {
        var gp = this.multiFieldsPanel;

        //anz. einträge geändert (felder selbst müssen nicht dirty sein)
        if (this.originalCount != gp.groups.length) return true;

        var dirty = false;
        gp.groups.each(function(g) {
            this._findFormFields(g.item, function(f) {
                if (f.isDirty()) {
                    dirty = true;
                    return false; //each verlassen
                }
            }, this);
        }, this);
        return dirty;
    },

    // private
    initEvents : function(){
        this.originalValue = '';
    },

    clearInvalid: function() {
        var gp = this.multiFieldsPanel;
        gp.groups.each(function(g) {
            this._findFormFields(g.item, function(f) {
                f.clearInvalid();
            }, this);
        }, this);
    }
});
