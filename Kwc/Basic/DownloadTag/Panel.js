Ext2.ns('Kwc.Basic.DownloadTag');
Kwc.Basic.DownloadTag.Panel = Ext2.extend(Ext2.Panel, {
    initComponent: function() {
        Kwc.Basic.DownloadTag.Panel.superclass.initComponent.call(this);
        this.findByType('kwf.file')[0].on('uploaded', function(field, value) {
            if (value) {
                this.ownerCt.find('autoFillWithFilename', 'filename').forEach(function (f) {
                    var v = value.uploaded_filename || value.filename;
                    v = v.toLowerCase().replace(/ä/g, 'ae').replace(/ö/g, 'oe')
                        .replace(/ü/g, 'ue').replace(/ß/g, 'ss')
                        .replace(/[^a-z0-9]/g, '_').replace(/__+/g, '_');
                    f.setValue(v);
                }, this);
                this.ownerCt.find('autoFillWithFilename', 'filenameWithExt').forEach(function (f) {
                    if (f.getValue() == '') {
                        f.setValue(value.filename+'.'+value.extension);
                    }
                }, this);
            }
        }, this);
    }
});
Ext2.reg('Kwc.Basic.DownloadTag', Kwc.Basic.DownloadTag.Panel);
