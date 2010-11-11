Ext.ns('Vpc.Shop.Cart.Checkout');
Vpc.Shop.Cart.Checkout.OrdersPanel = Ext.extend(Ext.Panel, {
    layout: 'border',
    initComponent: function()
    {
        var order = new Vps.Auto.FormPanel({
            region: 'center',
            controllerUrl:  this.orderControllerUrl,
            baseParams: Vps.clone(this.baseParams)
        });
        var orderProducts = new Vps.Auto.GridPanel({
            xtype: 'vps.autogrid',
            region: 'south',
            height: 150,
            split: true,
            controllerUrl: this.orderProductsControllerUrl,
            baseParams: Vps.clone(this.baseParams)
        });
        var orders = new Vps.Auto.GridPanel({
            xtype: 'vps.autogrid',
            region: 'center',
            controllerUrl: this.ordersControllerUrl,
            baseParams: Vps.clone(this.baseParams),
            bindings: [ order, { item: orderProducts, queryParam: 'shop_order_id' } ],
            columnsConfig: {
                invoice: {
                    clickHandler: function(grid, index, button, event) {
                        var row = grid.getStore().getAt(index);
                        window.open(this.ordersControllerUrl+'/pdf?'+Ext.urlEncode(this.baseParams)+'&id='+row.id);
                        (function() {
                            order.reload(); //invoice_date wurde wom�glich gesetzt
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
                                orders.reload();
                                order.reload();
                            }
                        });
                    },
                    scope: this
                }
            }
        });
        this.items = [orders, {
            layout: 'border',
            region: 'east',
            width: 500,
            split: true,
            items: [order, orderProducts]
        }];

        Ext.TaskMgr.start({
            run: function() {
                orders.reload();
            },
            interval: 1000*60*5
        });

        Vpc.Shop.Cart.Checkout.OrdersPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.shop.cart.checkout.orders', Vpc.Shop.Cart.Checkout.OrdersPanel);

