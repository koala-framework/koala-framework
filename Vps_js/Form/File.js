Vps.Form.File = Ext.extend(Ext.form.Field, {
    allowOnlyImages: false,
    fileSizeLimit: 0,
    showPreview: true,
    showDeleteButton: true,
    infoPosition: 'south',
    html5Upload: false,
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
    // also usable in infoTpl: {href}
    infoTpl: ['{filename}.{extension}<br />',
              '{fileSize:fileSize}',
              '<tpl if="image">, {imageWidth}x{imageHeight}px</tpl>'],
    emptyTpl: '<div style="height: 40px; width: 40px; text-align: center;"><br />('+trlVps('empty')+')</div>',

    initComponent: function() {
        this.addEvents(['uploaded']);

        if (this.showPreview) {
            if (!(this.previewTpl instanceof Ext.XTemplate)) {
                this.previewTpl = new Ext.XTemplate(this.previewTpl);
            }
            this.previewTpl.compile();
        }

        if (!(this.infoTpl instanceof Ext.XTemplate)) {
            this.infoTpl = new Ext.XTemplate(this.infoTpl);
        }
        this.infoTpl.compile();

        if (XMLHttpRequest) {
            var xhr = new XMLHttpRequest();
            if (xhr.upload) {
                this.html5Upload = true;
            }
        }

        Vps.Form.File.superclass.initComponent.call(this);

    },
    afterRender: function() {
        Vps.Form.File.superclass.afterRender.call(this);

        if (this.html5Upload) {
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
                    var dt = e.browserEvent.dataTransfer;
                    var files = dt.files;
                    if (files.length) {
                        this.html5UploadFile(files[0]);
                    }
                }
            }, this);
        }

        if (this.showPreview) {
            this.previewImage = this.el.createChild({
                style: 'margin-right: 10px; padding: 5px; float: left; border: 1px solid #b5b8c8;'+
                    'background-color: white;'
            });

            this.previewImage.update(this.emptyTpl); //bild wird in der richtigen größe angezeigt
        }

        if (this.infoPosition == 'west') this.createInfoContainer();

        this.uploadButtonContainer = this.el.createChild({
                                         style: 'float:left; margin-right: 5px; position: relative; '
                                     });

        this.createUploadButton();
        if (this.showDeleteButton) {
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
        }
        if (this.infoPosition == 'south') this.createInfoContainer();

        if (!this.html5Upload) {
            this.swfu = new Vps.Utils.SwfUpload({
                fileSizeLimit: this.fileSizeLimit,
                allowOnlyImages: this.allowOnlyImages,
                buttonPlaceholderId: this.uploadButtonContainer.id,
                postParams: {
                    maxResolution: this.maxResolution
                }
            });
            this.swfu.on('fileQueued', function(file) {
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
                        this.swfu.cancelUpload(file.id);
                    }
                });
                this.swfu.startUpload(file.id);
            }, this);
            this.swfu.on('uploadProgress', function(file, done, total) {
                this.progress.updateProgress(done/total);
            }, this);
            this.swfu.on('uploadSuccess', function(file, response) {
                this.progress.hide();
                this.setValue(response.value);
                this.fireEvent('uploaded', this, response.value);
            }, this);
            this.swfu.on('uploadError', function(file, errorCode, errorMessage) {
                this.progress.hide();
            }, this);
            this.swfu.on('flashLoadError', function() {
                this.createUploadButton();
            }, this);
        }
    },

    onDestroy: function() {
        if (this.swfu) this.swfu.destroy();
    },

    createInfoContainer: function() {
        this.infoContainer = this.el.createChild({
            style: 'margin-top: 3px;'+((this.infoPosition == 'west') ? ' float: left; margin-right: 3px;' : '')
        });
    },

    createUploadButton : function () {
        if (this.swfu) this.swfu.destroy();
        this.swfu = null;
        while (this.uploadButtonContainer.last()) {
            this.uploadButtonContainer.last().remove();
        }
        this.uploadButton = new Ext.Button({
            text: trlVps('Upload File'),
            cls: 'x-btn-text-icon',
            icon: '/assets/silkicons/add.png',
            renderTo: this.uploadButtonContainer,
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

        if (!this.html5Upload) return;

        var fileInputContainer = this.uploadButtonContainer.createChild({
            style: 'width: '+this.uploadButton.el.getWidth()+'px; height: '+this.uploadButton.el.getHeight()+'px; top: 0; position: absolute; overflow: hidden;'
        });

        var fileInput = fileInputContainer.createChild({
            tag: 'input',
            type: 'file',
            style: 'opacity: 0; cursor: pointer; '
        });
        fileInput.on('change', function(ev, dom) {
            if (dom.files) {
                if (dom.files.length) {
                    var file = dom.files[0];
                    dom.value = ''; //leeren, damit gleiche datei nochmal gewählt werden kann
                    this.html5UploadFile(file);
                }
            }
        }, this);
    },

    html5UploadFile: function(file)
    {
        var self = this;

        var xhr = new XMLHttpRequest();

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
                xhr.abort();
            }
        });

        xhr.upload.addEventListener("progress", function(e) {
            if (e.lengthComputable) {
                self.progress.updateProgress(e.loaded / e.total);
            }
        }, false);
        xhr.onreadystatechange = function(e) {
            if (xhr.readyState == 4) {
                self.progress.hide();
                var errorMsg = false;
                if (xhr.status != 200) {
                        errorMsg = xhr.responseText;
                } else if (!xhr.responseText) {
                    errorMsg = 'response is empty';
                } else {
                    try {
                        var r = Ext.decode(xhr.responseText);
                    } catch(e) {
                        errorMsg = e.toString()+': <br />'+xhr.responseText;
                    }
                    if (!errorMsg && r.exception) {
                        errorMsg = '<pre>'+r.exception+'</pre>';
                    }
                }
                if (errorMsg) {
                    var sendMail = !r || !r.exception;
                    Vps.handleError({
                        url: '/vps/media/upload/json-upload',
                        message: errorMsg,
                        title: trlVps('Upload Error'),
                        mail: sendMail,
                        checkRetry: false
                    });
                    return;
                }

                if (!r.success) {
                    if (r.error) {
                        Ext.Msg.alert(trlVps('Error'), r.error);
                    } else {
                        Ext.Msg.alert(trlVps('Error'), trlVps("A Server failure occured."));
                    }
                    return;
                }

                self.setValue(r.value);
                self.fireEvent('uploaded', this, r.value);
            }
        };
        xhr.open('POST', '/vps/media/upload/json-upload');
        xhr.setRequestHeader('X-Upload-Name', file.name);
        xhr.setRequestHeader('X-Upload-Size', file.size);
        xhr.setRequestHeader('X-Upload-Type', file.type);
        xhr.setRequestHeader('X-Upload-MaxResolution', this.maxResolution);
        xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
        xhr.send(file);
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
            var href = '/vps/media/upload/download?uploadId='+value.uploadId+'&hashKey='+value.hashKey;
            if (value.mimeType) {
                if (this.showPreview) {
                    if (value.mimeType.match(/(^image\/)/)) {
                        icon = '/vps/media/upload/preview?uploadId='+value.uploadId+'&hashKey='+value.hashKey;
                    } else {
                        icon = this.fileIcons[value.mimeType] || this.fileIcons['default'];
                        icon = '/assets/silkicons/' + icon + '.png';
                    }
                    this.previewTpl.overwrite(this.previewImage, {
                        preview: icon,
                        href: href
                    });
                }

                var infoVars = Vps.clone(value);
                infoVars.href = href;
                this.infoTpl.overwrite(this.infoContainer, infoVars);
            } else {
                if (this.showPreview) {
                    this.previewImage.update(this.emptyTpl);
                }
                this.infoContainer.update('');
            }
        }
        Vps.Form.File.superclass.setValue.call(this, value.uploadId);
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



Ext.reg('vps.file', Vps.Form.File);




