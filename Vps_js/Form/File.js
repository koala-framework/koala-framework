Vps.Form.File = Ext.extend(Ext.form.Field, {
    allowOnlyImages: false,
    fileSizeLimit: 0,
    showPreview: true,
    showDeleteButton: true,
    infoPosition: 'south',
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

        Vps.Form.File.superclass.initComponent.call(this);

    },
    afterRender: function() {
        Vps.Form.File.superclass.afterRender.call(this);

        if (this.showPreview) {
            this.previewImage = this.el.createChild({
                style: 'margin-right: 10px; padding: 5px; float: left; border: 1px solid #b5b8c8;'+
                    'background-color: white;'
            });

            this.previewImage.update(this.emptyTpl); //bild wird in der richtigen größe angezeigt
        }

        if (this.infoPosition == 'west') this.createInfoContainer();

        this.uploadButtonContainer = this.el.createChild({
                                         style: 'float:left; margin-right: 5px; '
                                     });

        this.uploadButtonContainerChildId = 'uploadButton'+Ext.id();
        this.createUploadButton(false);
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

        this.swfu = new Vps.Utils.SwfUpload({
            fileSizeLimit: this.fileSizeLimit,
            allowOnlyImages: this.allowOnlyImages,
            buttonPlaceholderId: this.uploadButtonContainerChildId,
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
            this.createUploadButton(true);
        }, this);
    },

    onDestroy: function() {
        this.swfu.destroy();
    },

    createInfoContainer: function() {
        this.infoContainer = this.el.createChild({
            style: 'margin-top: 3px;'+((this.infoPosition == 'west') ? ' float: left; margin-right: 3px;' : '')
        });
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




