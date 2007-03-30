YAHOO.namespace('E3.Component');
var E3 = YAHOO.E3;

E3.Component.Abstract = function(componentId, componentClass) {
    
  this.componentId = componentId;
  this.componentClass = componentClass;
  this.htmlelement = document.getElementById('container_' + this.componentId);
  this.el = new YAHOO.util.Element(this.htmlelement);
  this.init();
};

E3.Component.Abstract.prototype.init = function() {
  this.editButton = new YAHOO.util.Element(document.createElement('a'));
  this.editButton.set('innerHTML', 'edit...');
  this.editButton.set('href', 'javascript:void(0)');
  this.editButton.set('className', 'E3AbstractEditButton');

  this.el.insertBefore(this.editButton, this.el.get('firstChild'));

  this.el.on('mouseover', this.handleMouseOver, this);
  this.el.on('mouseout', this.handleMouseOut, this);
  this.editButton.on('click', this.handleClick, this);
};

E3.Component.Abstract.prototype.handleSuccess = function(o) {
    var resp = eval('(' + o.responseText + ')');
    if(resp.html) this.htmlelement.innerHTML = resp.html;
    this.init(this.componentId);
    if (resp.createComponents) {
        for(var id in resp.createComponents) {
            var className = resp.createComponents[id].replace(/_/g, '.');
            eval('new '+className+'(\''+id+'\', \''+className+'\');');
        }
    }
};

E3.Component.Abstract.prototype.handleEditSuccess = function(o)
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

E3.Component.Abstract.prototype.handleFailure = function(o) {
  alert('failure');
};

E3.Component.Abstract.prototype.handleMouseOver = function(o, scope) { 
    scope.editButton.setStyle('display', 'block');
};

E3.Component.Abstract.prototype.handleMouseOut = function(o, scope) { 
    scope.editButton.setStyle('display', 'none');
};

E3.Component.Abstract.prototype.handleSave = function() {
    var form = this.htmlelement.getElementsByTagName('form')[0];
    this.htmlelement.innerHTML = 'saving...';
    YAHOO.util.Connect.setForm(form);
    YAHOO.util.Connect.asyncRequest('POST', '/ajax/fe/save?componentId='+this.componentId+'&componentClass='+this.componentClass,
            {success: this.handleSuccess, failure: this.handleFailure, scope: this});
};

E3.Component.Abstract.prototype.handleCancel = function() {
    this.htmlelement.innerHTML = 'reloading...';
    var connectionObject = YAHOO.util.Connect.asyncRequest('get', '/ajax/fe/cancel?componentId='+this.componentId+'&componentClass='+this.componentClass,
    {success: this.handleSuccess, failure: this.handleFailure, scope: this});
};

E3.Component.Abstract.prototype.handleClick = function(o, e) {
    e.htmlelement.innerHTML = 'loading...';
    YAHOO.util.Connect.asyncRequest('get', '/ajax/fe/edit?componentId='+e.componentId+'&componentClass='+e.componentClass,
        {success: e.handleEditSuccess, failure: e.handleFailure, scope: e});
};
