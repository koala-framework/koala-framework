Ext.namespace('Vpc.Abstract.List');

Vpc.Abstract.List.MultiFileUploadPanel = Ext.extend(Ext.Panel,
{
    afterRender: function() {
        Vpc.Abstract.List.MultiFileUploadPanel.superclass.afterRender.call(this);

        if (this.allowOnlyImages) {
            fileTypes = '*.jpg;*.jpeg;*.gif;*.png';
            fileTypesDescription = 'Web Image Files';
        } else {
            fileTypes = '*.*';
            fileTypesDescription = 'All Files';
        }

        //cookie als post mitschicken
        var params = {};
        var cookies = document.cookie.split(';');
        Ext.each(cookies, function(c) {
            c = c.split('=');

            if (c[0].trim() == 'PHPSESSID' && c[1]) {
                params.PHPSESSID = c[1];
            }
        });
        if (!params.PHPSESSID) return;
//         if (!(navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"])){
//             return;
//         }
        var container = this.body.createChild();

        this.swf = new SWFUpload({
            upload_url: location.protocol+'/'+'/'+location.host+this.controllerUrl+'/json-multi-upload',
            flash_url: '/assets/swfupload/Flash/swfupload.swf',
            button_placeholder_id: container.id,
            button_image_url: "/assets/vps/Vps_js/Form/SwfUploadField/button.jpg",
            button_width: "120",
            button_height: "21",
            button_text: '<span class="theFont">'+trlVps('Upload Files')+'</span>',
            button_text_style: ".theFont { font-size: 11px; font-family:tahoma,verdana, helvetica;}",
            button_text_left_padding: 28,
            button_text_top_padding: 2,
            button_window_mode: SWFUpload.WINDOW_MODE.OPAQUE,

            file_queued_handler: function(file) {
                if (!this.vpsFiles) this.vpsFiles = [];
                this.vpsFiles.push(file.id);

                if (this.running) {
                    return;
                }

                this.running = true;
                this.progress = Ext.MessageBox.show({
                    title : trlVps('Upload'),
                    msg : trlVps('Uploading file'),
                    buttons: false,
                    progress:true,
                    closable:false,
                    minWidth: 250,
                    buttons: Ext.MessageBox.CANCEL,
                    scope: this,
                    fn: function(button) {
                        for(var i=0;i<this.vpsFiles.length;i++) {
                            this.cancelUpload(this.getFile(i).id);
                        }
                    }
                });
                this.startUpload(file.id);
            },
            upload_progress_handler: function(file, done, total) {
                var total = 0;
                var sumDone = 0;
                for(var i=0;i<this.vpsFiles.length;i++) {
                    total += this.getFile(i).size;
                    if (this.getFile(i).id == file.id) {
                        sumDone += done;
                    } else if (this.getFile(i).filestatus != SWFUpload.FILE_STATUS.QUEUED) {
                        sumDone += this.getFile(i).size;
                    }
                }
                console.log(total, sumDone);
                this.progress.updateProgress(sumDone/total);
            },
            upload_success_handler: function(file, response) {
                console.log(response);
                try {
                    var r = Ext.util.JSON.decode(response);
                } catch(e) {
                    Vps.handleError('Upload Error');
                    return;
                }
                console.log(r);
                if (r.success) {
                    for(var i=0;i<this.vpsFiles.length;i++) {
                        console.log(i, this.getFile(i).name, this.getFile(i).filestatus);
                        if (this.getFile(i).filestatus == SWFUpload.FILE_STATUS.QUEUED) {
                            //neeext
                            this.startUpload(this.getFile(i).id);
                            return;
                        }
                    }
                    this.running = false;
                    this.progress.hide();
                    //TODO: reload grid
                } else {
                    this.progress.hide();
                    if (r.wrongversion) {
                        Ext.Msg.alert(trlVps('Error - wrong version'),
                        trlVps('Because of an application update the application has to be reloaded.'),
                        function(){
                            location.reload();
                        });
                        return;
                    }
                    if (r.login) {
                        var dlg = new Vps.User.Login.Dialog({
                            message: r.message,
                            success: function() {
                                //redo action...
                                this.startUpload(file.id);
                            },
                            scope: this
                        });
                        Ext.getBody().unmask();
                        dlg.showLogin();
                        return;
                    }
                    if (r.error) {
                        Ext.Msg.alert(trlVps('Error'), r.error);
                    } else {
                        Vps.handleError(r.exception, 'Error', !r.exception);
                    }
                    this.running = false;
                    for(var i=0;i<this.vpsFiles.length;i++) {
                        this.cancelUpload(this.getFile(i).id, false);
                    }
                }
            },
            upload_error_handler: function(file, errorCode, errorMessage) {
                console.log('upload_error_handler');
                this.progress.hide();
                // Upload Errors
                var message = errorMessage;
                if (errorCode == SWFUpload.UPLOAD_ERROR.HTTP_ERROR) {
                    message = trlVps("A http Error occured.");
                } else if (errorCode == SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL) {
                    message = trlVps("Upload URL string is empty.");
                } else if (errorCode == SWFUpload.UPLOAD_ERROR.IO_ERROR) {
                    message = trlVps("IO Error.");
                } else if (errorCode == SWFUpload.UPLOAD_ERROR.SECURITY_ERROR) {
                    message = trlVps("Security Error.");
                } else if (errorCode == SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED) {
                    message = trlVps("The upload limit has been reached.");
                } else if (errorCode == SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED) {
                    message = errorMessage;
                } else if (errorCode == SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND) {
                    message = "File ID not found in the queue.";
                } else if (errorCode == SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED) {
                    message = "Call to uploadStart return false. Not uploading file.";
                } else if (errorCode == SWFUpload.UPLOAD_ERROR.FILE_CANCELLED) {
                    message = trlVps("File Upload Cancelled.");
                } else if (errorCode == SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED) {
                    message = trlVps("Upload Stopped");
                }
                if (errorCode != SWFUpload.UPLOAD_ERROR.FILE_CANCELLED) {
                    //Vps.handleError(errorMessage, 'Upload Error');
                    Ext.Msg.alert(trlVps("Upload Error"), message);
                }
            },
            file_queue_error_handler: function(file, errorCode, errorMessage) {
                console.log('file_queue_error_handler');
                var message = trlVps("File is zero bytes or cannot be accessed and cannot be uploaded.");
                if (errorCode == SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT) {
                    message = trlVps("File size exceeds allowed limit.");
                } else if (errorCode == SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
                    message = trlVps("File size exceeds allowed limit.");
                } else if (errorCode == SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE) {
                    message = trlVps("File is zero bytes and cannot be uploaded.");
                } else if (errorCode == SWFUpload.QUEUE_ERROR.INVALID_FILETYPE) {
                    message = trlVps("File is not an allowed file type.");
                }
                Ext.Msg.alert(trlVps("Upload Error"), message);
            },
            swfupload_loaded_handler: function() {
                console.log('swfupload_loaded_handler');
                //wenn CallFunction nicht vorhanden funktioniert der uploader nicht.
                //dann einfach durch die html version ersetzen
                if (!this.getMovieElement().CallFunction) {
                    this.customSettings.field.createUploadButton(true);
                    return;
                }
                this.customSettings.field.useSwf = true;
            }
        });
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

        var westItems = [this.grid];

        if (this.multiFileUpload) {
            this.multiFileUploadPanel = new Vpc.Abstract.List.MultiFileUploadPanel({
                region: 'south',
                height: 150,
                controllerUrl: this.controllerUrl
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
