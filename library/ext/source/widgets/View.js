/*
 * Ext JS Library 1.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.View
 * @extends Ext.util.Observable
 * Create a "View" for an element based on a data model or UpdateManager and the supplied DomHelper template. 
 * This class also supports single and multi selection modes. <br>
 * Create a data model bound view:
 <pre><code>
 var store = new Ext.data.Store(...);

 var view = new Ext.View("my-element",
 '&lt;div id="{0}"&gt;{2} - {1}&lt;/div&gt;', // auto create template
 {
 singleSelect: true,
 selectedClass: "ydataview-selected",
 store: store
 });

 // listen for node click?
 view.on("click", function(vw, index, node, e){
 alert('Node "' + node.id + '" at index: ' + index + " was clicked.");
 });

 // load XML data
 dataModel.load("foobar.xml");
 </code></pre>
 For an example of creating a JSON/UpdateManager view, see {@link Ext.JsonView}.
 * <br><br>
 * <b>Note: The root of your template must be a single node. Table/row implementations may work but are not supported due to
 * IE"s limited insertion support with tables and Opera"s faulty event bubbling.</b>
 * @constructor
 * Create a new View
 * @param {String/HTMLElement/Element} container The container element where the view is to be rendered.
 * @param {String/DomHelper.Template} tpl The rendering template or a string to create a template with
 * @param {Object} config The config object
 */
Ext.View = function(container, tpl, config){
    this.el = Ext.get(container, true);
    if(typeof tpl == "string"){
        tpl = new Ext.Template(tpl);
    }
    tpl.compile();
    /**
     * The template used by this View
     * @type {Ext.DomHelper.Template}
     */
    this.tpl = tpl;

    Ext.apply(this, config);

    /** @private */
    this.events = {
    /**
     * @event beforeclick
     * Fires before a click is processed. Returns false to cancel the default action.
     * @param {Ext.View} this
     * @param {Number} index The index of the target node
     * @param {HTMLElement} node The target node
     * @param {Ext.EventObject} e The raw event object
     */
        "beforeclick" : true,
    /**
     * @event click
     * Fires when a template node is clicked.
     * @param {Ext.View} this
     * @param {Number} index The index of the target node
     * @param {HTMLElement} node The target node
     * @param {Ext.EventObject} e The raw event object
     */
        "click" : true,
    /**
     * @event dblclick
     * Fires when a template node is double clicked.
     * @param {Ext.View} this
     * @param {Number} index The index of the target node
     * @param {HTMLElement} node The target node
     * @param {Ext.EventObject} e The raw event object
     */
        "dblclick" : true,
    /**
     * @event contextmenu
     * Fires when a template node is right clicked.
     * @param {Ext.View} this
     * @param {Number} index The index of the target node
     * @param {HTMLElement} node The target node
     * @param {Ext.EventObject} e The raw event object
     */
        "contextmenu" : true,
    /**
     * @event selectionchange
     * Fires when the selected nodes change.
     * @param {Ext.View} this
     * @param {Array} selections Array of the selected nodes
     */
        "selectionchange" : true,

    /**
     * @event beforeselect
     * Fires before a selection is made. If any handlers return false, the selection is cancelled.
     * @param {Ext.View} this
     * @param {HTMLElement} node The node to be selected
     * @param {Array} selections Array of currently selected nodes
     */
        "beforeselect" : true
    };

    this.el.on({
        "click": this.onClick,
        "dblclick": this.onDblClick,
        "contextmenu": this.onContextMenu,
        scope:this
    });

    this.selections = [];
    this.nodes = [];
    this.cmp = new Ext.CompositeElementLite([]);
    if(this.store){
        this.setStore(this.store, true);
    }
    Ext.View.superclass.constructor.call(this);
};

Ext.extend(Ext.View, Ext.util.Observable, {
    /**
     * The css class to add to selected nodes
     * @type {Ext.DomHelper.Template}
     */
    selectedClass : "x-view-selected",
    
    emptyText : "",
    /**
     * Returns the element this view is bound to.
     * @return {Ext.Element}
     */
    getEl : function(){
        return this.el;
    },

    /**
     * Refreshes the view.
     */
    refresh : function(){
        var t = this.tpl;
        this.clearSelections();
        this.el.update("");
        var html = [];
        var records = this.store.getRange();
        if(records.length < 1){
            this.el.update(this.emptyText);
            return;
        }
        for(var i = 0, len = records.length; i < len; i++){
            var data = this.prepareData(records[i].data, i, records[i]);
            html[html.length] = t.apply(data);
        }
        this.el.update(html.join(""));
        this.nodes = this.el.dom.childNodes;
        this.updateIndexes(0);
    },

    /**
     * Function to override to reformat the data that is sent to
     * the template for each node.
     * @param {Array/Object} data The raw data (array of colData for a data model bound view or
     * a JSON object for an UpdateManager bound view).
     * @param {Number} index The index of the data within the data model
     */
    prepareData : function(data, index){
        return data;
    },

    onUpdate : function(ds, record){
        this.clearSelections();
        var index = this.store.indexOf(record);
        var n = this.nodes[index];
        this.tpl.insertBefore(n, this.prepareData(record.data));
        n.parentNode.removeChild(n);
        this.updateIndexes(index, index);
    },

    onAdd : function(ds, records, index){
        this.clearSelections();
        if(this.nodes.length == 0){
            this.refresh();
            return;
        }
        var n = this.nodes[index];
        for(var i = 0, len = records.length; i < len; i++){
            var d = this.prepareData(records[i].data);
            if(n){
                this.tpl.insertBefore(n, d);
            }else{
                this.tpl.append(this.el, d);
            }
        }
        this.updateIndexes(index);
    },

    onRemove : function(ds, record, index){
        this.clearSelections();
        this.el.dom.removeChild(this.nodes[index]);
        this.updateIndexes(index);
    },

    /**
     * Refresh an individual node.
     * @param {Number} index
     */
    refreshNode : function(index){
        this.onUpdate(this.store, this.store.getAt(index));
    },

    updateIndexes : function(startIndex, endIndex){
        var ns = this.nodes;
        startIndex = startIndex || 0;
        endIndex = endIndex || ns.length - 1;
        for(var i = startIndex; i <= endIndex; i++){
            ns[i].nodeIndex = i;
        }
    },

    /**
     * Changes the data store this view uses and refresh the view.
     * @param {Store} store
     */
    setStore : function(store, initial){
        if(!initial && this.store){
            this.store.un("datachanged", this.refresh);
            this.store.un("add", this.onAdd);
            this.store.un("remove", this.onRemove);
            this.store.un("update", this.onUpdate);
            this.store.un("clear", this.refresh);
        }
        if(store){
            store.on("datachanged", this.refresh, this);
            store.on("add", this.onAdd, this);
            store.on("remove", this.onRemove, this);
            store.on("update", this.onUpdate, this);
            store.on("clear", this.refresh, this);
        }
        this.store = store;
        this.refresh();
    },

    /**
     * Returns the template node the passed child belongs to or null if it doesn't belong to one.
     * @param {HTMLElement} node
     * @return {HTMLElement} The template node
     */
    findItemFromChild : function(node){
        var el = this.el.dom;
        if(!node || node.parentNode == el){
		    return node;
	    }
	    var p = node.parentNode;
	    while(p && p != el){
            if(p.parentNode == el){
            	return p;
            }
            p = p.parentNode;
        }
	    return null;
    },

    /** @ignore */
    onClick : function(e){
        var item = this.findItemFromChild(e.getTarget());
        if(item){
            var index = this.indexOf(item);
            if(this.onItemClick(item, index, e) !== false){
                this.fireEvent("click", this, index, item, e);
            }
        }else{
            this.clearSelections();
        }
    },

    /** @ignore */
    onContextMenu : function(e){
        var item = this.findItemFromChild(e.getTarget());
        if(item){
            this.fireEvent("contextmenu", this, this.indexOf(item), item, e);
        }
    },

    /** @ignore */
    onDblClick : function(e){
        var item = this.findItemFromChild(e.getTarget());
        if(item){
            this.fireEvent("dblclick", this, this.indexOf(item), item, e);
        }
    },

    onItemClick : function(item, index, e){
        if(this.fireEvent("beforeclick", this, index, item, e) === false){
            return false;
        }
        if(this.multiSelect || this.singleSelect){
            if(this.multiSelect && e.shiftKey && this.lastSelection){
                this.select(this.getNodes(this.indexOf(this.lastSelection), index), false);
            }else{
                this.select(item, this.multiSelect && e.ctrlKey);
                this.lastSelection = item;
            }
            e.preventDefault();
        }
        return true;
    },

    /**
     * Get the number of selected nodes.
     * @return {Number}
     */
    getSelectionCount : function(){
        return this.selections.length;
    },

    /**
     * Get the currently selected nodes.
     * @return {Array} An array of HTMLElements
     */
    getSelectedNodes : function(){
        return this.selections;
    },

    /**
     * Get the indexes of the selected nodes.
     * @return {Array}
     */
    getSelectedIndexes : function(){
        var indexes = [], s = this.selections;
        for(var i = 0, len = s.length; i < len; i++){
            indexes.push(s[i].nodeIndex);
        }
        return indexes;
    },

    /**
     * Clear all selections
     * @param {Boolean} suppressEvent (optional) true to skip firing of the selectionchange event
     */
    clearSelections : function(suppressEvent){
        if(this.nodes && (this.multiSelect || this.singleSelect) && this.selections.length > 0){
            this.cmp.elements = this.selections;
            this.cmp.removeClass(this.selectedClass);
            this.selections = [];
            if(!suppressEvent){
                this.fireEvent("selectionchange", this, this.selections);
            }
        }
    },

    /**
     * Returns true if the passed node is selected
     * @param {HTMLElement/Number} node The node or node index
     * @return {Boolean}
     */
    isSelected : function(node){
        var s = this.selections;
        if(s.length < 1){
            return false;
        }
        node = this.getNode(node);
        return s.indexOf(node) !== -1;
    },

    /**
     * Selects nodes.
     * @param {Array/HTMLElement/String/Number} nodeInfo An HTMLElement template node, index of a template node, id of a template node or an array of any of those to select
     * @param {Boolean} keepExisting (optional) true to keep existing selections
     * @param {Boolean} suppressEvent (optional) true to skip firing of the selectionchange vent
     */
    select : function(nodeInfo, keepExisting, suppressEvent){
        if(nodeInfo instanceof Array){
            if(!keepExisting){
                this.clearSelections(true);
            }
            for(var i = 0, len = nodeInfo.length; i < len; i++){
                this.select(nodeInfo[i], true, true);
            }
        } else{
            var node = this.getNode(nodeInfo);
            if(node && !this.isSelected(node)){
                if(!keepExisting){
                    this.clearSelections(true);
                }
                if(this.fireEvent("beforeselect", this, node, this.selections) !== false){
                    Ext.fly(node).addClass(this.selectedClass);
                    this.selections.push(node);
                    if(!suppressEvent){
                        this.fireEvent("selectionchange", this, this.selections);
                    }
                }
            }
        }
    },

    /**
     * Gets a template node.
     * @param {HTMLElement/String/Number} nodeInfo An HTMLElement template node, index of a template node or the id of a template node
     * @return {HTMLElement} The node or null if it wasn't found
     */
    getNode : function(nodeInfo){
        if(typeof nodeInfo == "string"){
            return document.getElementById(nodeInfo);
        }else if(typeof nodeInfo == "number"){
            return this.nodes[nodeInfo];
        }
        return nodeInfo;
    },

    /**
     * Gets a range template nodes.
     * @param {Number} startIndex
     * @param {Number} endIndex
     * @return {Array} An array of nodes
     */
    getNodes : function(start, end){
        var ns = this.nodes;
        start = start || 0;
        end = typeof end == "undefined" ? ns.length - 1 : end;
        var nodes = [];
        if(start <= end){
            for(var i = start; i <= end; i++){
                nodes.push(ns[i]);
            }
        } else{
            for(var i = start; i >= end; i--){
                nodes.push(ns[i]);
            }
        }
        return nodes;
    },

    /**
     * Finds the index of the passed node
     * @param {HTMLElement/String/Number} nodeInfo An HTMLElement template node, index of a template node or the id of a template node
     * @return {Number} The index of the node or -1
     */
    indexOf : function(node){
        node = this.getNode(node);
        if(typeof node.nodeIndex == "number"){
            return node.nodeIndex;
        }
        var ns = this.nodes;
        for(var i = 0, len = ns.length; i < len; i++){
            if(ns[i] == node){
                return i;
            }
        }
        return -1;
    }
});

/**
 * @class Ext.JsonView
 * @extends Ext.View
 * Shortcut class to create a JSON + UpdateManager template view. Usage:
 <pre><code>
 var view = new Ext.JsonView("my-element",
 '&lt;div id="{id}"&gt;{foo} - {bar}&lt;/div&gt;', // auto create template
 { multiSelect: true, jsonRoot: "data" });

 // listen for node click?
 view.on("click", function(vw, index, node, e){
 alert('Node "' + node.id + '" at index: ' + index + " was clicked.");
 });

 // direct load of JSON data
 view.load("foobar.php");


 // Example from my blog list
 var tpl = new Ext.Template(
 '&lt;div class="entry"&gt;' +
 '&lt;a class="entry-title" href="{link}"&gt;{title}&lt;/a&gt;' +
 "&lt;h4&gt;{date} by {author} | {comments} Comments&lt;/h4&gt;{description}" +
 "&lt;/div&gt;&lt;hr /&gt;"
 );

 var moreView = new Ext.JsonView("entry-list", tpl, {
 jsonRoot: "posts"
 });
 moreView.on("beforerender", this.sortEntries, this);
 moreView.load({
 url:"/blog/get-posts.php",
 params: "allposts=true",
 text:"Loading Blog Entries..."
 });
 </code></pre>
 * @constructor
 * Create a new JsonView
 * @param {String/HTMLElement/Element} container The container element where the view is to be rendered.
 * @param {Template} tpl The rendering template
 * @param {Object} config The config object
 */
Ext.JsonView = function(container, tpl, config){
    Ext.JsonView.superclass.constructor.call(this, container, tpl, config);

    var um = this.el.getUpdateManager();
    um.setRenderer(this);
    um.on("update", this.onLoad, this);
    um.on("failure", this.onLoadException, this);

    /**
     * @event beforerender
     * Fires before rendering of the downloaded json data.
     * @param {Ext.View} this
     * @param {Object} data The json data loaded
     */
    this.events["beforerender"] = true;
    /**
     * @event load
     * Fires when data is loaded.
     * @param {Ext.View} this
     * @param {Object} data The json data loaded
     * @param {Object} response The raw Connect response object
     */
    this.events["load"] = true;
    /**
     * @event loadexception
     * Fires when loading fails.
     * @param {Ext.View} this
     * @param {Object} response The raw Connect response object
     */
    this.events["loadexception"] = true;
};
Ext.extend(Ext.JsonView, Ext.View, {
    /**
     * The root property in the loaded json object that contains the data
     * @type {String}
     */
    jsonRoot : "",

    /**
     * Refreshes the view.
     */
    refresh : function(){
        this.clearSelections();
        this.el.update("");
        var html = [];
        var o = this.jsonData;
        if(o && o.length > 0){
            for(var i = 0, len = o.length; i < len; i++){
                var data = this.prepareData(o[i], i, o);
                html[html.length] = this.tpl.apply(data);
            }
        }else{
            html.push(this.emptyText);
        }
        this.el.update(html.join(""));
        this.nodes = this.el.dom.childNodes;
        this.updateIndexes(0);
    },

    /**
     * Performs an async request, loading the JSON from the response. If params are specified it uses POST, otherwise it uses GET.
     * @param {Object/String/Function} url The url for this request or a function to call to get the url or a config object containing any of the following options:
     <pre><code>
     view.load({
     url: "your-url.php",<br/>
     params: {param1: "foo", param2: "bar"}, // or a URL encoded string<br/>
     callback: yourFunction,<br/>
     scope: yourObject, //(optional scope)  <br/>
     discardUrl: false, <br/>
     nocache: false,<br/>
     text: "Loading...",<br/>
     timeout: 30,<br/>
     scripts: false<br/>
     });
     </code></pre>
     * The only required property is url. The optional properties nocache, text and scripts
     * are shorthand for disableCaching, indicatorText and loadScripts and are used to set their associated property on this UpdateManager instance.
     * @param {String/Object} params (optional) The parameters to pass as either a url encoded string "param1=1&amp;param2=2" or an object {param1: 1, param2: 2}
     * @param {Function} callback (optional) Callback when transaction is complete - called with signature (oElement, bSuccess)
     * @param {Boolean} discardUrl (optional) By default when you execute an update the defaultUrl is changed to the last used url. If true, it will not store the url.
     */
    load : function(){
        var um = this.el.getUpdateManager();
        um.update.apply(um, arguments);
    },

    render : function(el, response){
        this.clearSelections();
        this.el.update("");
        var o;
        try{
            o = Ext.util.JSON.decode(response.responseText);
            if(this.jsonRoot){
                o = eval("o." + this.jsonRoot);
            }
        } catch(e){
        }
        /**
         * The current json data or null
         */
        this.jsonData = o;
        this.beforeRender();
        this.refresh();
    },

/**
 * Get the number of records in the current JSON dataset
 * @return {Number}
 */
    getCount : function(){
        return this.jsonData ? this.jsonData.length : 0;
    },

/**
 * Returns the JSON object for the specified node(s)
 * @param {HTMLElement/Array} node The node or an array of nodes
 * @return {Object/Array} If you pass in an array, you get an array back, otherwise
 * you get the JSON object for the node
 */
    getNodeData : function(node){
        if(node instanceof Array){
            var data = [];
            for(var i = 0, len = node.length; i < len; i++){
                data.push(this.getNodeData(node[i]));
            }
            return data;
        }
        return this.jsonData[this.indexOf(node)] || null;
    },

    beforeRender : function(){
        this.snapshot = this.jsonData;
        if(this.sortInfo){
            this.sort.apply(this, this.sortInfo);
        }
        this.fireEvent("beforerender", this, this.jsonData);
    },

    onLoad : function(el, o){
        this.fireEvent("load", this, this.jsonData, o);
    },

    onLoadException : function(el, o){
        this.fireEvent("loadexception", this, o);
    },

/**
 * Filter the data by a specific property.
 * @param {String} property A property on your JSON objects
 * @param {String/RegExp} value Either string that the property values
 * should start with or a RegExp to test against the property
 */
    filter : function(property, value){
        if(this.jsonData){
            var data = [];
            var ss = this.snapshot;
            if(typeof value == "string"){
                var vlen = value.length;
                if(vlen == 0){
                    this.clearFilter();
                    return;
                }
                value = value.toLowerCase();
                for(var i = 0, len = ss.length; i < len; i++){
                    var o = ss[i];
                    if(o[property].substr(0, vlen).toLowerCase() == value){
                        data.push(o);
                    }
                }
            } else if(value.exec){ // regex?
                for(var i = 0, len = ss.length; i < len; i++){
                    var o = ss[i];
                    if(value.test(o[property])){
                        data.push(o);
                    }
                }
            } else{
                return;
            }
            this.jsonData = data;
            this.refresh();
        }
    },

/**
 * Filter by a function. The passed function will be called with each
 * object in the current dataset. If the function returns true, the value is kept
 * otherwise it is filtered.
 * @param {Function} fn
 * @param {Object} scope (optional) The scope of the function (defaults to this JsonView)
 */
    filterBy : function(fn, scope){
        if(this.jsonData){
            var data = [];
            var ss = this.snapshot;
            for(var i = 0, len = ss.length; i < len; i++){
                var o = ss[i];
                if(fn.call(scope || this, o)){
                    data.push(o);
                }
            }
            this.jsonData = data;
            this.refresh();
        }
    },

/**
 * Clears the current filter.
 */
    clearFilter : function(){
        if(this.snapshot && this.jsonData != this.snapshot){
            this.jsonData = this.snapshot;
            this.refresh();
        }
    },


/**
 * Sorts the data for this view and refreshes it.
 * @param {String} property A property on your JSON objects to sort on
 * @param {String} direction (optional) desc or asc (defaults to asc)
 * @param {Function} sortType (optional) A function to call to convert the data to a sortable value.
 */
    sort : function(property, dir, sortType){
        this.sortInfo = Array.prototype.slice.call(arguments, 0);
        if(this.jsonData){
            var p = property;
            var dsc = dir && dir.toLowerCase() == "desc";
            var f = function(o1, o2){
                var v1 = sortType ? sortType(o1[p]) : o1[p];
                var v2 = sortType ? sortType(o2[p]) : o2[p];
                ;
                if(v1 < v2){
                    return dsc ? +1 : -1;
                } else if(v1 > v2){
                    return dsc ? -1 : +1;
                } else{
                    return 0;
                }
            };
            this.jsonData.sort(f);
            this.refresh();
            if(this.jsonData != this.snapshot){
                this.snapshot.sort(f);
            }
        }
    }
});