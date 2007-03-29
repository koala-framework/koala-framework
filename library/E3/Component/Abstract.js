YAHOO.namespace('E3.Component');
var E3 = YAHOO.E3;

E3.Component.Abstract = function(componentId, componentClass) {
    
  this.componentId = componentId;
  this.componentClass = componentClass;
  this.htmlelement = document.getElementById('container_' + this.componentId);
  this.el = new YAHOO.util.Element(this.htmlelement);
  this.success = this.handleSuccess;
  this.failure = this.handleFailure;
  this.init();
};

E3.Component.Abstract.prototype.init = function() {
  this.el.on('mouseover', this.handleMouseOver, this.el);
  this.el.on('mouseout', this.handleMouseOut, this.el);
  this.el.on('click', this.handleClick, this);
};

E3.Component.Abstract.prototype.handleSuccess = function(o) {
    this.htmlelement.innerHTML = o.responseText;
    this.init(this.componentId);
};

E3.Component.Abstract.prototype.handleEditSuccess = function(o)
{
    this.htmlelement.innerHTML = o.responseText;

  this.el.removeListener('mouseover', this.el.handleMouseOver);  
  this.el.removeListener('mouseout', this.el.handleMouseOut);  
  this.el.removeListener('click', this.el.handleClick);  

  this.el.setStyle('border', '');
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

E3.Component.Abstract.prototype.handleFailure = function(o) {
  alert('failure');
};

E3.Component.Abstract.prototype.handleMouseOver = function(o, el) { 
  el.setStyle('border', '1px solid black');
};

E3.Component.Abstract.prototype.handleMouseOut = function(o, el) { 
  el.setStyle('border', '');
};

E3.Component.Abstract.prototype.handleSave = function() {
    var inputs = this.htmlelement.getElementsByTagName('input');
    var data = '';
    for(var i=0;i<inputs.length;i++) {
        data += '&'+encodeURIComponent(inputs[i].name)+'='+encodeURIComponent(inputs[i].value);
    }
  YAHOO.util.Connect.asyncRequest('post', '/ajax/fe/save?componentId='+this.componentId+'&componentClass='+this.componentClass, this, data);
};

E3.Component.Abstract.prototype.handleCancel = function() { 
  var connectionObject = YAHOO.util.Connect.asyncRequest('get', '/ajax/fe/cancel?componentId='+this.componentId+'&componentClass='+this.componentClass, this);
};

E3.Component.Abstract.prototype.handleClick = function(o, e) {
  YAHOO.util.Connect.asyncRequest('get', '/ajax/fe/edit?componentId='+e.componentId+'&componentClass='+e.componentClass, {success: e.handleEditSuccess, failure: e.handleFailure, scope: e});
};
