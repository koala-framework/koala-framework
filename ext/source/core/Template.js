/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
* @class Ext.Template
* Represents an HTML fragment template. Templates can be precompiled for greater performance.
* For a list of available format functions, see {@link Ext.util.Format}.
<pre><code>
var t = new Ext.Template(
	'&lt;div name="{id}"&gt;',
		'&lt;span class="{cls}"&gt;{name:trim} {value:ellipsis(10)}&lt;/span&gt;',
	'&lt;/div&gt;'
);
t.append('some-element', {id: 'myid', name: 'foo', value: 'bar'});
</code></pre>
* For more information see <a href="http://www.jackslocum.com/yui/2006/10/06/domhelper-create-elements-using-dom-html-fragments-or-templates/">this blog post with examples</a>. 
* <br>
* @constructor
* @param {String/Array} html The HTML fragment or an array of fragments to join('') or multiple arguments to join('')
*/
Ext.Template = function(html){
    if(html instanceof Array){
        html = html.join("");
    }else if(arguments.length > 1){
        html = Array.prototype.join.call(arguments, "");
    }
    /**@private*/
    this.html = html;
    
};
Ext.Template.prototype = {
    /**
     * Returns an HTML fragment of this template with the specified values applied
     * @param {Object} values The template values. Can be an array if your params are numeric (i.e. {0}) or an object (i.e. {foo: 'bar'})
     * @return {String}
     */
    applyTemplate : function(values){
        if(this.compiled){
            return this.compiled(values);
        }
        var useF = this.disableFormats !== true;
        var fm = Ext.util.Format, tpl = this;
        var fn = function(m, name, format, args){
            if(format && useF){
                if(format.substr(0, 5) == "this."){
                    return tpl.call(format.substr(5), values[name]);
                }else{
                    if(args){
                        // quoted values are required for strings in compiled templates, 
                        // but for non compiled we need to strip them
                        // quoted reversed for jsmin
                        var re = /^\s*['"](.*)["']\s*$/;
                        args = args.split(',');
                        for(var i = 0, len = args.length; i < len; i++){
                            args[i] = args[i].replace(re, "$1");
                        }
                        args = [values[name]].concat(args);
                    }else{
                        args = [values[name]];
                    }
                    return fm[format].apply(fm, args);
                }
            }else{
                return values[name] !== undefined ? values[name] : "";
            }
        };
        return this.html.replace(this.re, fn);
    },
    
    /**
     * Sets the html used as the template and optionally compiles it
     * @param {String} html
     * @param {Boolean} compile (optional)
     * @return {Template} this
     */
    set : function(html, compile){
        this.html = html;
        this.compiled = null;
        if(compile){
            this.compile();
        }
        return this;
    },
    
    /**
     * True to disable format functions (default to false)
     * @type Boolean
     */
    disableFormats : false,
    
    /**
    * The regular expression used to match template variables 
    * @type RegExp
    * @property 
    */
    re : /\{([\w-]+)(?:\:([\w\.]*)(?:\((.*?)?\))?)?\}/g,
    
    /**
     * Compiles the template into an internal function, eliminating the RegEx overhead
     */
    compile : function(){
        var fm = Ext.util.Format;
        var useF = this.disableFormats !== true;
        var sep = Ext.isGecko ? "+" : ",";
        var fn = function(m, name, format, args){
            if(format && useF){
                args = args ? ',' + args : "";
                if(format.substr(0, 5) != "this."){
                    format = "fm." + format + '(';
                }else{
                    format = 'this.call("'+ format.substr(5) + '", ';
                    args = "";
                }
            }else{
                args= '', format = "(values['" + name + "'] == undefined ? '' : ";
            }
            return "'"+ sep + format + "values['" + name + "']" + args + ")"+sep+"'";
        };
        var body;
        // branched to use + in gecko and [].join() in others
        if(Ext.isGecko){
            body = "this.compiled = function(values){ return '" +
                   this.html.replace(/(\r\n|\n)/g, '\\n').replace("'", "\\'").replace(this.re, fn) +
                    "';};";
        }else{
            body = ["this.compiled = function(values){ return ['"];
            body.push(this.html.replace(/(\r\n|\n)/g, '\\n').replace("'", "\\'").replace(this.re, fn));
            body.push("'].join('');};");
            body = body.join('');
        }
        eval(body);
        return this;
    },
    
    // private function used to call members
    call : function(fnName, value){
        return this[fnName](value);
    },
    
    /**
     * Applies the supplied values to the template and inserts the new node(s) as the first child of el
     * @param {String/HTMLElement/Element} el The context element
     * @param {Object} values The template values. Can be an array if your params are numeric (i.e. {0}) or an object (i.e. {foo: 'bar'})
     * @param {Boolean} returnElement (optional) true to return a Ext.Element
     * @return {HTMLElement} The new node
     */
    insertFirst: function(el, values, returnElement){
        return this.doInsert('afterBegin', el, values, returnElement);
    },

    /**
     * Applies the supplied values to the template and inserts the new node(s) before el
     * @param {String/HTMLElement/Element} el The context element
     * @param {Object} values The template values. Can be an array if your params are numeric (i.e. {0}) or an object (i.e. {foo: 'bar'})
     * @param {Boolean} returnElement (optional) true to return a Ext.Element
     * @return {HTMLElement} The new node
     */
    insertBefore: function(el, values, returnElement){
        return this.doInsert('beforeBegin', el, values, returnElement);
    },

    /**
     * Applies the supplied values to the template and inserts the new node(s) after el
     * @param {String/HTMLElement/Element} el The context element
     * @param {Object} values The template values. Can be an array if your params are numeric (i.e. {0}) or an object (i.e. {foo: 'bar'})
     * @param {Boolean} returnElement (optional) true to return a Ext.Element
     * @return {HTMLElement} The new node
     */
    insertAfter : function(el, values, returnElement){
        return this.doInsert('afterEnd', el, values, returnElement);
    },
    
    /**
     * Applies the supplied values to the template and append the new node(s) to el
     * @param {String/HTMLElement/Element} el The context element
     * @param {Object} values The template values. Can be an array if your params are numeric (i.e. {0}) or an object (i.e. {foo: 'bar'})
     * @param {Boolean} returnElement (optional) true to return a Ext.Element
     * @return {HTMLElement} The new node
     */
    append : function(el, values, returnElement){
        return this.doInsert('beforeEnd', el, values, returnElement);
    },

    doInsert : function(where, el, values, returnEl){
        el = Ext.getDom(el);
        var newNode = Ext.DomHelper.insertHtml(where, el, this.applyTemplate(values));
        return returnEl ? Ext.get(newNode, true) : newNode;
    },

    /**
     * Applies the supplied values to the template and overwrites the content of el with the new node(s)
     * @param {String/HTMLElement/Element} el The context element
     * @param {Object} values The template values. Can be an array if your params are numeric (i.e. {0}) or an object (i.e. {foo: 'bar'})
     * @param {Boolean} returnElement (optional) true to return a Ext.Element
     * @return {HTMLElement} The new node
     */
    overwrite : function(el, values, returnElement){
        el = Ext.getDom(el);
        el.innerHTML = this.applyTemplate(values);
        return returnElement ? Ext.get(el.firstChild, true) : el.firstChild;
    }
};
/**
 * Alias for applyTemplate
 * @method
 */
Ext.Template.prototype.apply = Ext.Template.prototype.applyTemplate;

// backwards compat
Ext.DomHelper.Template = Ext.Template;

/**
 * Creates a template from the passed element's value (display:none textarea, preferred) or innerHTML
 * @param {String/HTMLElement} el
 * @static
 */
Ext.Template.from = function(el){
    el = Ext.getDom(el);
    return new Ext.Template(el.value || el.innerHTML);  
};

/**
 * @class Ext.MasterTemplate
 * @extends Ext.Template
 * Provides a template that can have child templates. The syntax is:
<pre><code>
var t = new Ext.MasterTemplate(
	'&lt;select name="{name}"&gt;',
		'&lt;tpl name="options"&gt;&lt;option value="{value:trim}"&gt;{text:ellipsis(10)}&lt;/option&gt;&lt;/tpl&gt;',
	'&lt;/select&gt;'
);
t.add('options', {value: 'foo', text: 'bar'});
// or you can add multiple child elements in one shot
t.addAll('options', [
    {value: 'foo', text: 'bar'},
    {value: 'foo2', text: 'bar2'},
    {value: 'foo3', text: 'bar3'}
]);
// then append, applying the master template values
t.append('my-form', {name: 'my-select'});
</code></pre>
* A name attribute for the child template is not required if you have only one child 
* template or you want to refer to them by index.
 */
Ext.MasterTemplate = function(){
    Ext.MasterTemplate.superclass.constructor.apply(this, arguments);
    this.originalHtml = this.html;
    var st = {};
    var m, re = this.subTemplateRe;
    re.lastIndex = 0;
    var subIndex = 0;
    while(m = re.exec(this.html)){
        var name = m[1], content = m[2];
        st[subIndex] = {
            name: name,
            index: subIndex,
            buffer: [],
            tpl : new Ext.Template(content)
        };
        if(name){
            st[name] = st[subIndex];
        }
        st[subIndex].tpl.compile();
        st[subIndex].tpl.call = this.call.createDelegate(this);
        subIndex++;
    }
    this.subCount = subIndex;
    this.subs = st;
};
Ext.extend(Ext.MasterTemplate, Ext.Template, {
    /**
    * The regular expression used to match sub templates 
    * @type RegExp
    * @property 
    */
    subTemplateRe : /<tpl(?:\sname="([\w-]+)")?>((?:.|\n)*?)<\/tpl>/gi,
    
    /**
     * Applies the passed values to a child template.
     * @param {String/Number} name (optional) The name or index of the child template
     * @param {Array/Object} values The values to be applied to the template
     * @return {MasterTemplate} this
     */
     add : function(name, values){
        if(arguments.length == 1){
            values = arguments[0];
            name = 0;
        }
        var s = this.subs[name];
        s.buffer[s.buffer.length] = s.tpl.apply(values);
        return this;
    },
    
    /**
     * Applies all the passed values to a child template.
     * @param {String/Number} name (optional) The name or index of the child template
     * @param {Array} values The values to be applied to the template, this should be an array of objects.
     * @param {Boolean} reset (optional) True to reset the template first
     * @return {MasterTemplate} this
     */
    fill : function(name, values, reset){
        var a = arguments;
        if(a.length == 1 || (a.length == 2 && typeof a[1] == "boolean")){
            values = a[0];
            name = 0;
            reset = a[1];
        }
        if(reset){
            this.reset();
        }
        for(var i = 0, len = values.length; i < len; i++){
            this.add(name, values[i]);
        }
        return this;
    },
    
    /**
     * Resets the template for reuse
     * @return {MasterTemplate} this
     */
     reset : function(){
        var s = this.subs;
        for(var i = 0; i < this.subCount; i++){
            s[i].buffer = [];
        }
        return this;
    },
    
    applyTemplate : function(values){
        var s = this.subs;
        var replaceIndex = -1;
        this.html = this.originalHtml.replace(this.subTemplateRe, function(m, name){
            return s[++replaceIndex].buffer.join("");
        });
        return Ext.MasterTemplate.superclass.applyTemplate.call(this, values);
    },
    
    apply : function(){
        return this.applyTemplate.apply(this, arguments);
    },
    
    compile : function(){return this;}
});

/**
 * Alias for fill().
 * @method
 */
Ext.MasterTemplate.prototype.addAll = Ext.MasterTemplate.prototype.fill;
 /**
 * Creates a template from the passed element's value (display:none textarea, preferred) or innerHTML. e.g.
 * var tpl = Ext.MasterTemplate.from('element-id');
 * @param {String/HTMLElement} el
 * @static
 */
Ext.MasterTemplate.from = function(el){
    el = Ext.getDom(el);
    return new Ext.MasterTemplate(el.value || el.innerHTML);  
};