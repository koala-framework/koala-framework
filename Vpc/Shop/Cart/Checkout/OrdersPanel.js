Ext.ns('Vpc.Shop.Cart.Checkout');
Vpc.Shop.Cart.Checkout.OrdersPanel = Ext.extend(Ext.Panel, {
    layout: 'border',
    initComponent: function()
    {
        var order = new Vps.Auto.FormPanel({
            region: 'north',
            height: 400,
            controllerUrl:  this.orderControllerUrl,
            baseParams: Vps.clone(this.baseParams)
        });
        var orderProducts = new Vps.Auto.GridPanel({
            xtype: 'vps.autogrid',
            region: 'center',
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
                            order.reload(); //invoice_date wurde womöglich gesetzt
                        }).defer(500);
                    },
                    scope: this
                },
                shipped: {
                    clickHandler: function(grid, index, button, event) {
                        var row = grid.getStore().getAt(index);
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
            items: [order, orderProducts]
        }];

        Vpc.Shop.Cart.Checkout.OrdersPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.shop.cart.checkout.orders', Vpc.Shop.Cart.Checkout.OrdersPanel);

