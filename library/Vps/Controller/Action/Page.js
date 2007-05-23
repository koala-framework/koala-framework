Page = function(config) {
    
    // Pagetree
    var PageTree = function(el) {
        tree = new Ext.tree.TreePanel(el, {
            animate:true, 
            loader: new Ext.tree.TreeLoader({dataUrl:'/admin/page/ajaxGetNodes', baseParams: { pageId:config.pageId }}),
            containerScroll: true,
            rootVisible: false
        });
    
        // set the root node
        var root = new Ext.tree.AsyncTreeNode({
            text: '',
            draggable:false,
            id: 'root'
        });
        tree.setRootNode(root);
    
        tree.getSelectionModel().on('selectionchange',
            function (e, node) {
                toolbar.items.each(function(b) { b.enable(); });
                Ext.get('main').dom.src = '/admin/component?id=' + node.id;
                form.selectDecorators(node.attributes.decorators);
            }
        );
        
        // render the tree
        //tree.render();
        root.expand();
        return tree;
    }
    
    // Toolbar
    var Toolbar = function(el, pageId) {
        var toolbar = new Ext.Toolbar(el);
        toolbar.addButton({
            id: 'add',
            disabled: true,
            text    : 'Absatz einfügen',
            handler : function(o, p) {
                new Ext.data.Connection().request({
                    url: '/admin/page/ajaxAddParagraph',
                    method: 'post',
                    scope: this,
                    params: {pageId: pageId},
                    callback: function(options, bSuccess, response) {
                        var o = Ext.decode(response.responseText);
                        if('object' != typeof o) {
                            throw 'Invalid server response';
                        }
                        if(true === o.success) {
                            tree.getRootNode().reload()
                        } else {
                        }
                    }
                }); 
            },
            scope   : this
        });
        toolbar.addButton({
            id: 'delete',
            disabled: true,
            text    : 'Absatz löschen',
            handler : function() {},
            scope   : this
        });
        
        return toolbar;
    }
    
    // Form
    var Form = function(el, decorators) {
        
        this.selectDecorators = function(decorators) {
            form.reset();
            for (var i in decorators) {
                var d = form.findField(decorators[i]);
                if (d) { d.setValue(true); }
            }
        }
        
        var form = new Ext.form.Form({
            labelAlign: 'right',
            labelWidth: 75
        });
        
        // Decorators
        form.fieldset({legend:'Decorators', hideLabels:true});
        for (var dName in decorators) {
            form.add(new Ext.form.Checkbox({
                boxLabel: dName,
                name: decorators[dName]
            }));
        }
        form.end();
    
        form.addButton('Save');
        form.addButton('Cancel');
    
        form.render(el);
    }

    // Konstruktor
    west = Ext.DomHelper.append(document.body, '<div />', true);
    west1 = Ext.DomHelper.append(west, '<div />', true);
    west2 = Ext.DomHelper.append(west, '<div />', true);
    center = Ext.DomHelper.append(document.body, '<iframe id="main" frameborder="no" />', true);

    var layout = new Ext.BorderLayout(document.body, {
        west: {
            split:true,
            initialSize: 300,
            minSize: 200,
            titlebar: true,
            useShim: true,
            collapsible: true,
            maxSize: 600
        },
        center: {
            autoScroll: false,
            titlebar: true
        }
    });

    var innerLayout = new Ext.BorderLayout(west, {
        south: {
            split:true,
            initialSize: 200,
            minSize: 100,
            maxSize: 400,
            autoScroll:true,
            titlebar: true
        },
        center: {
            autoScroll:true
        }
    });

    layout.beginUpdate();
    
    innerLayout.add('center', new Ext.ContentPanel(west1));
    innerLayout.add('south', new Ext.ContentPanel(west2, {title: 'Eigenschaften des gewählten Seitenbausteins', fitToFrame:true}));
    
    layout.add('west', new Ext.NestedLayoutPanel(innerLayout, {title: 'Aufbau der aktuellen Seite', fitToFrame:true}));
    layout.add('center', new Ext.ContentPanel(center, {title: 'Inhalt des gewählten Seitenbausteins', fitToFrame:true}));
    layout.restoreState();
    layout.endUpdate();
    
    var tree = new PageTree(west1);
    var toolbar = new Toolbar(west1, config.pageId);
    var form = new Form(west2, config.decorators);
    
    tree.render();
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

