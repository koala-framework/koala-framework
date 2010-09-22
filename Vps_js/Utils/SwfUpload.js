Vps.Utils.SwfUpload = function(config) {
    Ext.apply(this, config);
    this.addEvents(
        'fileQueued',
        'uploadProgress',
        'uploadSuccess',
        'uploadError',
        'flashLoadError'
    );
    Vps.Utils.SwfUpload.superclass.constructor.call(this);
    this.initSwf();
};
Ext.extend(Vps.Utils.SwfUpload, Ext.util.Observable, {
    //buttonPlaceholderId,
    //fileSizeLimit,
    //postParams,
    allowOnlyImages: false,
    buttonText: trlVps('Upload File'),
    selectMultiple: false,

    autoEl: 'div',
    useSwf: false,
    startUpload: function(fileId) {
        return this.swfu.startUpload(fileId);
    },
    cancelUpload: function(fileId) {
        return this.swfu.startUpload(fileId);
    },
    getFile: function(fileId) {
        return this.swfu.getFile(fileId);
    },
    destroy: function() {
        if (this.swfu) this.swfu.destroy();
    },

    initSwf: function()
    {
        if (this.allowOnlyImages) {
            fileTypes = '*.jpg;*.jpeg;*.gif;*.png';
            fileTypesDescription = trlVps('Web Image Files');
        } else {
            fileTypes = '*.*';
            fileTypesDescription = trlVps('All Files');
        }

        //cookie als post mitschicken
        var params = Vps.clone(this.postParams);
        var cookies = document.cookie.split(';');
        Ext.each(cookies, function(c) {
            c = c.split('=');

            if (c[0].trim() == 'PHPSESSID' && c[1]) {
                params.PHPSESSID = c[1];
            }
        });
        if (!params.PHPSESSID) return;

        this.swfu = new SWFUpload({
            minimum_flash_version : '9.0.28',
            custom_settings: {component: this},
            upload_url: location.protocol+'/'+'/'+location.host+'/vps/media/upload/json-upload',
            flash_url: '/assets/swfupload/Flash/swfupload.swf',
            file_size_limit: this.fileSizeLimit,
            file_types: fileTypes,
            file_types_description: fileTypesDescription,
            post_params: params,
            button_image_url: '/assets/vps/Vps_js/Form/File/button.jpg',
            button_width: '120',
            button_height: '21',
            button_placeholder_id: this.buttonPlaceholderId,
            button_text: '<span class="theFont">'+this.buttonText+'</span>',
            button_text_style: '.theFont { font-size: 11px; font-family:tahoma,verdana, helvetica;}',
            button_text_left_padding: 28,
            button_text_top_padding: 2,
            button_window_mode: SWFUpload.WINDOW_MODE.OPAQUE,
            button_action : this.selectMultiple ? SWFUpload.BUTTON_ACTION.SELECT_FILES : SWFUpload.BUTTON_ACTION.SELECT_FILE,
            button_cursor : SWFUpload.CURSOR.HAND,

            file_queued_handler: function(file) {
                this.customSettings.component.fireEvent('fileQueued', file);
            },
            upload_progress_handler: function(file, done, total) {
                this.customSettings.component.fireEvent('uploadProgress', file, done, total);
            },
            upload_success_handler: function(file, response) {
                try {
                    var r = Ext.util.JSON.decode(response);
                } catch(e) {
                    Vps.handleError(response, 'Upload Error');
                    return;
                }
                if (r.success) {
                    this.customSettings.component.fireEvent('uploadSuccess', file, r);
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
                this.customSettings.component.fireEvent('uploadError', file, errorCode, errorMessage);
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
            },
            swfupload_loaded_handler: function() {
                //wenn CallFunction nicht vorhanden funktioniert der uploader nicht.
                //dann einfach durch die html version ersetzen
                if (typeof(this.getMovieElement().CallFunction) == "undefined") {
                    this.customSettings.component.fireEvent('flashLoadError');
                    return;
                }
                this.customSettings.component.useSwf = true;
            }
        });
    }
});
