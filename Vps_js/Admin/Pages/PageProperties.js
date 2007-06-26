Vps.Admin.Pages.PageProperties = function(renderTo, config)
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
        handler : save,
        scope   : this
    });
    this.toolbar.addButton({
        id: 'delete',
        text    : 'Löschen',
        handler : remove,
        disabled: true,
        scope   : this
    });
    this.toolbar.addButton({
        id: 'add',
        text    : 'Als neue Seite speichern',
        handler : add,
        disabled: true,
        scope   : this
    });
    this.toolbar.addButton({
        id: 'edit',
        text    : 'Seite bearbeiten',
        handler : edit,
        disabled: true,
        scope   : this
    });
    
    this.form.render(renderTo);
}

Ext.extend(Vps.Admin.Pages.PageProperties, Ext.util.Observable,
{
    remove: function(o, e) {
        form.baseParams.command = 'delete';
        form.submit({
            success: function(form, a) {
                nextNode = form.node.nextSibling;
                form.tree.getSelectionModel().selNode.parentNode.removeChild(form.node);
                form.tree.getSelectionModel().select(nextNode);
            },
            invalid: ajaxInvalid,
            failure: ajaxFailure
        })
    },
        
    save: function(o, e) {
        form.baseParams.command = 'save';
        form.submit({
            success: function(form, a) {
                form.node.setText(a.result.name);
                form.node.ui.removeClass('offline');
                if(a.result.status == '0'){
                    form.node.ui.addClass('offline');
                }
            },
            invalid: ajaxInvalid,
            failure: ajaxFailure
        })
    },
        
    edit: function(o, e) {
        document.location.href = '/admin/page?id=' + form.node.id;
    },
        
    add: function(o, e) {
        form.baseParams.command = 'add';
        parentNode = form.tree.getSelectionModel().getSelectedNode();
        if (parentNode == null) {
            parentNode = form.tree.getRootNode();
        }
        form.parentNode = parentNode;
        form.baseParams.id = 0;
        form.baseParams.parentId = parentNode.id;
        form.submit({
            success: function(form, a) {
                node = form.tree.getLoader().createNode(a.result.data);
                form.parentNode.appendChild(node);
                form.parentNode.expand();
            },
            invalid: ajaxInvalid,
            failure: ajaxFailure
        })
    },
        
    loadPage: function (node) {
        if (isNaN(node.id)) {
            toolbar.items.get('add').enable(); 
            toolbar.items.get('delete').disable(); 
            toolbar.items.get('save').disable(); 
            toolbar.items.get('edit').disable(); 
        } else {
            toolbar.items.each(function(b) { b.enable(); });
        }
        form.baseParams.id = node.id;
        form.node = node;
        form.load({url:'/admin/pages/ajaxLoadPageData'});
    }

})
