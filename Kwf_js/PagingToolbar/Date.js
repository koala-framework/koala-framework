Ext2.namespace('Kwf.PagingToolbar');

Kwf.PagingToolbar.Date = Ext2.extend(Ext2.PagingToolbar, {
    initComponent: function() {
        Kwf.PagingToolbar.Date.superclass.initComponent.call(this);
        this.cursor = new Date();
    },
    // private
    onRender : function(ct, position){
        Ext2.PagingToolbar.superclass.onRender.call(this, ct, position);

        this.prev = this.addButton({
            tooltip: this.prevText,
            iconCls: "x2-tbar-page-prev",
            handler: this.onClick.createDelegate(this, ["prev"])
        });
        this.addSeparator();

        this.field = new Kwf.Form.DateField({
            width: 80
        });
        this.add(this.field);
        this.field.el.on("keydown", this.onPagingKeydown, this);
        this.field.on('menuhidden', function() {
            this.store.load({params:{start: this.field.getValue().format('Y-m-d'), limit: this.pageSize}});
        }, this);
        this.addSeparator();
        this.next = this.addButton({
            tooltip: this.nextText,
            iconCls: "x2-tbar-page-next",
            handler: this.onClick.createDelegate(this, ["next"])
        });

        this.addSeparator();
        this.loading = this.addButton({
            tooltip: this.refreshText,
            iconCls: "x2-tbar-loading",
            handler: this.onClick.createDelegate(this, ["refresh"])
        });

        if(this.displayInfo){
            this.displayEl = Ext2.fly(this.el.dom).createChild({cls:'x2-paging-info'});
        }

        if(this.dsLoaded){
            this.onLoad.apply(this, this.dsLoaded);
        }
    },

    // private
    onLoad : function(ds, r, o){
        if(!this.rendered){
            this.dsLoaded = [store, r, o];
            return;
        }
        this.cursor = o.params ? Date.parseDate(o.params.start, 'Y-m-d') : new Date();
        this.field.setValue(this.cursor);
        this.loading.enable();
        this.updateInfo();
    },

    // private
    onPagingKeydown : function(e){
        var k = e.getKey();
        if(k == e.RETURN){
            var v = this.field.getValue();
            if(!v || !v.getDate()){
                this.field.setValue(this.cursor);
                return;
            }
            this.store.load({params:{start: this.field.getValue().format('Y-m-d'), limit: this.pageSize}});
            e.stopEvent();
        } else if(k == e.UP || k == e.RIGHT || k == e.PAGEUP || k == e.DOWN || k == e.LEFT || k == e.PAGEDOWN) {
            var increment = (e.shiftKey) ? 10 : 1;
            if(k == e.DOWN || k == e.LEFT || k == e.PAGEDOWN) increment *= -1;
            var v = this.field.getValue();
            if(!v || !v.getDate()){
                this.field.setValue(this.cursor);
                return;
            }
            this.store.load({params:{start: this.cursor.add(Date.DAY, increment*this.pageSize).format('Y-m-d'), limit: this.pageSize}});
            e.stopEvent();
        }
    },

    // private
    onClick : function(which){
        var ds = this.store;
        switch(which){
            case "prev":
                ds.load({params:{start: this.cursor.add(Date.DAY, -this.pageSize).format('Y-m-d'), limit: this.pageSize}});
            break;
            case "next":
                ds.load({params:{start: this.cursor.add(Date.DAY, this.pageSize).format('Y-m-d'), limit: this.pageSize}});
            break;
            case "refresh":
                ds.load({params:{start: this.cursor.format('Y-m-d'), limit: this.pageSize}});
            break;
        }
    }
});
