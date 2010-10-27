Ext.namespace('Vpc.Abstract.List');

Vpc.Abstract.List.MultiFileUploadPanel = Ext.extend(Ext.Panel,
{
    afterRender: function() {
        Vpc.Abstract.List.MultiFileUploadPanel.superclass.afterRender.call(this);

        var container = this.body.createChild();

        if (!Vps.Utils.Upload.supportsHtml5Upload()) {
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
                            this.swfu.cancelUpload(this.files[i]);
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
                    var f = this.swfu.getFile(this.files[i])
                    total += f.size;
                    if (f.id == file.id) {
                        sumDone += done;
                    } else if (f.filestatus != SWFUpload.FILE_STATUS.QUEUED) {
                        sumDone += f.size;
                    }
                }
                this.progress.updateProgress(sumDone/total);
            }, this);
            this.swfu.on('uploadSuccess', function(file, r) {
                this.uploadedIds.push(r.value.uploadId);

                for(var i=0;i<this.files.length;i++) {
                    if (this.swfu.getFile(this.files[i]).filestatus == SWFUpload.FILE_STATUS.QUEUED) {
                        //neeext
                        this.swfu.startUpload(this.files[i]);
                        return;
                    }
                }
                this.running = false;
                this.files = [];
                this.progress.hide();

                var params = Ext.apply(this.list.getBaseParams(), { uploadIds: this.uploadedIds.join(',')});
                Ext.Ajax.request({
                    url: this.list.controllerUrl+'/json-multi-upload',
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
        } else {
            container.setStyle('position', 'relative');
            this.uploadButton = new Ext.Button({
                text: trlVps('Upload Files'),
                cls: 'x-btn-text-icon',
                icon: '/assets/silkicons/add.png',
                renderTo: container
            });

            var fileInputContainer = container.createChild({
                style: 'width: 120px; height: 20px; top: 0; position: absolute; overflow: hidden;'
            });

            var fileInput = fileInputContainer.createChild({
                tag: 'input',
                type: 'file',
                multiple: 'multiple',
                style: 'opacity: 0; cursor: pointer; '
            });
            fileInput.on('change', function(ev, dom) {
                if (dom.files) {
                    this.html5UploadFiles(dom.files);
                    dom.value = ''; //leeren, damit gleiche datei nochmal gewählt werden kann
                }
            }, this);

            this.list.westPanel.el.on('dragenter', function(e) {
                e.browserEvent.stopPropagation();
                e.browserEvent.preventDefault();
            }, this);
            this.list.westPanel.el.on('dragover', function(e) {
                e.browserEvent.stopPropagation();
                e.browserEvent.preventDefault();
            }, this);
            this.list.westPanel.el.on('drop', function(e) {
                e.browserEvent.stopPropagation();
                e.browserEvent.preventDefault();
                if (e.browserEvent.dataTransfer) {
                    this.html5UploadFiles(e.browserEvent.dataTransfer.files);
                }
            }, this);
        }
    },
    html5UploadFiles: function(files)
    {
        if (!files.length) return;

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
                this.fileQueue = [];
                this.currentXhr.abort();
            }
        });

        this.processedFiles = [];
        this.fileQueue = [];
        this.uploadedIds = [];
        for(var i=0;i<files.length;++i) {
            this.fileQueue.push(files[i]);
        }
        this.html5UploadFile();
    },

    updateProgress: function(e)
    {
        var total = 0;
        if (e && e.total) total += e.total;
        var loaded = 0;
        if (e && e.loaded) loaded += e.loaded;
        this.processedFiles.forEach(function(f) {
            total += f.size;
            loaded += f.size;
        }, this);
        this.fileQueue.forEach(function(f) {
            total += f.size;
        }, this);
        this.progress.updateProgress(loaded / total);
    },

    html5UploadFile: function()
    {
        var file = this.fileQueue.pop();
        this.currentXhr = Vps.Utils.Upload.uploadFile({
            maxResolution: this.maxResolution,
            file: file,
            success: function(r, options) {
                this.processedFiles.push(options.file);
                this.uploadedIds.push(r.value.uploadId);
                if (this.fileQueue.length) {
                    this.updateProgress();
                    this.html5UploadFile(); //neext
                } else {
                    var params = Ext.apply(this.list.getBaseParams(), { uploadIds: this.uploadedIds.join(',')});
                    Ext.Ajax.request({
                        url: this.list.controllerUrl+'/json-multi-upload',
                        params: params,
                        success: function() {
                            this.progress.hide();
                            this.list.grid.reload();
                        },
                        scope: this
                    });
                }
            },
            failure: function() {
                this.progress.hide();
            },
            progress: function(e) {
                this.updateProgress(e);
            },
            scope: this
        });
    },
    onDestroy: function() {
        if (this.swfu) this.swfu.destroy();
    }
});
Vpc.Abstract.List.List = Ext.extend(Vps.Binding.ProxyPanel,
{
    border: false,
    initComponent: function()
    {
        if (this.childConfig.title) delete this.childConfig.title;
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

        this.westPanel = new Ext.Panel({
            layout: 'border',
            region: 'west',
            width: 300,
            border: false,
            items: westItems
        });

        this.layout = 'border';
        this.items = [this.westPanel, this.childPanel];
        Vpc.Abstract.List.List.superclass.initComponent.call(this);
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
Ext.reg('vpc.list.list', Vpc.Abstract.List.List);
