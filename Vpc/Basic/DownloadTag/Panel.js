Ext.ns('Vpc.Basic.DownloadTag');
Vpc.Basic.DownloadTag.Panel = Ext.extend(Ext.Panel, {
    initComponent: function() {
        Vpc.Basic.DownloadTag.Panel.superclass.initComponent.call(this);
        this.findByType('vps.file')[0].on('uploaded', function(field, value) {
            if (value) {
                var v = value.filename;
                v = v.toLowerCase().replace(/ä/g, 'ae').replace(/ö/g, 'oe')
                     .replace(/ü/g, 'ue').replace(/ß/g, 'ss')
                     .replace(/[^a-z0-9]/g, '_').replace(/__+/g, '_');
                this.find('isFilename', true)[0].setValue(v);
            }
        }, this);
    }
});
Ext.reg('Vpc.Basic.DownloadTag', Vpc.Basic.DownloadTag.Panel);
