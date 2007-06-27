Vps.Admin.Pages.Properties = function(renderTo, config)
{    
    Ext.apply(this, config);
    this.events = {
    };
    
    this.form = new Ext.form.Form({
        labelWidth: 100,
        url: '/admin/pages/ajaxProcessPageData',
        baseParams: {id: 0}
    });

    this.form.fieldset(
        {legend: 'Seiteneigenschaften'},
        new Ext.form.TextField({
            allowBlank: false,
            blankText: 'Seitenname wird benötigt',
            fieldLabel: 'Seitenname',
            name: 'name',
            minLength: 1,
            maxLength: 255,
            width: 200
        }),
        new Ext.form.Checkbox({
            fieldLabel: 'Status',
            name: 'status'
        })
    );
    
    this.toolbar = new Ext.Toolbar(Ext.get(renderTo).createChild());
    this.toolbar.addButton({
        id: 'save',
        disabled: true,
        text    : 'Speichern',
        handler : this.save,
        scope   : this
    });
    this.toolbar.addButton({
        id: 'delete',
        text    : 'Löschen',
        handler : this.remove,
        disabled: true,
        scope   : this
    });
    this.toolbar.addButton({
        id: 'add',
        text    : 'Als neue Seite speichern',
        handler : this.add,
        disabled: true,
        scope   : this
    });
    
    this.form.render(renderTo);
}

Ext.extend(Vps.Admin.Pages.Properties, Ext.util.Observable,
{
    remove: function(o, e) {
        this.form.baseParams.command = 'delete';
        this.form.submit({
            success: function(form, a) {
                nextNode = form.node.nextSibling;
                form.tree.getSelectionModel().selNode.parentNode.removeChild(form.node);
                form.tree.getSelectionModel().select(nextNode);
            }
        })
    },
        
    save: function(o, e) {
        this.form.baseParams.command = 'save';
        this.form.submit({
            success: function(form, a) {
                form.node.setText(a.result.name);
                form.node.ui.removeClass('offline');
                if(a.result.status == '0'){
                    form.node.ui.addClass('offline');
                }
            }
        })
    },
        
    add: function(o, e) {
        this.form.baseParams.command = 'add';
        parentNode = this.form.tree.getSelectionModel().getSelectedNode();
        if (parentNode == null) {
            parentNode = this.form.tree.getRootNode();
        }
        this.form.parentNode = parentNode;
        this.form.baseParams.id = 0;
        this.form.baseParams.parentId = parentNode.id;
        this.form.submit({
            success: function(form, a) {
                node = form.tree.getLoader().createNode(a.result.data);
                form.parentNode.appendChild(node);
                form.parentNode.expand();
            }
        })
    },
        
    load: function (node) {
        if (isNaN(node.id)) {
            this.toolbar.items.get('add').enable(); 
            this.toolbar.items.get('delete').disable(); 
            this.toolbar.items.get('save').disable(); 
            this.toolbar.items.get('edit').disable(); 
        } else {
            this.toolbar.items.each(function(b) { b.enable(); });
        }
        this.form.baseParams.id = node.id;
        this.form.node = node;
        this.form.load({url:'/admin/pages/ajaxLoadPageData'});
    }

})
