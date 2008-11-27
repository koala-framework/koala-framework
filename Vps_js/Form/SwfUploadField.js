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

        this.swfReady = false;

        Vps.Form.SwfUploadField.superclass.initComponent.call(this);
    },
    afterRender: function() {
        Vps.Form.SwfUploadField.superclass.afterRender.call(this);
        this.previewImage = this.el.createChild({
            style: 'margin-right: 10px; padding: 5px; float: left; border: 1px solid #b5b8c8;'+
                   'background-color: white;'
        });
        this.uploadButton = new Ext.Button({
            text: trlVps('Upload File'),
            cls: 'x-btn-text-icon',
            icon: '/assets/silkicons/add.png',
            renderTo: this.el.createChild({ style: 'float:left; margin-right: 5px; ' }),
            scope: this,
            handler: function() {
                if (this.swfReady) {
                    this.swfu.selectFile();
                } else {
                    var win = new Vps.Form.FileUploadWindow();
                    win.on('uploaded', function(win, result) {
                        this.setValue(result.value);
                        this.fireEvent('uploaded', this, result.value);
                    }, this);
                    win.show();
                }
            }
        });
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

        if (Ext.isLinux) return;
        return; // Flash Uploader deaktiviert
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
            if (c[0] == 'PHPSESSID' && c[1]) {
                params.PHPSESSID = c[1];
            }
        });
        if (!params.PHPSESSID) return;
        this.swfu = new SWFUpload({
            custom_settings: {field: this},
            upload_url: location.protocol+'/'+'/'+location.host+'/vps/media/upload/json-upload',
            flash_url: '/assets/swfupload/Flash9/swfupload_f9.swf',
            file_size_limit: this.fileSizeLimit,
            file_types: fileTypes,
            file_types_description: fileTypesDescription,
            post_params: params,
            swfupload_loaded_handler: function() {
                this.customSettings.field.swfReady = true;
            },
            file_queued_handler: function(file) {
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
                if (errorCode != SWFUpload.UPLOAD_ERROR.FILE_CANCELLED) {
                    Vps.handleError(errorMessage, 'Upload Error');
                }
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
                    icon = '/vps/media/upload/preview?uploadId='+value.uploadId;
                } else {
                    icon = this.fileIcons[value.mimeType] || this.fileIcons['default'];
                    icon = '/assets/silkicons/' + icon + '.png';
                }
                this.previewTpl.overwrite(this.previewImage, {
                    preview: icon,
                    href: '/vps/media/upload/download?uploadId='+value.uploadId
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

