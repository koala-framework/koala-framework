Ext.namespace('Vpc.Abstract.List');

Vpc.Abstract.List.MultiFileUploadPanel = Ext.extend(Ext.Panel,
{
    afterRender: function() {
        Vpc.Abstract.List.MultiFileUploadPanel.superclass.afterRender.call(this);

        if (this.list.multiFileUpload.allowOnlyImages) {
            fileTypes = '*.jpg;*.jpeg;*.gif;*.png';
            fileTypesDescription = trlVps('Web Image Files');
        } else {
            fileTypes = '*.*';
            fileTypesDescription = trlVps('All Files');
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
        params.maxResolution = this.list.multiFileUpload.maxResolution;
        if (!params.PHPSESSID) return;

        var container = this.body.createChild();

        this.swf = new SWFUpload({
            minimum_flash_version : "9.0.28",
            custom_settings: {list: this.list},
            upload_url: location.protocol+'/'+'/'+location.host+'/vps/media/upload/json-upload',
            flash_url: '/assets/swfupload/Flash/swfupload.swf',
            file_size_limit: this.list.multiFileUpload.fileSizeLimit,
            file_types: fileTypes,
            file_types_description: fileTypesDescription,
            post_params: params,
            button_placeholder_id: container.id,
            button_image_url: "/assets/vps/Vps_js/Form/File/button.jpg",
            button_width: "120",
            button_height: "21",
            button_text: '<span class="theFont">'+trlVps('Upload Files')+'</span>',
            button_text_style: ".theFont { font-size: 11px; font-family:tahoma,verdana, helvetica;}",
            button_text_left_padding: 28,
            button_text_top_padding: 2,
            button_window_mode: SWFUpload.WINDOW_MODE.OPAQUE,
            button_cursor : SWFUpload.CURSOR.HAND,
            button_action : SWFUpload.BUTTON_ACTION.SELECT_FILES,

            file_queued_handler: function(file) {
                if (!this.vpsFiles) this.vpsFiles = [];
                this.vpsFiles.push(file.id);

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
                        for(var i=0;i<this.vpsFiles.length;i++) {
                            if (this.getFile(i)) this.cancelUpload(this.getFile(i).id);
                        }
                        this.vpsFiles = [];
                        this.running = false;
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
                this.progress.updateProgress(sumDone/total);
            },
            upload_success_handler: function(file, response) {
                try {
                    var r = Ext.util.JSON.decode(response);
                } catch(e) {
                    Vps.handleError('Upload Error');
                    return;
                }
                if (r.success) {

                    this.uploadedIds.push(r.value.uploadId);

                    for(var i=0;i<this.vpsFiles.length;i++) {
                        if (this.getFile(i).filestatus == SWFUpload.FILE_STATUS.QUEUED) {
                            //neeext
                            this.startUpload(this.getFile(i).id);
                            return;
                        }
                    }
                    this.running = false;
                    this.vpsFiles = [];
                    this.progress.hide();

                    var params = Ext.apply(this.customSettings.list.getBaseParams(), { uploadIds: this.uploadedIds.join(',')});
                    Ext.Ajax.request({
                        url: location.protocol+'/'+'/'+location.host+this.customSettings.list.controllerUrl+'/json-multi-upload',
                        params: params,
                        success: function() {
                            this.customSettings.list.grid.reload();
                        },
                        scope: this
                    })
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
                    this.vpsFiles = [];
                }
            },
            upload_error_handler: function(file, errorCode, errorMessage) {
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
