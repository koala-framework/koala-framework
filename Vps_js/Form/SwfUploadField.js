Vps.Form.SwfUploadField = Ext.extend(Ext.form.Field, {
    allowOnlyImages: false,
    fileSizeLimit: 0,
    defaultAutoCreate : {
        tag: 'div',
        style: 'width: 300px; height: 53px'
    },
    fileIcons: {
        'application/pdf': 'page_white_acrobat',
        'application/x-zip': 'page_white_compressed',
        'application/msexcel': 'page_white_excel',
        'application/msword': 'page_white_word',
        'application/mspowerpoint': 'page_white_powerpoint',
        'default': 'page_white'
    },
    previewTpl: ['<a href="{href}" target="_blank" ',
                 'style="width: 40px; height: 40px; display: block; background-repeat: no-repeat; background-position: center; background-image: url({preview});"></a>'],
    infoTpl: ['{filename}.{extension}<br />',
              '{fileSize:fileSize}',
              '<tpl if="image">, {imageWidth}x{imageHeight}px</tpl>'],
    emptyTpl: '<div style="height: 40px; width: 40px; text-align: center;"><br />('+trlVps('empty')+')</div>',

    initComponent: function() {
        this.addEvents(['uploaded']);
        if (!(this.previewTpl instanceof Ext.XTemplate)) {
            this.previewTpl = new Ext.XTemplate(this.previewTpl);
        }
        this.previewTpl.compile();

        if (!(this.infoTpl instanceof Ext.XTemplate)) {
            this.infoTpl = new Ext.XTemplate(this.infoTpl);
        }
        this.infoTpl.compile();

        Vps.Form.SwfUploadField.superclass.initComponent.call(this);

    },
    afterRender: function() {
        Vps.Form.SwfUploadField.superclass.afterRender.call(this);
        this.previewImage = this.el.createChild({
            style: 'margin-right: 10px; padding: 5px; float: left; border: 1px solid #b5b8c8;'+
                   'background-color: white;'
        });

        this.previewImage.update(this.emptyTpl); //bild wird in der richtigen größe angezeigt

        this.uploadButtonContainer = this.el.createChild({
                                         style: 'float:left; margin-right: 5px; '
                                     });

        this.uploadButtonContainerChildId = 'uploadButton'+Ext.id();
        this.createUploadButton(false);
        this.deleteButton = new Ext.Button({
            text: trlVps('Delete File'),
            cls: 'x-btn-text-icon',
            icon: '/assets/silkicons/delete.png',
            renderTo: this.el.createChild({}),
            scope: this,
            handler: function() {
                this.setValue('');
            }
        });
        this.infoContainer = this.el.createChild({
            style: 'margin-top: 3px;'
        });

        //return; // Flash Uploader deaktiviert
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
        if (!(navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"])){
            return;
        }
        /*this.uploadChild = this.uploadButtonContainer.createChild({
            id: 'uploadButton'+Ext.id()
        });*/
        this.checkSwf = false;
        this.swfu = new SWFUpload({
            custom_settings: {field: this},
            upload_url: location.protocol+'/'+'/'+location.host+'/vps/media/upload/json-upload',
            flash_url: '/assets/swfupload/Flash/swfupload.swf',
            file_size_limit: this.fileSizeLimit,
            file_types: fileTypes,
            file_types_description: fileTypesDescription,
            post_params: params,
            button_image_url: "/assets/swfupload/Flash/uploadbutton.jpg",
            button_width: "120",
            button_height: "21",
            button_placeholder_id: this.uploadButtonContainerChildId,
            button_text: '<span class="theFont">'+trlVps('Upload File')+'</span>',
            button_text_style: ".theFont { font-size: 11; font-family:tahoma,verdana, helvetica;}",
            button_text_left_padding: 28,
            button_text_top_padding: 2,

            file_queued_handler: function(file) {
                this.checkSwf = true;
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
                        this.cancelUpload(file.id);
                    }
                });
                this.startUpload(file.id);
            },
            upload_progress_handler: function(file, done, total) {
                this.progress.updateProgress(done/total);
            },
            upload_success_handler: function(file, response) {
                this.progress.hide();
                try {
                    var r = Ext.util.JSON.decode(response);
                } catch(e) {
                    Vps.handleError(response, 'Upload Error');
                    return;
                }
                if (r.success) {
                    this.customSettings.field.setValue(r.value);
                    this.customSettings.field.fireEvent('uploaded', this, r.value);
                } else {
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
                    message = "File Upload Cancelled.";
                } else if (errorCode == SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED) {
                    message = "Upload Stopped";
                }
                if (errorCode != SWFUpload.UPLOAD_ERROR.FILE_CANCELLED) {
                    //Vps.handleError(errorMessage, 'Upload Error');
                     Ext.Msg.alert(trlVps("Upload Error"), message);
                }
            },
            file_queue_error_handler: function(file, errorCode, errorMessage) {
                var message = "File is zero bytes or cannot be accessed and cannot be uploaded.";
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
            swfupload_loaded_handler: function(file, errorCode, errorMessage) {
                this.customSettings.field.checkSwf = true;
            }
        });
        var checkButton = function() {
            if (!this.checkSwf) {
                this.createUploadButton(true);
            }
        };
        checkButton.defer(5000, this);

    },

    createUploadButton : function (check) {
        if (check == true) {
            this.uploadButtonContainer.first().remove();
        }
        this.uploadButton = new Ext.Button({
            text: trlVps('Upload File'),
            cls: 'x-btn-text-icon',
            icon: '/assets/silkicons/add.png',
            renderTo: this.uploadButtonContainer.createChild({
                id: this.uploadButtonContainerChildId
            }),
            scope: this,
            handler: function() {
                var win = new Vps.Form.FileUploadWindow({
                    maxResolution: this.maxResolution
                });
                win.on('uploaded', function(win, result) {
                    this.setValue(result.value);
                    this.fireEvent('uploaded', this, result.value);
                }, this);
                win.show();
            }
        });
    },
    onDestroy: function()
    {
        Vps.Form.SwfUploadField.superclass.onDestroy.call(this);
        if (this.swfu) this.swfu.destroy();
    },

    // überschrieben weil ext implementation auf this.el.dom.value zugreift was wir nicht haben
    initValue : function() {
        if(this.value !== undefined){
            this.setValue(this.value);
        }
    },

    setValue: function(value)
    {
        var v = '';
        if (value.uploadId) {
            v = value.uploadId;
        }
        if (v != this.value) {
            this.fireEvent('change', this, value, this.value);
            var icon = false;
            if (value.mimeType) {
                if (value.mimeType.match(/(^image\/)/)) {
                    icon = '/vps/media/upload/preview?uploadId='+value.uploadId+'&hashKey='+value.hashKey;
                } else {
                    icon = this.fileIcons[value.mimeType] || this.fileIcons['default'];
                    icon = '/assets/silkicons/' + icon + '.png';
                }
                this.previewTpl.overwrite(this.previewImage, {
                    preview: icon,
                    href: '/vps/media/upload/download?uploadId='+value.uploadId+'&hashKey='+value.hashKey
                });
                this.infoTpl.overwrite(this.infoContainer, value);
            } else {
                this.previewImage.update(this.emptyTpl);
                this.infoContainer.update('');
            }
        }
        Vps.Form.SwfUploadField.superclass.setValue.call(this, value.uploadId);
    },

    validateValue : function(value){
        if(!value){ // if it's blank
             if(this.allowBlank){
                 this.clearInvalid();
                 return true;
             }else{
                 this.markInvalid(trlVps('This field is required'));
                 return false;
             }
        }
        return true;
    }

});



Ext.reg('swfuploadfield', Vps.Form.SwfUploadField);




