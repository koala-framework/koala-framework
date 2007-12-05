Vps.Form.FileUploadField = Ext.extend(Ext.form.TextField,
{
    inputType: 'file',
    setValue: function(v) {
        if (this.rendered) {
            //input neu rendern, damit eventuell ausgewählte datei aus input wird
            Ext.destroy(this.el);
            var cfg = this.getAutoCreate();
            if(!cfg.name){
                cfg.name = this.name || this.id;
            }
            if(this.inputType){
                cfg.type = this.inputType;
            }
            this.el = this.outerEl.createChild(cfg);
        }
    },
    onRender : function(ct, position){
        this.outerEl = ct.createChild({}, position);
        Vps.Form.FileUploadField.superclass.onRender.call(this, this.outerEl);
    }

});
Ext.reg('fileuploadfield', Vps.Form.FileUploadField);
