/*
Vps.Component.Pic = function(componentId, componentClass, pageId) {
    Vps.Component.Pic.superclass.constructor.call(this, componentId, componentClass, pageId);
};
YAHOO.lang.extend(Vps.Component.Pic, Vps.Component.Abstract);

Vps.Component.Pic.prototype.handleSuccess = function(o) {
    this.progressBar = null;
    this.progressPercent = null;
    this.progressText = null;
    var resp = eval('(' + o.responseText + ')');
    debugger;
    if(resp.html) this.htmlelement.innerHTML = resp.html;
    if (resp.createComponents) {
        for(var id in resp.createComponents) {
            YAHOO.Vps.createComponent(id, resp.createComponents[id]);
        }
    }
};
*/
/*
Vps.Component.Abstract.prototype.handleSave = function() {
    YAHOO.util.Connect.setForm(this.form.el, true);
    //YAHOO.util.Connect.asyncRequest('POST', '/ajax/fe/save?componentId='+this.componentId+'&componentClass='+this.componentClass+'&currentPageId='+currentPageId, 
    //    {success: this.handleSuccess, failure: this.handleFailure, upload: this.handleSuccess, scope: this});
};
*/
/*
Vps.Component.Pic.prototype.handleEdit = function() {
    form = this.form;
    form.baseParams.UPLOAD_IDENTIFIER = Math.round(Math.random() * 1000000);
    form.baseParams.MAX_FILE_SIZE = 64388608;
    form.add(
        new Ext.form.Field({
            name: 'upload',
            inputType: 'file',
            allowBlank:false
        })
    );
    
    form.addButton('Save', this.handleSave, this);
    form.addButton('Cancel', this.handleCancel, this);
};

Vps.Component.Pic.prototype.handleSave = function() {
    this.htmlelement.isInEditMode = false;
    //debugger;
    YAHOO.util.Connect.setForm(this.form.el.dom, true);
    YAHOO.util.Connect.asyncRequest('POST', '/ajax/fe/save?componentId='+this.componentId+'&componentClass='+this.componentClass+'&currentPageId='+currentPageId, 
        {success: this.handleSuccess, failure: this.handleFailure, upload: this.handleSuccess, scope: this});
}
/*
Vps.Component.Pic.prototype.handleSave = function() {
    this.progressKey = this.form.baseParams.UPLOAD_IDENTIFIER.value;
    this.submitForm('save');

    var progressDiv = document.createElement('div');
    progressDiv.style.height = '1em';
    progressDiv.style.width = '400px';
    progressDiv.style.border = '1px solid black';
    this.htmlelement.innerHTML = '';
    this.htmlelement.appendChild(progressDiv);
    
    this.progressBar = document.createElement('div');
    this.progressBar.style.backgroundColor = '#99e';
    this.progressBar.style.height = '98%';
    this.progressBar.style.width = '0%';
    this.progressBar.style.float = 'left';
    progressDiv.appendChild(this.progressBar);
    
    this.progressPercent = document.createElement('div');
    this.progressPercent.style.height = '90%';
    this.progressPercent.style.position = 'absolute';
    this.progressPercent.style.margin = '1px 0 0 185px';
    this.progressPercent.innerHTML = '0%'
    this.progressBar.appendChild(this.progressPercent);
    
    this.progressText = document.createElement('div');
    this.progressText.style.margin = '3px 0 0 5px';
    this.progressText.innerHTML = 'Connecting...';
    this.htmlelement.appendChild(this.progressText);
    
    this.updateProgress();

    this.htmlelement.isInEditMode = false;
};

Vps.Component.Pic.prototype.updateProgress = function() {
  YAHOO.util.Connect.asyncRequest('POST','/ajax/fe/status?componentId='+this.componentId+'&componentClass='+this.componentClass+'&currentPageId='+this.pageId,
    {success: this.progressHandler, scope: this},
    'progress_upload='+this.progressKey);
};

Vps.Component.Pic.prototype.progressHandler = function(o) {
  var resp = eval('(' + o.responseText + ')');
  if(resp.progress_upload) resp = resp.progress_upload;
  
  if(resp['percent'] && this.progressBar) {
    this.progressBar.style.width = resp.percent+'%';
    this.progressPercent.innerHTML = resp.percent+'%';
    this.progressText.innerHTML = resp.message;
    var scope = this;
    setTimeout(function() { scope.updateProgress(); }, 500);
  }
};
*/