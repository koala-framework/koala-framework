YAHOO.namespace('YAHOO.E3.Component');
var E3 = YAHOO.E3;

YAHOO.E3.createComponent = function(componentId, componentClass)
{
    if (!this.loadedFiles) this.loadedFiles = {};
    var componentData = {componentId: componentId, componentClass: componentClass};
    if (!this.loadedFiles[componentClass]) {
        this.loadedFiles[componentClass] = { 'finished': false, 'data': [componentData] }
        var file = '/files/'+componentClass.replace(/_/g, '/')+'.js';
        YAHOO.util.Connect.asyncRequest('GET', file,
                { failure: function() { /*alert('error loading '+file);*/ }, scope: this,
                  success: function(o) {
                    eval(o.responseText);
                    this.loadedFiles[componentClass].finished = true;
                    for(var i=0; i<this.loadedFiles[componentClass].data.length; i++) {
                        this.createComponentCallback(this.loadedFiles[componentClass].data[i]);
                    }
                  }
                });
    } else if (!this.loadedFiles[componentClass].finished) {
        this.loadedFiles[componentClass].data.push(componentData);
    } else {
        this.createComponentCallback(componentData);
    }
};
YAHOO.E3.createComponentCallback = function(data) {
    eval('new '+data.componentClass.replace(/_/g, '.')+'(\''+data.componentId+'\', \''+data.componentClass+'\');');
}


YAHOO.E3.Component.Abstract = function(componentId, componentClass) {
    
  this.componentId = componentId;
  this.componentClass = componentClass;
  this.htmlelement = document.getElementById('container_' + this.componentId);
  if(!this.htmlelement) return;
  this.el = new YAHOO.util.Element(this.htmlelement);
  if(!this.htmlelement.isInEditMode || this.htmlelement.isInEditMode==false) {
    this.init();
  }
};

YAHOO.E3.Component.Abstract.prototype.init = function() {
  this.editButton = new YAHOO.util.Element(document.createElement('a'));
  this.editButton.set('innerHTML', 'edit...');
  this.editButton.set('href', 'javascript:void(0)');
  this.editButton.set('className', 'E3AbstractEditButton');

if(!this.el.get('firstChild')) debugger;
  this.el.insertBefore(this.editButton, this.el.get('firstChild'));

  this.el.on('mouseover', this.handleMouseOver, this);
  this.el.on('mouseout', this.handleMouseOut, this);
  this.editButton.on('click', this.handleClick, this);
};

YAHOO.E3.Component.Abstract.prototype.handleSuccess = function(o) {
    var resp = eval('(' + o.responseText + ')');
    if(resp.html) this.htmlelement.innerHTML = resp.html;
    if (resp.createComponents) {
        for(var id in resp.createComponents) {
            YAHOO.E3.createComponent(id, resp.createComponents[id]);
        }
    }
};

YAHOO.E3.Component.Abstract.prototype.handleEditSuccess = function(o)
{
  this.htmlelement.innerHTML = o.responseText;

  this.el.removeListener('mouseover', this.el.handleMouseOver);  
  this.el.removeListener('mouseout', this.el.handleMouseOut);  

  var saveButton = new YAHOO.widget.Button({
                                          label: "Save", 
                                          container: this.el
                                      });
  saveButton.on('click', this.handleSave, null, this);
  var cancelButton = new YAHOO.widget.Button({
                                          label: "Cancel", 
                                          container: this.el
                                      });
  cancelButton.on('click', this.handleCancel, null, this);
};

YAHOO.E3.Component.Abstract.prototype.handleFailure = function(o) {
  alert('failure');
};

YAHOO.E3.Component.Abstract.prototype.handleMouseOver = function(o, scope) { 
    scope.editButton.setStyle('display', 'block');
};

YAHOO.E3.Component.Abstract.prototype.handleMouseOut = function(o, scope) { 
    scope.editButton.setStyle('display', 'none');
};

YAHOO.E3.Component.Abstract.prototype.handleSave = function() {
    this.htmlelement.isInEditMode = false;
    var form = this.htmlelement.getElementsByTagName('form')[0];
    this.htmlelement.innerHTML = 'saving...';
    YAHOO.util.Connect.setForm(form);
    YAHOO.util.Connect.asyncRequest('POST', '/ajax/fe/save?componentId='+this.componentId+'&componentClass='+this.componentClass+'&currentPageId='+currentPageId,
            {success: this.handleSuccess, failure: this.handleFailure, scope: this});
};

YAHOO.E3.Component.Abstract.prototype.handleCancel = function() {
    this.htmlelement.isInEditMode = false;
    this.htmlelement.innerHTML = 'reloading...';
    var connectionObject = YAHOO.util.Connect.asyncRequest('get', '/ajax/fe/cancel?componentId='+this.componentId+'&componentClass='+this.componentClass+'&currentPageId='+currentPageId,
    {success: this.handleSuccess, failure: this.handleFailure, scope: this});
};

YAHOO.E3.Component.Abstract.prototype.handleClick = function(o, e) {
    e.htmlelement.isInEditMode = true;
    e.htmlelement.innerHTML = 'loading...';
    YAHOO.util.Connect.asyncRequest('get', '/ajax/fe/edit?componentId='+e.componentId+'&componentClass='+e.componentClass+'&currentPageId='+currentPageId,
        {success: e.handleEditSuccess, failure: e.handleFailure, scope: e});
};
