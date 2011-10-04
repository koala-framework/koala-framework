Ext.namespace('Vps.Utils');
Vps.Utils.MultiFileUploadPanel = Ext.extend(Ext.Panel,
{
    fileSizeLimit: null,
    allowOnlyImages: false,
    maxResolution: null,
    baseParams: {},
    controllerUrl: '',
    
    afterRender: function() {
        Vps.Utils.MultiFileUploadPanel.superclass.afterRender.call(this);

        var container = this.body.createChild();

        if (!Vps.Utils.Upload.supportsHtml5Upload()) {
            this.swfu = new Vps.Utils.SwfUpload({
                fileSizeLimit: this.fileSizeLimit,
                allowOnlyImages: this.allowOnlyImages,
                buttonPlaceholderId: container.id,
                postParams: {
                    maxResolution: this.maxResolution
                },
                buttonText: trlVps('Upload Files'),
                selectMultiple: true
            });
            this.swfu.on('fileQueued', function(file) {
                if (!this.numFiles) this.numFiles = 0;
                this.numFiles++;

                if (this.running) {
                    return;
                }

                this.uploadedIds = [];
                this.running = true;
                this.startFileIndex = this.numFiles-1;
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
                        for(var i=this.startFileIndex;i<this.numFiles;i++) {
                            this.swfu.cancelUpload(this.swfu.getFile(i));
                        }
                        this.running = false;
                    }
                });
                this.swfu.startUpload(file.id);
            }, this);
            this.swfu.on('uploadProgress', function(file, done, total) {
                var total = 0;
                var sumDone = 0;
                for(var i=this.startFileIndex;i<this.numFiles;i++) {
                    var f = this.swfu.getFile(i)
                    total += f.size;
                    if (f.id == file.id) {
                        sumDone += done;
                    } else if (f.filestatus != SWFUpload.FILE_STATUS.QUEUED) {
                        sumDone += f.size;
                    }
                }
                this.progress.updateProgress(sumDone/total - 0.1);
            }, this);
            this.swfu.on('uploadSuccess', function(file, r) {
                this.uploadedIds.push(r.value.uploadId);

                for(var i=this.startFileIndex;i<this.numFiles;i++) {
                    if (this.swfu.getFile(i).filestatus == SWFUpload.FILE_STATUS.QUEUED) {
                        //neeext
                        this.swfu.startUpload(this.swfu.getFile(i).id);
                        return;
                    }
                }
                this.running = false;
                this.progress.updateProgress(0.9);

                var params = Ext.apply(this.baseParams, { uploadIds: this.uploadedIds.join(',')});
                Ext.Ajax.request({
                    url: this.controllerUrl+'/json-multi-upload',
                    params: params,
                    success: function() {
                        this.progress.hide();
                        this.fireEvent('uploaded');
                    },
                    failure: function() {
                        this.progress.hide();
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

            this.el.on('dragenter', function(e) {
                e.browserEvent.stopPropagation();
                e.browserEvent.preventDefault();
            }, this);
            this.el.on('dragover', function(e) {
                e.browserEvent.stopPropagation();
                e.browserEvent.preventDefault();
            }, this);
            this.el.on('drop', function(e) {
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
        var file = this.fileQueue.shift();
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
                    var params = Ext.apply(this.baseParams, { uploadIds: this.uploadedIds.join(',')});
                    Ext.Ajax.request({
                        url: this.controllerUrl+'/json-multi-upload',
                        params: params,
                        success: function() {
                            this.progress.hide();
                            this.fireEvent('uploaded');
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
