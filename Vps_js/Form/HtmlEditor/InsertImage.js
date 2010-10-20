Vps.Form.HtmlEditor.InsertImage = function(config) {
    Ext.apply(this, config);

    var panel = Ext.ComponentMgr.create(Ext.applyIf(this.componentConfig, {
        baseCls: 'x-plain',
        formConfig: {
            tbar: false
        },
        autoLoad: false
    }));
    this.imageDialog = new Vps.Auto.Form.Window({
        autoForm: panel,
        width: 450,
        height: 400
    });

};
Ext.extend(Vps.Form.HtmlEditor.InsertImage, Ext.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
    },

    // private
    afterCreateToolbar: function() {
        var tb = this.cmp.getToolbar();
        tb.insert(7, {
            icon: '/assets/silkicons/picture.png',
            handler: this.onInsertImage,
            scope: this,
            tooltip: {
                cls: 'x-html-editor-tip',
                title: trlVps('Image'),
                text: trlVps('Insert new image or edit selected image.')
            },
            cls: 'x-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
    },

    onInsertImage: function() {
        var img = this.cmp.getFocusElement('img');
        if (img) {
            this._currentImage = img;
            var expr = new RegExp('/media/[^/]+/'+this.cmp.componentId+'-i([0-9]+)/');
            var m = img.src.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.imageDialog.un('datachange', this._insertImage, this);
                this.imageDialog.un('datachange', this._modifyImage, this);
                this.imageDialog.showEdit({
                    componentId: this.cmp.componentId+'-i'+nr
                });
                this.imageDialog.on('datachange', this._modifyImage, this);
                return;
            }
        }
        Ext.Ajax.request({
            params: {componentId: this.cmp.componentId},
            url: this.cmp.controllerUrl+'/json-add-image',
            success: function(response, options, r) {
                this.imageDialog.un('datachange', this._insertImage, this);
                this.imageDialog.un('datachange', this._modifyImage, this);
                this.imageDialog.showEdit({
                    componentId: r.componentId
                });
                this.imageDialog.on('datachange', this._insertImage, this);
            },
            scope: this
        });
    },
    _insertImage: function(r) {
        var html = '<img src="'+r.imageUrl+'?'+Math.random()+'" ';
        html += 'width="'+r.imageDimension.width+'" ';
        html += 'height="'+r.imageDimension.height+'" />';
        this.cmp.insertAtCursor(html);
    },
    _modifyImage: function(r) {
        this._currentImage.src = r.imageUrl+'?'+Math.random();
        this._currentImage.width = r.imageDimension.width;
        this._currentImage.height = r.imageDimension.height;
    }
});