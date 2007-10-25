Ext.namespace('Vpc.Basic.Link');
Vpc.Basic.Link.Index = Ext.extend(Vps.Auto.FormPanel, {
    initComponent: function()
    {
        Vpc.Basic.Link.Index.superclass.initComponent.call(this);

        this.on('renderform', function()
        {
/*
            var dlg = this.bookingDialog, frm = dlg.getForm();
            frm.findField('type_course').on('check', function(field, value) {
                dlg.findById('tabCourse').setDisabled(!value);
            }, this);
            frm.findField('type_transfer').on('check', function(field, value) {
                dlg.findById('tabTransfer').setDisabled(!value);
            }, this);
            frm.findField('type_material_leasing').on('check', function(field, value) {
                dlg.findById('tabMaterialLeasing').setDisabled(!value);
            }, this);
            frm.findField('type_boat_trip').on('check', function(field, value) {
                dlg.findById('tabBoatTrip').setDisabled(!value);
            }, this);

            dlg.findById('tabCourse').disable();
            dlg.findById('tabMaterialLeasing').disable();
            dlg.findById('tabTransfer').disable();
            dlg.findById('tabBoatTrip').disable();
            */
           debugger;
        }, this);
    }
});
