Ext.namespace('Vps.PagingToolbar');
Vps.PagingToolbar.Date = function(el, ds, config){
    this.cursor = new Date();
    Vps.PagingToolbar.Date.superclass.constructor.call(this, el, ds, config);
};

Ext.extend(Vps.PagingToolbar.Date, Ext.PagingToolbar, {
    // private
    renderButtons : function(el){
        Ext.PagingToolbar.superclass.render.call(this, el);

        this.prev = this.addButton({
            tooltip: this.prevText,
            cls: "x-btn-icon x-grid-page-prev",
            handler: this.onClick.createDelegate(this, ["prev"])
        });
        this.addSeparator();

        this.field = new Vps.Form.DateField({
            width: 80,
            msgTarget: 'qtip'
        });
        this.add(this.field);
        this.field.el.on("keydown", this.onPagingKeydown, this);
        this.field.on('menuhidden', function() {
            this.ds.load({params:{start: this.field.getValue().format('Y-m-d'), limit: this.pageSize}});
        }, this);
        this.addSeparator();
        this.next = this.addButton({
            tooltip: this.nextText,
            cls: "x-btn-icon x-grid-page-next",
            handler: this.onClick.createDelegate(this, ["next"])
        });

        this.addSeparator();
        this.loading = this.addButton({
            tooltip: this.refreshText,
            cls: "x-btn-icon x-grid-loading",
            disabled: true,
            handler: this.onClick.createDelegate(this, ["refresh"])
        });
    },

    // private
    onLoad : function(ds, r, o){
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
            this.ds.load({params:{start: this.field.getValue().format('Y-m-d'), limit: this.pageSize}});
            e.stopEvent();
        } else if(k == e.UP || k == e.RIGHT || k == e.PAGEUP || k == e.DOWN || k == e.LEFT || k == e.PAGEDOWN) {
            var increment = (e.shiftKey) ? 10 : 1;
            if(k == e.DOWN || k == e.LEFT || k == e.PAGEDOWN) increment *= -1;
            var v = this.field.getValue();
            if(!v || !v.getDate()){
                this.field.setValue(this.cursor);
                return;
            }
            this.ds.load({params:{start: this.cursor.add(Date.DAY, increment*this.pageSize).format('Y-m-d'), limit: this.pageSize}});
            e.stopEvent();
        }
    },

    // private
    onClick : function(which){
        var ds = this.ds;
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
