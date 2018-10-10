Ext2.ns('Kwc.Shop.Cart.Checkout');
Kwc.Shop.Cart.Checkout.OrdersPanel = Ext2.extend(Ext2.Panel, {
    layout: 'border',

    createItems: function()
    {
        this.order = new Kwf.Auto.FormPanel({
            region: 'center',
            controllerUrl:  this.orderControllerUrl,
            baseParams: Kwf.clone(this.baseParams)
        });
        this.orderProducts = new Kwf.Auto.GridPanel({
            xtype: 'kwf.autogrid',
            region: 'south',
            height: 180,
            split: true,
            controllerUrl: this.orderProductsControllerUrl,
            baseParams: Kwf.clone(this.baseParams)
        });
        this.orders = new Kwf.Auto.GridPanel({
            xtype: 'kwf.autogrid',
            region: 'center',
            controllerUrl: this.ordersControllerUrl,
            baseParams: Kwf.clone(this.baseParams),
            bindings: [ this.order, { item: this.orderProducts, queryParam: 'shop_order_id' } ],
            columnsConfig: {
                invoice: {
                    clickHandler: function(grid, index, button, event) {
                        var row = grid.getStore().getAt(index);
                        window.open(this.ordersControllerUrl+'/pdf?'+Ext2.urlEncode(this.baseParams)+'&id='+row.id);
                        (function() {
                            this.order.reload(); //invoice_date wurde wom�glich gesetzt
                        }).defer(500, this);
                    },
                    renderer: function(value, p, record, rowIndex, colIndex, store, column) {
                        p.css += 'kwf-cell-button';
                        var icon = '/assets/silkicons/page_white.png';
                        if (record.get('invoice_number')) {
                            icon = '/assets/silkicons/page_white_star.png';
                        }
                        p.attr += 'style="background-image:url('+icon+');" ';
                    },
                    scope: this
                },
                shipped: {
                    renderer: function(value, p, record, rowIndex, colIndex, store, column) {
                        if (!record.get('shipped')) {
                            return Ext2.util.Format.cellButton.apply(this, arguments);
                        }
                    },
                    clickHandler: function(grid, index, button, event) {
                        var row = grid.getStore().getAt(index);
                        if (row.get('shipped')) return;
                        var p = Kwf.clone(this.baseParams);
                        p.id = row.id;
                        Ext2.Ajax.request({
                            url: this.ordersControllerUrl+'/json-shipped',
                            params: p,
                            scope: this,
                            mask: this.getEl(),
                            success: function() {
                                this.orders.reload();
                                this.order.reload();
                            }
                        });
                    },
                    scope: this
                }
            }
        });
        this.items = [this.orders, {
            layout: 'border',
            region: 'east',
            width: 500,
            split: true,
            items: [this.order, this.orderProducts]
        }];
    },
    initComponent: function() {
        this.createItems();
        Ext2.TaskMgr.start({
            run: function() {
                this.orders.reload();
            },
            scope: this,
            interval: 1000*60*5
        });

        Kwc.Shop.Cart.Checkout.OrdersPanel.superclass.initComponent.call(this);
    }
});
Ext2.reg('kwc.shop.cart.checkout.orders', Kwc.Shop.Cart.Checkout.OrdersPanel);

