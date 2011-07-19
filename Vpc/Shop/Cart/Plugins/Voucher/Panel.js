Ext.namespace('Vpc.Shop.Cart.Plugins.Voucher');
Vpc.Shop.Cart.Plugins.Voucher.Panel = Ext.extend(Ext.Panel, {
    layout: 'border',
    initComponent: function() {
        var history = new Vps.Auto.GridPanel({
            controllerUrl: this.voucherHistoryControllerUrl,
            region: 'center'
        });
        var vouchers = new Vps.Auto.GridPanel({
            controllerUrl: this.vouchersControllerUrl,
            region: 'west',
            width: 400,
            editDialog: new Vps.Auto.Form.Window({
                controllerUrl: this.voucherControllerUrl
            }),
            bindings: [{
                item: history,
                queryParam: 'voucher_id'
            }]
        });

        this.items = [ history, vouchers ];

        Vpc.Shop.Cart.Plugins.Voucher.Panel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.shop.cart.plugins.voucher', Vpc.Shop.Cart.Plugins.Voucher.Panel);
