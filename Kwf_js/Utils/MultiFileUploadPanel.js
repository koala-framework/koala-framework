Ext2.namespace('Kwf.Utils');
Kwf.Utils.MultiFileUploadPanel = Ext2.extend(Ext2.Panel,
{
    fileSizeLimit: null,
    allowOnlyImages: false,
    maxResolution: 0,
    //baseParams: {},
    controllerUrl: '',
    maxNumberOfFiles: null,
    maxEntriesErrorMessage: '',

    _maxEntriesAlertVisible: false,
    
    initComponent: function() {
        if (!this.baseParams) this.baseParams = {};
        Kwf.Utils.MultiFileUploadPanel.superclass.initComponent.call(this);
    },
    afterRender: function() {
        Kwf.Utils.MultiFileUploadPanel.superclass.afterRender.call(this);

        var container = this.body.createChild();


        container.setStyle('position', 'relative');
        this.uploadButton = new Ext2.Button({
            text: trlKwf('Upload Files'),
            cls: 'x2-btn-text-icon',
            icon: '/assets/silkicons/add.png',
            renderTo: container
        });

        var fileInputContainer = container.createChild({
            style: 'width: 120px; height: 20px; top: 0; position: absolute; overflow: hidden;'
        });

        var fileInput = fileInputContainer.createChild({
            tag: 'input',
            type: 'file',
            multiple: 'multiple',
            style: 'opacity: 0; cursor: pointer; '
        });
        fileInput.on('change', function(ev, dom) {
            if (dom.files) {
                this.html5UploadFiles(dom.files);
                dom.value = ''; //leeren, damit gleiche datei nochmal gewählt werden kann
            }
        }, this);

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
                this.html5UploadFiles(e.browserEvent.dataTransfer.files);
            }
        }, this);
    },
    html5UploadFiles: function(files)
    {
        if (!files.length) return;

        if (this.maxNumberOfFiles!==null && files.length > this.maxNumberOfFiles) {
            Ext2.Msg.alert(trlKwf('Error'), this.maxEntriesErrorMessage);
            return;
        }

        this.progress = Ext2.MessageBox.show({
            title : trlKwf('Upload'),
            msg : trlKwf('Uploading files'),
            buttons: false,
            progress:true,
            closable:false,
            minWidth: 250,
            buttons: Ext2.MessageBox.CANCEL,
            scope: this,
            fn: function(button) {
                this.fileQueue = [];
                this.currentXhr.abort();
            }
        });

        this.processedFiles = [];
        this.fileQueue = [];
        this.uploadedIds = [];
        for(var i=0;i<files.length;++i) {
            this.fileQueue.push(files[i]);
        }
        this.html5UploadFile();
    },

    updateProgress: function(e)
    {
        var total = 0;
        if (e && e.total) total += e.total;
        var loaded = 0;
        if (e && e.loaded) loaded += e.loaded;
        this.processedFiles.forEach(function(f) {
            total += f.size;
            loaded += f.size;
        }, this);
        this.fileQueue.forEach(function(f) {
            total += f.size;
        }, this);
        this.progress.updateProgress(loaded / total);
    },

    html5UploadFile: function()
    {
        var file = this.fileQueue.shift();
        this.currentXhr = Kwf.Utils.Upload.uploadFile({
            maxResolution: this.maxResolution,
            file: file,
            success: function(r, options) {
                this.processedFiles.push(options.file);
                this.uploadedIds.push(r.value.uploadId);
                if (this.fileQueue.length) {
                    this.updateProgress();
                    this.html5UploadFile(); //neext
                } else {
                    var params = Ext2.apply(this.baseParams, { uploadIds: this.uploadedIds.join(',')});
                    Ext2.Ajax.request({
                        url: this.controllerUrl+'/json-multi-upload',
                        params: params,
                        success: function() {
                            this.progress.hide();
                            this.fireEvent('uploaded');
                        },
                        scope: this
                    });
                }
            },
            failure: function() {
                this.progress.hide();
            },
            progress: function(e) {
                this.updateProgress(e);
            },
            scope: this
        });
    },

    applyBaseParams: function(baseParams)
    {
        Ext2.apply(this.baseParams, baseParams);
    },
    setBaseParams: function(baseParams)
    {
        this.baseParams = Kwf.clone(baseParams);
    }
});
