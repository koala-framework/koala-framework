/*
Vps = function(){};

Vps.createComponent = function(componentId, componentClass) {
    if (!this.loadedFiles) this.loadedFiles = {};
    var componentData = {componentId: componentId, componentClass: componentClass, pageId: currentPageId};
    if (!this.loadedFiles[componentClass]) {
        this.loadedFiles[componentClass] = { 'finished': false, 'data': [componentData] }
        var file = '/files/'+componentClass.replace(/_/g, '/')+'.js';
        YAHOO.util.Connect.asyncRequest('GET', file,
                { failure: function() { }, scope: this,
                  success: function(o) {
                    eval(o.responseText);
                    this.loadedFiles[componentClass].finished = true;
                    for(var i=0; i<this.loadedFiles[componentClass].data.length; i++) {
                        Vps.createComponentCallback(this.loadedFiles[componentClass].data[i]);
                    }
                  }
                });
    } else if (!this.loadedFiles[componentClass].finished) {
        this.loadedFiles[componentClass].data.push(componentData);
    } else {
        Vps.createComponentCallback(componentData);
    }
};
    
Vps.createComponentCallback = function(data) {
    command = 'new '+data.componentClass.replace(/_/g, '.')+'(\''+data.componentId+'\', \''+data.componentClass+'\', \''+data.pageId+'\');';
    console.log(command);
    eval(command);
};



Vps.Component = function(){};
*/
Vps.Component.Abstract = function(componentId, componentClass, pageId)
{
    this.pageId = pageId;
    this.componentId = componentId;
    this.componentClass = componentClass;
    this.htmlelement = document.getElementById('container_' + this.componentId);
    if(!this.htmlelement) return;
    this.el = Ext.get(this.htmlelement);
    if(!this.htmlelement.isInEditMode || this.htmlelement.isInEditMode==false) {
        this.init();
    }
};

Vps.Component.Abstract.prototype.writeHead = function() {
    div = Ext.DomHelper.insertBefore(this.el, '<div></div>', true);
    var editButton = new Ext.Button(div, {
        text: "+", 
        handler: function() {
            alert('foo');
        },
        scope: this
    });
}

Vps.Component.Abstract.prototype.init = function()
{
    if (!Vps.Component.headWritten) {
        this.writeHead();
        Vps.Component.headWritten = true;
    }

    div = Ext.DomHelper.insertFirst(this.el, '<div></div>', true);
    this.editButton = new Ext.Button(div, {
        text: "Edit", 
        handler: this.handleClick,
        scope: this
    });
    this.editButton.el.addClass('VpsAbstractEditButton');
    this.editButton.setVisible(false);
    this.el.on('mouseover', function(){ this.editButton.setVisible(true); }, this);
    this.el.on('mouseout', function(){ this.editButton.setVisible(false); }, this);
};

Vps.Component.Abstract.prototype.handleClick = function(o, e) {
    this.htmlelement.isInEditMode = true;
    this.form = new Ext.form.Form({
        labelWidth: 75, // label settings here cascade unless overridden
        url:'/ajax/fe/save',
        labelWidth: 0,
        //fileUpload: true,
        labelAlign: '',
        buttonAlign: 'left',
        baseParams: {
            componentId: this.componentId,
            componentClass: this.componentClass,
            currentPageId: this.pageId
        }
    });
    this.handleEdit(o, e);
    this.el.update('');
    this.form.render(this.el);
    //this.form.el.encoding = 'multipart/form-data';
    //debugger;
    this.form.load({url:'/ajax/fe/edit'});
};

Vps.Component.Abstract.prototype.handleSave = function() {
    this.submitForm('save');
};

Vps.Component.Abstract.prototype.handleCancel = function() {
    this.submitForm('cancel');
};

Vps.Component.Abstract.prototype.submitForm = function(action)
{
    this.htmlelement.isInEditMode = false;
    this.form.baseParams.action = action;
    this.form.submit({
        success: this.handleSuccess,
        invalid: this.handleFailure,
        failure: this.handleFailure,
        scope: this
    });
}

Vps.Component.Abstract.prototype.handleSuccess = function(form, action) {
    resp = action.result;
    if(resp.html) this.htmlelement.innerHTML = resp.html;
    if (resp.createComponents) {
        for(var id in resp.createComponents) {
            Vps.createComponent(id, resp.createComponents[id]);
        }
    }
};

Vps.Component.Abstract.prototype.handleFailure = function(form, action) {
    alert('failure');
};

/*
Vps.Component.Abstract.prototype.handleSaveTemplate = function() {
    this.htmlelement.isInEditMode = false;
    var form = this.htmlelement.getElementsByTagName('form')[0];
    this.htmlelement.innerHTML = 'saving...';
    YAHOO.util.Connect.setForm(form);
    YAHOO.util.Connect.asyncRequest('POST', '/ajax/fe/save?componentId='+this.componentId+'&componentClass='+this.componentClass+'&currentPageId='+this.pageId,
            {success: this.handleSuccess, failure: this.handleFailure, scope: this});
};

Vps.Component.Abstract.prototype.handleEdit = function(o, e) {
    this.htmlelement.innerHTML = 'loading...';
    YAHOO.util.Connect.asyncRequest('get', '/ajax/fe/edit?componentId='+this.componentId+'&componentClass='+this.componentClass+'&currentPageId='+this.pageId,
        {success: this.handleEditSuccess, failure: this.handleFailure, scope: this});
};
*/

