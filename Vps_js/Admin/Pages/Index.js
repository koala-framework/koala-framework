Vps.Admin.Pages.Index = function() {
    content = Ext.DomHelper.append(document.body, '<div />', true);
    west = Ext.DomHelper.append(content, '<div />', true);
    center = Ext.DomHelper.append(content, '<div />', true);
    
    var layout = new Ext.BorderLayout(content, {
        west: {
            split:true,
            initialSize: 400,
            minSize: 200,
            maxSize: 600
        },
        center: {
            autoScroll: false
        }
    });
    layout.beginUpdate();
    layout.add('west', new Ext.ContentPanel(west, 'West'));
    layout.add('center', new Ext.ContentPanel(center, 'Center'));
    layout.endUpdate();
    
    var form = new PageForm(center);

    var treeTabs = new TreeTabs(west, form);
}

TreeTabs = function(el, form) {
    var jtabs = new Ext.TabPanel(el);
    
    var tabs = new Array();
    
    tabs[1] = jtabs.addTab('tab1', 'Seitenbaum', '<div id="tree1" />');
    tabs[1].on('activate', function() { PageTree('tree1', form); });

    jtabs.activate('tab1');
}

PageTree = function(el, form) {
    var Tree = Ext.tree;

    var tree = new Tree.TreePanel(
        el,
        {
            animate:true, 
            loader: new Tree.TreeLoader({dataUrl:'/admin/pages/ajaxGetNodes'}),
            enableDD:true,
            containerScroll: true,
            rootVisible: false
        }
    );

    // set the root node
    var root = new Tree.AsyncTreeNode({
        text: '',
        draggable:false,
        id:'root'
   });
    tree.setRootNode(root);

    tree.on(
        'collapse',
        function(e){
            var conn = new Ext.data.Connection();
            conn.request({
                url: '/admin/pages/ajaxCollapseNode',
                params: {id: e.id},
                method: 'post'
            });
        }
    );
    
    tree.on(
        'nodedrop',
        function(e){
            var callback = function(option, success, response)
            {
                eval('result = ' + response.responseText);
                if (!result.success) {
                    // Falls Fehler aufgetreten ist, einfach neu laden
                    document.location.href = '/admin/pages';
                }
            }
            var params = {
                command: 'move',
                source: e.dropNode.id,
                target: e.target.id,
                point: e.point
            }
            var config = {
                url: '/admin/pages/ajaxProcessPageData',
                params: params,
                method: 'post',
                callback: callback,
                scope: this
            }

            var conn = new Ext.data.Connection();
            conn.request(config);
        }
    );
    
    tree.getSelectionModel().on(
        'selectionchange',
        function (e, node) {
            if (node) { form.loadPage(node); }
        }
    );
    
    // render the tree
    tree.render();
    form.setTree(tree);
    root.expand();

}

MyNodeUI = function(node){
    MyNodeUI.superclass.constructor.call(this, node);
}
Ext.extend(MyNodeUI, Ext.tree.TreeNodeUI, {
    initEvents : function(){
        MyNodeUI.superclass.initEvents.call(this);
        if(this.node.attributes.status == '0'){
            this.addClass('offline');
        }
    }
});

PageForm = function(el) {
    
    var tree;
    
    init = function(el) {
        form = new Ext.form.Form({
            labelWidth: 100,
            url: '/admin/pages/ajaxProcessPageData',
            baseParams: {id: 0}
        });
    
        form.fieldset(
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
        
        toolbar = new Ext.Toolbar(Ext.get(el).createChild());
        toolbar.addButton({
            id: 'save',
            disabled: true,
            text    : 'Speichern',
            handler : save,
            scope   : this
        });
        toolbar.addButton({
            id: 'delete',
            text    : 'Löschen',
            handler : remove,
            disabled: true,
            scope   : this
        });
        toolbar.addButton({
            id: 'add',
            text    : 'Als neue Seite speichern',
            handler : add,
            disabled: true,
            scope   : this
        });
        toolbar.addButton({
            id: 'edit',
            text    : 'Seite bearbeiten',
            handler : edit,
            disabled: true,
            scope   : this
        });
        form.render(el);
    }

    remove = function(o, e) {
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
    }
        
    save = function(o, e) {
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
    }
        
    edit = function(o, e) {
        document.location.href = '/admin/page?id=' + form.node.id;
    }
        
    add = function(o, e) {
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
    }
        
    this.loadPage = function (node) {
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

    this.setTree = function (tree) {
        form.tree = tree;
    }

    init(el);
};

ajaxInvalid = function(form, type) {
    alert('invalid');
}
ajaxFailure = function(form, type) {
    alert('failure');
}
