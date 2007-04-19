YAHOO.Vps.Component.Paragraphs = function(componentId, componentClass) {
    YAHOO.Vps.Component.Paragraphs.superclass.constructor.call(this, componentId, componentClass);
    
};
YAHOO.lang.extend(YAHOO.Vps.Component.Paragraphs, YAHOO.Vps.Component.Abstract);

YAHOO.Vps.Component.Paragraphs.prototype.handleSave = function() {
    var data = 'order=';
    for (var i = 0; i < this.containers.length; i++) {
        data += this.containers[i].id+';';
    }
    data = data.substr(0, data.length-1);
    YAHOO.util.Connect.asyncRequest('POST', '/ajax/fe/save?componentId='+this.componentId+'&componentClass='+this.componentClass+'&currentPageId='+currentPageId,
            {failure: this.handleFailure, scope: this}, data);
}
YAHOO.Vps.Component.Paragraphs.prototype.init = function() {
	this.containers = new Array();
	
	for(var i = 0; i < this.htmlelement.childNodes.length; i++) {
	    var node = this.htmlelement.childNodes[i];
	    if(node.nodeName.toLowerCase() == "div" && 
	        node.id.substr(0, 10) == "container_" &&
	        !node.isInEditMode) {
	        var container = new Object();
		    container.component = this;
		    container.node = node;
		    container.id = node.id.substr(10);
	        this.containers.push(container);
    		
            container.moveButton = document.createElement('div');
            container.moveButton.innerHTML = 'move...';
            container.moveButton.className = 'VpsParagraphsMoveButton';
            
            container.node.insertBefore(container.moveButton, container.node.firstChild);

            var e = new YAHOO.util.Element(container.node);
            e.on('mouseover', function(o, scope) { scope.moveButton.style.display = 'block'; }, container);
            e.on('mouseout', function(o, scope) { scope.moveButton.style.display = 'none'; }, container);

    		
    		var dd = new YAHOO.Vps.ParagraphDragDrop(container);
	    }
	}
}
YAHOO.namespace('YAHOO.Vps.ParagraphDragDrop');

YAHOO.Vps.ParagraphDragDrop = function (container, sGroup, config) {
    if (container) {
        this.init(container.node, sGroup, config);
        this.setHandleElId(container.moveButton);
        this.initFrame();
    }
    
    this.container = container;

    var el = this.getDragEl();
    YAHOO.util.Dom.setStyle(el, "opacity", 0.67);

    this.setPadding(-4);
    this.goingUp = false;
    this.lastY = 0;
}

YAHOO.Vps.ParagraphDragDrop.prototype = new YAHOO.util.DDProxy();

YAHOO.Vps.ParagraphDragDrop.prototype.startDrag = function(x, y) {
    var dragEl = this.getDragEl();
    var clickEl = this.getEl();
    YAHOO.util.Dom.setStyle(clickEl, "visibility", "hidden");

    
    dragEl.innerHTML = clickEl.innerHTML;

    YAHOO.util.Dom.setStyle(dragEl, "color", YAHOO.util.Dom.getStyle(clickEl, "color"));
    YAHOO.util.Dom.setStyle(dragEl, "backgroundColor", "gray");
    YAHOO.util.Dom.setStyle(dragEl, "border", "2px solid gray");
}


YAHOO.Vps.ParagraphDragDrop.prototype.endDrag = function(e) {
    var el = this.getEl();
    var srcEl = this.getEl();
    var proxy = this.getDragEl();
    YAHOO.util.Dom.setStyle(proxy, "visibility", "visible");

    // animate the proxy element to the src element's location
    var a = new YAHOO.util.Motion( 
        proxy, { 
            points: { 
                to: YAHOO.util.Dom.getXY(srcEl)
            }
        }, 
        0.3, 
        YAHOO.util.Easing.easeOut 
    )
    var proxyid = proxy.id;
    var id = this.id;
    a.onComplete.subscribe(function() {
            YAHOO.util.Dom.setStyle(proxyid, "visibility", "hidden");
            YAHOO.util.Dom.setStyle(id, "visibility", "");
        });
    a.animate();
    srcEl.style.backgroundColor = '';

     this.container.component.handleSave();
};

YAHOO.Vps.ParagraphDragDrop.prototype.onDrag = function(e, id) {

    var y = YAHOO.util.Event.getPageY(e);

    if (y < this.lastY) {
        this.goingUp = true;
    } else if (y > this.lastY) {
        this.goingUp = false;
    }

    this.lastY = y;
};

YAHOO.Vps.ParagraphDragDrop.prototype.onDragOver = function(e, id)
{
    var srcEl = this.getEl();
    var destEl;

    destEl = YAHOO.util.Dom.get(id);

    if (this.goingUp) {
        if (srcEl.nextSibling== destEl) return;
    } else {
        if (srcEl.previousSibling  == destEl) return;
    }
    var p = destEl.parentNode;

    if (this.goingUp) {
        p.insertBefore(srcEl, destEl);
    } else {
        p.insertBefore(srcEl, destEl.nextSibling);
    }
    var containers = this.container.component.containers;

    var newContainers = [];

    for(var i = 0; i < containers.length; i++) {
        if (containers[i].node == destEl) {
            if (this.goingUp) {
                newContainers.push(containers[i+1]);
            } else {
                newContainers.push(containers[i-1]);
            }
        } else if (containers[i].node == srcEl) {
            if (this.goingUp) {
                newContainers.push(containers[i-1]);
            } else {
                newContainers.push(containers[i+1]);
            }
        } else {
            newContainers.push(containers[i]);
        }
    }
    this.container.component.containers = newContainers;

    YAHOO.util.DragDropMgr.refreshCache();
};