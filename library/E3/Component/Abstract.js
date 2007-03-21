YAHOO.namespace('E3.Component');
var E3 = YAHOO.E3;

E3.Component.Abstract = function(componentId) {
    
	this.componentId = componentId;
	this.htmlelement = document.getElementById('container_' + this.componentId);
	this.success = this.handleSuccess;
	this.failure = this.handleFailure;
	this.init();
};

E3.Component.Abstract.prototype.init = function() {
	el = new YAHOO.util.Element(this.htmlelement);
	el.on('mouseover', this.handleMouseOver, el);
	el.on('mouseout', this.handleMouseOut, el);
	el.on('click', this.handleClick, {el:el, scope:this});
}

E3.Component.Abstract.prototype.handleSuccess = function(o){
  		this.htmlelement.innerHTML = o.responseText;
		this.init(this.componentId);
	},

E3.Component.Abstract.prototype.handleFailure = function(o){
	alert('failure');
},

E3.Component.Abstract.prototype.handleMouseOver = function(o, el) { 
	el.setStyle('border', '1px solid black');
};

E3.Component.Abstract.prototype.handleMouseOut = function(o, el) { 
	el.setStyle('border', '');
};

E3.Component.Abstract.prototype.handleSave = function() {
	var connectionObject = YAHOO.util.Connect.asyncRequest('get', 'ajax/fe?componentId='+this.componentId, this);
};

E3.Component.Abstract.prototype.handleCancel = function() { 
	var connectionObject = YAHOO.util.Connect.asyncRequest('get', 'ajax/fe?componentId='+this.componentId, this);
};

E3.Component.Abstract.prototype.handleClick = function(o, e) {
	el = e.el;
	scope = e.scope;

	el.removeListener('mouseover', el.handleMouseOver);  
	el.removeListener('mouseout', el.handleMouseOut);  
	el.removeListener('click', el.handleClick);  

	el.setStyle('border', '');
	var saveButton = new YAHOO.widget.Button({
	                                        label: "Save", 
	                                        container: el
	                                    });
	saveButton.on('click', scope.handleSave, null, scope);
	var cancelButton = new YAHOO.widget.Button({
	                                        label: "Cancel", 
	                                        container: el
	                                    });
	cancelButton.on('click', scope.handleCancel, null, scope);
};
