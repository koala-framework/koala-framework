YAHOO.E3.Component.Pic = function(componentId, componentClass) {
    YAHOO.E3.Component.Pic.superclass.constructor.call(this, componentId, componentClass);
};
YAHOO.lang.extend(YAHOO.E3.Component.Pic, YAHOO.E3.Component.Abstract);

YAHOO.E3.Component.Pic.prototype.handleSuccess = function(o) {
    this.progressBar = null;
    this.progressPercent = null;
    this.progressText = null;
    YAHOO.E3.Component.Pic.superclass.handleSuccess.call(this, o);
};
YAHOO.E3.Component.Pic.prototype.handleSave = function() {
    var form = this.htmlelement.getElementsByTagName('form')[0];
    YAHOO.util.Connect.setForm(form, true);
    YAHOO.util.Connect.asyncRequest('POST', '/ajax/fe/save?componentId='+this.componentId+'&componentClass='+this.componentClass+'&currentPageId='+currentPageId, 
        {success: this.handleSuccess, failure: this.handleFailure, upload: this.handleSuccess, scope: this});
    this.progressKey = form.UPLOAD_IDENTIFIER.value;

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

YAHOO.E3.Component.Pic.prototype.updateProgress = function() {
  YAHOO.util.Connect.asyncRequest('POST','/ajax/fe/status?componentId='+this.componentId+'&componentClass='+this.componentClass+'&currentPageId='+currentPageId,
    {success: this.progressHandler, scope: this},
    'progress_upload='+this.progressKey);
};

YAHOO.E3.Component.Pic.prototype.progressHandler = function(o) {
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
