Ext2.namespace('Kwc.Shop.Cart.Plugins.Voucher');
Kwc.Shop.Cart.Plugins.Voucher.Panel = Ext2.extend(Ext2.Panel, {
    layout: 'border',
    initComponent: function() {
        var history = new Kwf.Auto.GridPanel({
            controllerUrl: this.voucherHistoryControllerUrl,
            region: 'center'
        });
        var vouchers = new Kwf.Auto.GridPanel({
            controllerUrl: this.vouchersControllerUrl,
            region: 'west',
            width: 400,
            editDialog: new Kwf.Auto.Form.Window({
                controllerUrl: this.voucherControllerUrl
            }),
            bindings: [{
                item: history,
                queryParam: 'voucher_id'
            }]
        });

        this.items = [ history, vouchers ];

        Kwc.Shop.Cart.Plugins.Voucher.Panel.superclass.initComponent.call(this);
    }
});
Ext2.reg('kwc.shop.cart.plugins.voucher', Kwc.Shop.Cart.Plugins.Voucher.Panel);
