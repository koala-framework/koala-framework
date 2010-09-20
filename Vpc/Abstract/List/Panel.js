Ext.namespace('Vpc.Abstract.List');

Vpc.Abstract.List.MultiFileUploadPanel = Ext.extend(Ext.Panel,
{
    afterRender: function() {
        Vpc.Abstract.List.MultiFileUploadPanel.superclass.afterRender.call(this);

        var container = this.body.createChild();

        this.swfu = new Vps.Utils.SwfUpload({
            fileSizeLimit: this.list.multiFileUpload.fileSizeLimit,
            allowOnlyImages: this.list.multiFileUpload.allowOnlyImages,
            buttonPlaceholderId: container.id,
            postParams: {
                maxResolution: this.list.multiFileUpload.maxResolution
            },
            buttonText: trlVps('Upload Files'),
            selectMultiple: true
        });
        this.swfu.on('fileQueued', function(file) {
            if (!this.files) this.files = [];
            this.files.push(file.id);

            if (this.running) {
                return;
            }

            this.uploadedIds = [];
            this.running = true;
            this.progress = Ext.MessageBox.show({
                title : trlVps('Upload'),
                msg : trlVps('Uploading files'),
                buttons: false,
                progress:true,
                closable:false,
                minWidth: 250,
                buttons: Ext.MessageBox.CANCEL,
                scope: this,
                fn: function(button) {
                    for(var i=0;i<this.files.length;i++) {
                        if (this.swfu.getFile(i)) this.swfu.cancelUpload(this.swfu.getFile(i).id);
                    }
                    this.files = [];
                    this.running = false;
                }
            });
            this.swfu.startUpload(file.id);
        }, this);
        this.swfu.on('uploadProgress', function(file, done, total) {
            var total = 0;
            var sumDone = 0;
            for(var i=0;i<this.files.length;i++) {
                total += this.swfu.getFile(i).size;
                if (this.swfu.getFile(i).id == file.id) {
                    sumDone += done;
                } else if (this.swfu.getFile(i).filestatus != SWFUpload.FILE_STATUS.QUEUED) {
                    sumDone += this.swfu.getFile(i).size;
                }
            }
            this.progress.updateProgress(sumDone/total);
        }, this);
        this.swfu.on('uploadSuccess', function(file, r) {
            this.uploadedIds.push(r.value.uploadId);

            for(var i=0;i<this.files.length;i++) {
                if (this.swfu.getFile(i).filestatus == SWFUpload.FILE_STATUS.QUEUED) {
                    //neeext
                    this.swfu.startUpload(this.swfu.getFile(i).id);
                    return;
                }
            }
            this.running = false;
            this.files = [];
            this.progress.hide();

            var params = Ext.apply(this.list.getBaseParams(), { uploadIds: this.uploadedIds.join(',')});
            Ext.Ajax.request({
                url: location.protocol+'/'+'/'+location.host+this.list.controllerUrl+'/json-multi-upload',
                params: params,
                success: function() {
                    this.list.grid.reload();
                },
                scope: this
            })
        }, this);
        this.swfu.on('uploadError', function(file, errorCode, errorMessage) {
            this.progress.hide();
        }, this);
    },
    onDestroy: function() {
        this.swfu.destroy();
    }
});
Vpc.Abstract.List.Panel = Ext.extend(Vps.Binding.ProxyPanel,
{
    initComponent: function()
    {
        this.childPanel = Ext.ComponentMgr.create(Ext.applyIf(this.childConfig, {
            region: 'center'
        }));

        this.grid = new Vps.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            split: true,
            region: 'center',
            baseParams: this.baseParams, //Kompatibilität zu ComponentPanel
            autoLoad: this.autoLoad,
            bindings: [{
                item        : this.childPanel,
                componentIdSuffix: '-{0}'
            }],
            onAdd: this.onAdd
        });
        this.proxyItem = this.grid;

        this.grid.on('datachange', function() {
            this.childPanel.reload();
        }, this);

        var westItems = [this.grid];

        if (this.multiFileUpload) {
            this.multiFileUploadPanel = new Vpc.Abstract.List.MultiFileUploadPanel({
                border: false,
                region: 'south',
                height: 50,
                bodyStyle: 'padding-top: 15px; padding-left:80px;',
                list: this
            });
            westItems.push(this.multiFileUploadPanel);
        }

        this.layout = 'border';
        this.items = [{
            layout: 'border',
            region: 'west',
            width: 300,
            items: westItems
        }, this.childPanel];
        Vpc.Abstract.List.Panel.superclass.initComponent.call(this);
    },

    load: function()
    {
        this.grid.load();
        this.grid.selectId(false);

        this.childPanel.setBaseParams({});
        var f = this.childPanel.getForm();
        if (f) {
            f.clearValues();
            f.clearInvalid();
        }
        this.childPanel.disable();
    },

    onAdd : function()
    {
        Ext.Ajax.request({
            mask: true,
            url: this.controllerUrl + '/json-insert',
            params: this.getBaseParams(),
            success: function(response, options, r) {
                this.getSelectionModel().clearSelections();
                this.reload({
                    callback: function(o, r, s) {
                        this.getSelectionModel().selectLastRow();
                    },
                    scope: this
                });
            },
            scope: this
        });
    }
});
Ext.reg('vpc.list', Vpc.Abstract.List.Panel);
