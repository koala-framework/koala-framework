Ext.ns('Vpc.Shop.Cart.Checkout');
Vpc.Shop.Cart.Checkout.OrdersPanel = Ext.extend(Ext.Panel, {
    layout: 'border',

    createItems: function()
    {
        this.order = new Vps.Auto.FormPanel({
            region: 'center',
            controllerUrl:  this.orderControllerUrl,
            baseParams: Vps.clone(this.baseParams)
        });
        this.orderProducts = new Vps.Auto.GridPanel({
            xtype: 'vps.autogrid',
            region: 'south',
            height: 180,
            split: true,
            controllerUrl: this.orderProductsControllerUrl,
            baseParams: Vps.clone(this.baseParams)
        });
        this.orders = new Vps.Auto.GridPanel({
            xtype: 'vps.autogrid',
            region: 'center',
            controllerUrl: this.ordersControllerUrl,
            baseParams: Vps.clone(this.baseParams),
            bindings: [ this.order, { item: this.orderProducts, queryParam: 'shop_order_id' } ],
            columnsConfig: {
                invoice: {
                    clickHandler: function(grid, index, button, event) {
                        var row = grid.getStore().getAt(index);
                        window.open(this.ordersControllerUrl+'/pdf?'+Ext.urlEncode(this.baseParams)+'&id='+row.id);
                        (function() {
                            this.order.reload(); //invoice_date wurde wom�glich gesetzt
                        }).defer(500);
                    },
                    renderer: function(value, p, record, rowIndex, colIndex, store, column) {
                        p.css += 'vps-cell-button';
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
                            return Ext.util.Format.cellButton.apply(this, arguments);
                        }
                    },
                    clickHandler: function(grid, index, button, event) {
                        var row = grid.getStore().getAt(index);
                        if (row.get('shipped')) return;
                        var p = Vps.clone(this.baseParams);
                        p.id = row.id;
                        Ext.Ajax.request({
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
        Ext.TaskMgr.start({
            run: function() {
                this.orders.reload();
            },
            scope: this,
            interval: 1000*60*5
        });

        Vpc.Shop.Cart.Checkout.OrdersPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.shop.cart.checkout.orders', Vpc.Shop.Cart.Checkout.OrdersPanel);

