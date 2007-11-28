Vps.Form.MultiFields = Ext.extend(Ext.Panel,
    minEntries: 
    position: tru
    initComponent : function()
        Vps.Form.MultiFields.superclass.initComponent.call(this

        this.hiddenCountValue = new Vps.Form.MultiFieldsHidden
            name: this.nam
            multiFieldsPanel: th
        }
        this.add(this.hiddenCountValue

        this.groups = [
    

    // priva
    onRender : function(ct, position
        Vps.Form.MultiFields.superclass.onRender.call(this, ct, position

        for (var i = 0; i < this.minEntries; i++)
            this.addGroup(
       

        this.addGroupButton = new Vps.Form.MultiFieldsAddButton
            multiFieldsPanel: thi
            renderTo: this.bo
        }, position
        if (this.multiItems[this.multiItems.length-1].xtype == 'fieldset')
            this.addGroupButton.el.setStyle('top', '-19px'
       
    

    // priva
    addGroup : function
   
        var deleteButton = new Vps.Form.MultiFieldsDeleteButton
            multiFieldsPanel: th
        }
        var items = [deleteButton
        if (this.position)
            var upButton = new Vps.Form.MultiFieldsUpButton
                multiFieldsPanel: th
            }
            items.push(upButton
            var downButton = new Vps.Form.MultiFieldsDownButton
                multiFieldsPanel: th
            }
            items.push(downButton
       

        this.multiItems.each(function(i)
            items.push(i
        }

        var item = this.add
            layout: 'form
            border: fals
            items: ite
        }
        deleteButton.groupItem = ite
        if (upButton) upButton.groupItem = ite
        if (downButton) downButton.groupItem = ite
        this.doLayout(

        item.cascade(function(i)
            if (i.title && i.title.match(/\{0\}/))
                i.replaceTitle = i.titl
           
        }, this


        if (this.multiItems[this.multiItems.length-1].xtype == 'fieldset')
            if (upButton && upButton.el)
                upButton.el.applyStyles('clear: right; left: 0;'
            } else if (upButton)
                upButton.style += ' clear: right; left: 0;
           
            if (downButton && downButton.el)
                downButton.el.applyStyles('clear: right; left: 0;'
            } else if (downButton)
                downButton.style += ' clear: right; left: 0;
           
       

        this.groups.push
            item: ite
            deleteButton: deleteButto
            upButton: upButto
            downButton: downButt
        }

        this.updateButtonsState(
    

    updateButtonsState: function()
        if (this.addGroupButton)
            if (this.maxEntries && this.groups.length >= this.maxEntries)
                this.addGroupButton.disable(
            } else
                this.addGroupButton.enable(
           
       
        for (var i = 0; i < this.groups.length; i++)
            var g = this.groups[i
            if (g.upButton && i == 0)
                g.upButton.disable(
            } else if (g.upButton)
                g.upButton.enable(
           
            if (g.downButton && i == this.groups.length-1)
                g.downButton.disable(
            } else if (g.downButton)
                g.downButton.enable(
           
            if (this.minEntries >= this.groups.length)
                g.deleteButton.disable(
            } else
                g.deleteButton.enable(
           
            g.item.cascade(function(item)
                if (item.replaceTitle)
                    item.setTitle(item.replaceTitle.replace(/\{0\}/, i+1)
               
            }, this
       
   
}
Ext.reg('multifields', Vps.Form.MultiFields

Vps.Form.MultiFieldsDeleteButton = Ext.extend(Ext.BoxComponent, 
    // priva
    onRender : function(ct, position
        this.el = ct.createChild
            tag: 'a
            html: '<img src="/assets/silkicons/delete.png" />
            href: '#
            style: 'float: right; position: relative; z-index: 10; left: -20px; top: 1px
        }, position
        this.el.on('click', function(e)
            e.stopEvent(
            if (this.disabled) retur
            var p = this.multiFieldsPane
            for(var i=0; i < p.groups.length; i++)
                var g = p.groups[i
                if (g.item == this.groupItem)
                    p.remove(g.item
                    p.groups.splice(i, 1
                    p.doLayout(
                    brea
               
           
            p.updateButtonsState(
        }, this
   
}
Vps.Form.MultiFieldsUpButton = Ext.extend(Ext.BoxComponent, 
    // priva
    onRender : function(ct, position
        this.el = ct.createChild
            tag: 'a
            html: '<img src="/assets/silkicons/arrow_up.png" />
            href: '#
            style: 'float: right; position: relative; z-index: 10; left: -20px; top: 1px
        }, position
        this.el.on('click', function(e)
            e.stopEvent(
            if (this.disabled) retur
            var p = this.multiFieldsPane
            for(var i=0; i < p.groups.length; i++)
                var g = p.groups[i
                if (g.item == this.groupItem)
                    g.item.getEl().insertBefore(p.groups[i-1].item.getEl()
                    p.groups.splice(i-1, 2, p.groups[i], p.groups[i-1]
                    brea
               
           
            p.updateButtonsState(
        }, this
   
}
Vps.Form.MultiFieldsDownButton = Ext.extend(Ext.BoxComponent, 
    // priva
    onRender : function(ct, position
        this.el = ct.createChild
            tag: 'a
            html: '<img src="/assets/silkicons/arrow_down.png" />
            href: '#
            style: 'float: right; position: relative; z-index: 10; left: -20px; top: 1px
        }, position
        this.el.on('click', function(e)
            e.stopEvent(
            if (this.disabled) retur
            var p = this.multiFieldsPane
            for(var i=0; i < p.groups.length; i++)
                var g = p.groups[i
                if (g.item == this.groupItem)
                    if (!p.groups[i+2])
                        g.item.getEl().insertBefore(p.addGroupButton.getEl()
                    } else
                        g.item.getEl().insertBefore(p.groups[i+2].item.getEl()
                   
                    p.groups.splice(i, 2, p.groups[i+1], p.groups[i]
                    brea
               
           
            p.updateButtonsState(
        }, this
   
}
Vps.Form.MultiFieldsAddButton = Ext.extend(Ext.BoxComponent, 
    // priva
    onRender : function(ct, position
        this.el = ct.createChild
            tag: 'a
            html: '<img src="/assets/silkicons/add.png" />
            href: '#
            style: 'float: right; position: relative; z-index: 10; left: -20px; top: 1px
        }, position
        this.el.on('click', function(e)
            e.stopEvent(
            if (this.disabled) return fals
            this.multiFieldsPanel.addGroup(
        }, this
   
}

Vps.Form.MultiFieldsHidden = Ext.extend(Ext.form.Hidden,
    setValue : function(value)
        var gp = this.multiFieldsPane
        if (!value instanceof Array) throw new 'ohje, value ist kein array - wos mochma do?
        var cnt = value.lengt
        for (var i = gp.groups.length; i < cnt; i++)
            gp.addGroup(
       
        if (cnt < gp.minEntries) cnt = gp.minEntrie
        if (cnt > gp.maxEntries) cnt = gp.maxEntrie
        for (var i = gp.groups.length; i > cnt; i--)
            var g = gp.groups[i-1
            gp.remove(g.item
            gp.remove(g.deleteButton
            gp.remove(g.upButton
            gp.remove(g.downButton
            gp.groups.splice(i-1
       
        for (var i = 0; i < gp.groups.length; i++)
            this._findFormFields(gp.groups[i].item, function(item)
                if (value[i])
                    for (var j in value[i])
                        if (item.name == j)
                            item.setValue(value[i][j]
                       
                   
                } else
                    item.setValue(item.defaultValue || ''
                    item.originalValue = item.getValue(
               
            }
       
        gp.updateButtonsState(
    
    getValue : function()
        var ret = [
        var gp = this.multiFieldsPane
        for (var i = 0; i < gp.groups.length; i++)
            var g = gp.groups[i
            var row = {
            this._findFormFields(g.item, function(item) 
                row[item.name] = item.getValue(
            }
            ret.push(row
       
        return re
    
    _findFormFields: function(item, fn, scope)
        if (item.isFormField)
            fn.call(scope || this, item
       
        if (item.items)
            item.items.each(function(i)
                return this._findFormFields(i, fn, scope
            }, this
       
    
    validate : function()
        var valid = tru
        var gp = this.multiFieldsPane
        gp.groups.each(function(g)
            this._findFormFields(g.item, function(f)
                if (!f.validate())
                    valid = fals
               
            }, this
        }, this
        return vali
    
    isDirty : function()
        var dirty = fals
        var gp = this.multiFieldsPane
        gp.groups.each(function(g)
            this._findFormFields(g.item, function(f)
                if (f.isDirty())
                    dirty = tru
                    return fals
               
            }, this
        }, this
        return dirt
   
}
