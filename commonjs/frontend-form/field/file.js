var $ = require('jquery');
var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var Field = require('kwf/commonjs/frontend-form/field/field');
var kwfExtend = require('kwf/commonjs/extend');
var t = require('kwf/commonjs/trl');
require('kwf/commonjs/frontend-form/field/file.scss');

var File = kwfExtend(Field, {
    initField: function() {
        this.el.addClass('kwfUp-dropField');
        this.dropContainer = this.el;
        this.fileInput = this.el.find('input[type="file"]');
        this.uploadIdField = this.dropContainer.find('input.kwfUp-kwfUploadIdField');
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

        var files = e.dataTransfer ? e.dataTransfer.files : e.target.files;
        if (!files.length) return;

        this.form.disableSubmit();

        var file = files[0];

        if (file.size > this.fileSizeLimit) {
            return alert(__trlKwf('Allowed upload size exceeded max. allowed upload size {0} MB', this.fileSizeLimit/1048576));
        }

        var progressbar = $(
            '<div class="kwfUp-kwfFormFieldUploadProgressBar">' +
                '<div class="kwfUp-inner">' +
                    '<span class="kwfUp-progress"></span>' +
                    '<span class="kwfUp-processing">'+__trlKwf("Processing")+'...</span>' +
                '</div>' +
            '</div>');

        this.dropContainer.prepend(progressbar);

        var uploadIdField = this.uploadIdField;

        var xhr = new XMLHttpRequest();
        var url = '/kwf/media/upload/json-upload';
        xhr.open('POST', url);
        xhr.setRequestHeader('X-Requested-With', "XMLHttpRequest");
        xhr.setRequestHeader('X-Upload-Name', encodeURIComponent(file.name));
        xhr.setRequestHeader('X-Upload-Size', file.size);
        xhr.setRequestHeader('X-Upload-Type', file.type);
        var ua = navigator.userAgent.toLowerCase();
        if (!ua.match(/trident/) && !ua.match(/edge/)) {
            xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
        }


        xhr.upload.onprogress = (function(data) {
            if (data.lengthComputable) {
                var progress = (data.loaded / data.total) * 100;
                if (progress < 100) {
                    progressbar.find('span.kwfUp-progress').css('width', progress+'%');
                } else {
                    progressbar.find('span.kwfUp-progress').css('width', '100%');
                    progressbar.find('span.kwfUp-progress').hide();
                    progressbar.find('span.kwfUp-processing').addClass('visible');
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
                    response = JSON.parse(xhr.responseText);
                } catch (e) {
                    return alert(__trlKwf('An error occured, please try again later'));
                }
                this.dropContainer.find('input.kwfUp-fileSelector').val('');
                uploadIdField.val(response.value.uploadId+'_'+response.value.hashKey);
                this.dropContainer.find('input.kwfUp-kwfFormFieldFileUnderlayText').val(response.value.filename);

            } else if (xhr.readyState == 4 && xhr.status !== 200) {
                this.form.enableSubmit();
                return alert(__trlKwf('An error occured, please try again later'));
            }
        }).bind(this);
    },
    getFieldName: function() {
        var inp = this.el.find('input.kwfUp-fileSelector');
        if (!inp.length) return null;
        return inp.get(0).name;
    },
    getValue: function() {
        var inp = this.el.find('input[type="hidden"]');
        if (!inp.length) return null;
        var ret = inp.get(0).value;
        return ret;
    },
    clearValue: function() {
        var inp = this.el.find('input[type="hidden"]');
        inp.get(0).value = '';
    },
    setValue: function(value) {
        var inp = this.el.find('input[type="hidden"]');
        inp.get(0).value = value;
    }
});

fieldRegistry.register('kwfFormFieldFile', File);
module.exports = File;
