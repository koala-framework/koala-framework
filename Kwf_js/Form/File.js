Kwf.Form.File = Ext2.extend(Ext2.form.Field, {
    allowOnlyImages: false,
    fileSizeLimit: 0,
    showPreview: true,
    previewUrl: '/kwf/media/upload/preview?',
    previewWidth: 40,
    previewHeight: 40,
    showDeleteButton: true,
    infoPosition: 'south',
    imageData: null,
    defaultAutoCreate : {
        tag: 'div',
        cls: 'swfUploadField',
        style: 'width: 336px; height: 53px',
        html: '<div class="hint">'+trlKwf('or drag here')+'</div>'
    },
    fileIcons: {
        'application/pdf': 'page_white_acrobat',
        'application/x-zip': 'page_white_compressed',
        'application/msexcel': 'page_white_excel',
        'application/msword': 'page_white_word',
        'application/mspowerpoint': 'page_white_powerpoint',
        'default': 'page_white'
    },
    previewTpl: ['<div class="hover-background"></div><a href="{href:htmlEncode}" target="_blank" class="previewImage" ',
                 'style="width: {previewWidth}px; height: {previewHeight}px; display: block; background-repeat: no-repeat; background-position: center; background-image: url({preview});"></a>'],
    // also usable in infoTpl: {href} {filename}.{extension}
    infoTpl: ['<div class="filedescription">',
              '<div class="filesize"><tpl if="image">{imageWidth:htmlEncode}x{imageHeight:htmlEncode}px, </tpl>',
              '{fileSize:fileSize}</div></div>'],
    emptyTpl: ['<div class="empty" style="height: {previewHeight}px; width: {previewWidth}px; text-align: center;line-height:{previewHeight}px">('+trlKwf('empty')+')</div>'],

    initComponent: function() {
        this.addEvents(['uploaded']);

        if (this.showPreview) {
            if (!(this.previewTpl instanceof Ext2.XTemplate)) {
                this.previewTpl = new Ext2.XTemplate(this.previewTpl);
            }
            this.previewTpl.compile();

            if (!(this.emptyTpl instanceof Ext2.XTemplate)) {
                this.emptyTpl = new Ext2.XTemplate(this.emptyTpl);
            }
            this.emptyTpl.compile();
        }

        if (!(this.infoTpl instanceof Ext2.XTemplate)) {
            this.infoTpl = new Ext2.XTemplate(this.infoTpl);
        }
        this.infoTpl.compile();

        Kwf.Form.File.superclass.initComponent.call(this);

    },
    afterRender: function() {
        if (Kwf.Utils.Upload.supportsHtml5Upload()) {
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
            this.previewImageBox = this.el.createChild({
                cls: 'box'
            });

            this.emptyTpl.overwrite(this.previewImageBox, { //bild wird in der richtigen größe angezeigt
                previewWidth: this.previewWidth,
                previewHeight: this.previewHeight
            });
        }

        if (this.infoPosition == 'west') this.createInfoContainer();

        this.uploadButtonContainer = this.el.createChild({
            cls: 'uploadButton'
        });

        this.createUploadButton();
        if (this.showDeleteButton) {
            this.deleteButton = new Ext2.Button({
                text: trlKwf('Delete File'),
                cls: 'x2-btn-text-icon',
                icon: '/assets/silkicons/delete.png',
                renderTo: this.el.createChild({cls: 'deleteButton'}),
                scope: this,
                handler: function() {
                    this.setValue('');
                }
            });
        }
        Kwf.Form.File.superclass.afterRender.call(this);

        if (this.infoPosition == 'south') this.createInfoContainer();
    },

    alignHelpAndComment: function() {
        if (this.helpEl) {
            this.helpEl.anchorTo(this.deleteButton.el, 'r', [10, -8]);
        }
    },

    createInfoContainer: function() {
        this.infoContainer = this.el.createChild({
            style: 'margin-top: 3px;'+((this.infoPosition == 'west') ? ' float: left; margin-right: 3px;' : '')
        });
    },

    createUploadButton : function () {
        while (this.uploadButtonContainer.last()) {
            this.uploadButtonContainer.last().remove();
        }
        this.uploadButton = new Ext2.Button({
            text: trlKwf('Upload File'),
            cls: 'x2-btn-text-icon',
            icon: '/assets/silkicons/add.png',
            renderTo: this.uploadButtonContainer,
            scope: this,
            handler: function() {
                var win = new Kwf.Form.FileUploadWindow({
                    maxResolution: this.maxResolution
                });
                win.on('uploaded', function(win, result) {
                    this.setValue(result.value);
                    this.fireEvent('uploaded', this, result.value);
                }, this);
                win.show();
            }
        });

        if (!Kwf.Utils.Upload.supportsHtml5Upload()) return;

        var w = this.uploadButton.el.getWidth();
        var h = this.uploadButton.el.getHeight();
        if (!w) w = 131; //might be 0 if element is hidden
        if (!h) h = 21;
        var fileInputContainer = this.uploadButtonContainer.createChild({
            style: 'width: '+w+'px; height: '+h+'px; top: 0; position: absolute; overflow: hidden;'
        });

        var accept = '*';
        if (this.allowOnlyImages) {
            accept = 'image/png,image/jpg,image/jpeg,image/gif';
        }
        var fileInput = fileInputContainer.createChild({
            tag: 'input',
            type: 'file',
            style: 'opacity: 0; cursor: pointer; ',
            accept: accept
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
        this.progress = Ext2.MessageBox.show({
            title : trlKwf('Upload'),
            msg : trlKwf('Uploading file'),
            buttons: false,
            progress:true,
            closable:false,
            minWidth: 250,
            buttons: Ext2.MessageBox.CANCEL,
            scope: this,
            fn: function(button) {
                xhr.abort();
            }
        });

        var xhr = Kwf.Utils.Upload.uploadFile({
            maxResolution: this.maxResolution,
            file: file,
            success: function(r) {
                this.progress.hide();
                this.setValue(r.value);
                this.fireEvent('uploaded', this, r.value);
            },
            failure: function() {
                this.progress.hide();
            },
            progress: function(e) {
                this.progress.updateProgress(e.loaded / e.total);
            },
            scope: this
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
        if (v) {
            this.addClass('file-uploaded');
        } else {
            this.removeClass('file-uploaded');
        }
        if (v != this.value) {
            this.imageData = value;
            var icon = false;
            var href = '/kwf/media/upload/download?uploadId='+value.uploadId+'&hashKey='+value.hashKey;
            if (value.mimeType) {
                if (this.showPreview) {
                    if (value.mimeType.match(/(^image\/)/)) {
                        icon = this._generatePreviewUrl(this.previewUrl);
                    } else {
                        icon = this.fileIcons[value.mimeType] || this.fileIcons['default'];
                        icon = '/assets/silkicons/' + icon + '.png';
                    }
                    this.previewTpl.overwrite(this.previewImageBox, {
                        preview: icon,
                        href: href,
                        previewWidth: this.previewWidth,
                        previewHeight: this.previewHeight
                    });
                }

                var infoVars = Kwf.clone(value);
                infoVars.href = href;
                this.infoContainer.addClass('info-container');
                this.infoTpl.overwrite(this.infoContainer, infoVars);
            } else {
                if (this.showPreview) {
                    this.emptyTpl.overwrite(this.previewImageBox, {
                        previewWidth: this.previewWidth,
                        previewHeight: this.previewHeight
                    });
                }
                this.infoContainer.update('');
            }
            this.fireEvent('change', this, value, this.value);
        }
        Kwf.Form.File.superclass.setValue.call(this, value.uploadId);
    },

    _generatePreviewUrl: function (previewUrl) {
        return previewUrl+'uploadId='+this.imageData.uploadId
        +'&hashKey='+this.imageData.hashKey;
    },

    setPreviewUrl: function (previewUrl) {
        this.previewUrl = previewUrl;
        if (!previewUrl) {
            this.getEl().child('.box').setStyle('background-image', 'none');
            return;
        }
        if (this.getEl().child('.previewImage') && this.imageData) {
            this.getEl().addClass('loading');
            var img = new Image();
            img.onload = (function () {
                this.getEl().removeClass('loading');
            }).createDelegate(this);
            img.src = this._generatePreviewUrl(previewUrl);
            this.getEl().child('.previewImage')
                .setStyle('background-image', 'url('+this._generatePreviewUrl(previewUrl)+')');
        }
    },

    validateValue : function(value){
        if(!value){ // if it's blank
             if(this.allowBlank){
                 this.clearInvalid();
                 return true;
             }else{
                 this.markInvalid(trlKwf('This field is required'));
                 return false;
             }
        }
        return true;
    }

});



Ext2.reg('kwf.file', Kwf.Form.File);




