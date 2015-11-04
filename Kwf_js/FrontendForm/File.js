// @require ModernizrNetworkXhr2

Kwf.FrontendForm.File = Ext2.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        if (!Modernizr.xhr2) {
            return;
        }

        this.el.addClass('dropField');
        this.dropContainer = $(this.el.dom);
        this.fileInput = $(this.el.dom).find('input[type="file"]');
        this.uploadIdField = this.dropContainer.find('input.kwfUploadIdField');
        this.fileSizeLimit = this.fileInput.data('fileSizeLimit');

        // Prevent Event-Bubbling
        this.dropContainer.on('dragenter', function(e) {
            e.stopPropagation();
            e.preventDefault();
        });

        this.dropContainer.on('dragover', function(e) {
            e.stopPropagation();
            e.preventDefault();
        });

        this.dropContainer.get(0).addEventListener('drop', this.onDrop.bind(this));
        this.fileInput.get(0).addEventListener('change', this.onDrop.bind(this));

    },
    onDrop: function(e) {
        e.stopPropagation();
        e.preventDefault();

        this.form.disableSubmit();

        var files = e.dataTransfer ? e.dataTransfer.files : e.target.files;

        if (!files.length) return;

        var file = files[0];

        if (file.size > this.fileSizeLimit) {
            return alert(trl('Allowed upload size exceeded max. allowed upload size {0} MB', this.fileSizeLimit/1048576));
        }

        var progressbar = $(
            '<div class="kwfFormFieldUploadProgressBar">' +
                '<div class="inner">' +
                    '<span class="progress"></span>' +
                    '<span class="processing">'+trlKwf("Processing")+'...</span>' +
                '</div>' +
            '</div>');

        this.dropContainer.prepend(progressbar);

        var uploadIdField = this.uploadIdField;

        var xhr = new XMLHttpRequest();
        var url = '/kwf/media/upload/json-upload';
        if (Kwf.sessionToken) url += '?kwfSessionToken='+Kwf.sessionToken;
        xhr.open('POST', url);
        xhr.setRequestHeader('X-Upload-Name', encodeURIComponent(file.name));
        xhr.setRequestHeader('X-Upload-Size', file.size);
        xhr.setRequestHeader('X-Upload-Type', file.type);
        xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');


        xhr.upload.onprogress = (function(data) {
            if (data.lengthComputable) {
                var progress = (data.loaded / data.total) * 100;
                if (progress < 100) {
                    progressbar.find('span.progress').css('width', progress+'%');
                } else {
                    progressbar.find('span.progress').css('width', '100%');
                    progressbar.find('span.progress').hide();
                    progressbar.find('span.processing').addClass('visible');
                }
            }
        }).bind(this);

        xhr.send(file);

        xhr.onreadystatechange = (function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                this.form.enableSubmit();

                progressbar.fadeOut(function() {
                    $(this).remove();
                });

                var response;
                try {
                    response = JSON.parse(xhr.response);
                } catch (e) {
                    return alert(trl('An error occured, please try again later'));
                }
                this.dropContainer.find('input.kwfFormFieldFileUnderlayText').val(response.value.filename);
                uploadIdField.val(response.value.uploadId+'_'+response.value.hashKey);
                this.dropContainer.find('input.fileSelector').val('');

            } else if (xhr.readyState == 4 && xhr.status !== 200) {
                this.form.enableSubmit();
                return alert(trl('An error occured, please try again later'));
            }
        }).bind(this);
    },
    getFieldName: function() {
        var inp = this.el.child('input.fileSelector');
        if (!inp) return null;
        return inp.dom.name;
    },
    getValue: function() {
        var inp = this.el.child('input[type="hidden"]');
        if (!inp) return null;
        var ret = inp.dom.value;
        return ret;
    },
    clearValue: function() {
        var inp = this.el.child('input[type="hidden"]');
        inp.dom.value = '';
    },
    setValue: function(value) {
        var inp = this.el.child('input[type="hidden"]');
        inp.dom.value = value;
    }
});

Kwf.FrontendForm.fields['kwfFormFieldFile'] = Kwf.FrontendForm.File;
