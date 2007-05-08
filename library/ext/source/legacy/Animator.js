/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.Animator
 * Provides support for syncing animations for multiple {@link Ext.Actor}s.<br><br>
* <br><br>This example can be seen in action <a href="http://www.jackslocum.com/yui/2006/08/19/a-splitbar-component-for-yahoo-ui/" target="_new">here</a> 
* by clicking on "Click here and I will point it out" at the end of the first paragraph.<br>
 * <pre><code>
var animator = new Ext.Animator();
var cursor = new Ext.Actor("cursor-img", animator);
var click = new Ext.Actor("click-img", animator);
var resize = new Ext.Actor("resize-img", animator);

// start capturing
animator.startCapture();

// these animations will be run in sequence
cursor.show();
cursor.moveTo(500,400);
cursor.moveTo(20, Ext.get("navbar").getY()+10, true, .75);
click.show();
click.alignTo(cursor, "tl", [-4, -4]);

// Add an async function call, pass callback to argument 1
animator.addAsyncCall(Blog.navbar.undockDelegate, 1);

// pause .5 seconds
animator.pause(.5);

// again, these animations will be run in sequence
click.hide(true, .7);
cursor.alignTo("splitter", "tr", [0, +100], true, 1);
resize.alignTo("splitter", "tr", [-12, +100]);

// start sync block: these animations will run at the same time
animator.beginSync();
cursor.hide();
resize.show();
animator.endSync();

// play the captured animation sequences, call myCallback when done
animator.play(myCallback);
 * </code></pre>
 * @requires Ext.Element
 * @requires YAHOO.util.Dom
 * @requires YAHOO.util.Event
 * @requires YAHOO.util.CustomEvent 
 * @requires YAHOO.util.Anim
 * @requires YAHOO.util.ColorAnim
 * @requires YAHOO.util.Motion
 * @constructor
 * @param {String/HTMLElement} el The dom element or element id 
 * @param {Ext.Animator} animator (optional) The Animator that will capture this Actor's actions
 * @param {Boolean} selfCapture (optional) Whether this actor should capture its own actions to support self playback without an animator (defaults to false)
 */ 
 Ext.Animator = function(/*Actors...*/){
    this.actors = [];
    this.playlist = new Ext.Animator.AnimSequence();
    this.captureDelegate = this.capture.createDelegate(this);
    this.playDelegate = this.play.createDelegate(this);
    this.syncing = false;
    this.stopping = false;
    this.playing = false;
    for(var i = 0; i < arguments.length; i++){
        this.addActor(arguments[i]);
    }
 };
 
 Ext.Animator.prototype = {
 
     capture : function(actor, action){
        if(this.syncing){
            if(!this.syncMap[actor.id]){
                this.syncMap[actor.id] = new Ext.Animator.AnimSequence();
            }
            this.syncMap[actor.id].add(action);
        }else{
            this.playlist.add(action);
        }
    },
    
    /**
      * Add an actor. The actor is also set to capturing = true.
      * @param {Ext.Actor} actor
      */
     addActor : function(actor){
        actor.onCapture.addListener(this.captureDelegate);
        this.actors.push(actor);
    },
    
    
    /**
      * Start capturing actions on the added actors. 
      * @param {Boolean} clearPlaylist Whether to also create a new playlist
      */
     startCapture : function(clearPlaylist){
        for(var i = 0; i < this.actors.length; i++){
            var a = this.actors[i];
            if(!this.isCapturing(a)){
                a.onCapture.addListener(this.captureDelegate);
            }
            a.capturing = true;
        }
        if(clearPlaylist){
            this.playlist = new Ext.Animator.AnimSequence();
        }
     },
     
     /**
      * Checks whether this animator is listening to a specific actor.
      * @param {Ext.Actor} actor
      */
     isCapturing : function(actor){
        return actor.onCapture.isListening(this.captureDelegate);
     },
     
     /**
      * Stop capturing on all added actors.
      */
     stopCapture : function(){
         for(var i = 0; i < this.actors.length; i++){
            var a = this.actors[i];
            a.onCapture.removeListener(this.captureDelegate);
            a.capturing = false;
         }
     },
     
     /**
     * Start a multi-actor sync block. By default all animations are run in sequence. While in the sync block
     * each actor's own animations will still be sequenced, but all actors will animate at the same time. 
     */
    beginSync : function(){
        this.syncing = true;
        this.syncMap = {};
     },
     
     /**
     * End the multi-actor sync block
     */
    endSync : function(){
         this.syncing = false;
         var composite = new Ext.Animator.CompositeSequence();
         for(key in this.syncMap){
             if(typeof this.syncMap[key] != "function"){
                composite.add(this.syncMap[key]);
             }
         }
         this.playlist.add(composite);
         this.syncMap = null;
     },
     
    /**
     * Starts playback of the playlist, also stops any capturing. To start capturing again call {@link #startCapture}.
     * @param {Function} oncomplete (optional) Callback to execute when playback has completed
     */
    play : function(oncomplete){
        if(this.playing) return; // can't play the same animation twice at once
        this.stopCapture();
        this.playlist.play(oncomplete);
    },
    
    /**
     * Stop at the next available stopping point
     */
    stop : function(){
        this.playlist.stop();
    },
    
    /**
     * Check if this animator is currently playing
     */
    isPlaying : function(){
        return this.playlist.isPlaying();
    },
    /**
     * Clear the playlist
     */
    clear : function(){
        this.playlist = new Ext.Animator.AnimSequence();
     },
     
    /**
     * Add a function call to the playlist.
     * @param {Function} fcn The function to call
     * @param {Array} args The arguments to call the function with
     * @param {Object} scope (optional) The scope of the function
     */
     addCall : function(fcn, args, scope){
        this.playlist.add(new Ext.Actor.Action(scope, fcn, args || []));
     },
     
     /**
     * Add an async function call to the playlist.
     * @param {Function} fcn The function to call
     * @param {Number} callbackIndex The index of the callback parameter on the passed function. A CALLBACK IS REQUIRED.
     * @param {Array} args The arguments to call the function with
     * @param {Object} scope (optional) The scope of the function
     */
    addAsyncCall : function(fcn, callbackIndex, args, scope){
        this.playlist.add(new Ext.Actor.AsyncAction(scope, fcn, args || [], callbackIndex));
     },
     
     /**
     * Add a pause to the playlist (in seconds)
     * @param {Number} seconds The number of seconds to pause.
     */
    pause : function(seconds){
        this.playlist.add(new Ext.Actor.PauseAction(seconds));
     }
     
  };
/**
 * Static function to build a AnimatorComposite from a css selector (requires Ext.Element.selectorFunction be defined)
 * @param {String/Array} selector The css selector or an array of nodes to animate
 * @method @static
 */
Ext.Animator.select = function(selector){
    var els;
    if(typeof selector == "string"){
        els = Ext.Element.selectorFunction(selector);
    }else if(selector instanceof Array){
        els = selector;
    }else{
        throw "Invalid selector";
    }
    return new Ext.AnimatorComposite(els);
};
//var getActors = Ext.Animator.select;
Ext.actors = Ext.Animator.select;

/**
 * @class Ext.AnimatorComposite
 * Composite class with synchronized animations. This is the class returned by getActors(selector) or Ext.Animator.select().
 */
Ext.AnimatorComposite = function(els){
    this.animator = new Ext.Animator();
    this.addElements(els);
    this.syncAnims = true;
};
Ext.AnimatorComposite.prototype = {
    isComposite: true,
    /**
    * Adds elements to this composite.
    * @param {Array} els An array of elements to add
    * @return {AnimatorComposite} this
    */
    addElements : function(els){
        if(!els) return this;
        var anim = this.animator;
        for(var i = 0, len = els.length; i < len; i++) {
        	anim.addActor(new Ext.Actor(els[i]));
        }
        anim.startCapture();
        return this;
    },
    /**
    * Operations called after sequence() will be performed one by one on each element in this composite.
    * @return {AnimatorComposite} this
    */
    sequence : function(){
        this.syncAnims = false;
        return this;
    },
    /**
    * Operations called after sync() will be performed at the same time on each element in this composite.
    * @return {AnimatorComposite} this
    */
    sync : function(){
        this.syncAnims = true;
        return this;
    },
    invoke : function(fn, args){
        var els = this.animator.actors;
        if(this.syncAnims) this.animator.beginSync();
        for(var i = 0, len = els.length; i < len; i++) {
            Ext.Actor.prototype[fn].apply(els[i], args);
        }
        if(this.syncAnims) this.animator.endSync();
        return this;
    },
    /**
    * Play the actions queued in this composite.
    * @param {Function} callback (optional) callback is called when all animations have compelted
    * @return {AnimatorComposite} this
    */
    play : function(callback){
        this.animator.play(callback);
        return this;
    },
    /**
    * Clear all actions in the queue.
    * @param {Function} callback (optional) callback is called when all animations have compelted
    * @return {AnimatorComposite} this
    */
    reset : function(callback){
        this.animator.startCapture(true);
        return this;
    },
    /**
     * Add a pause
     * @param {Number} seconds
    * @return {AnimatorComposite} this
     */
    pause : function(seconds){
        this.animator.pause(seconds);
        return this;
    },
    /**
    * Get the Ext.Animator that controls the animations for this composite.
    * @return {Ext.Animator}
    */
    getAnimator : function(){
        return this.animator;
    },
    /**
    * Calls the passed function passing (el, this, index) for each element in this composite.
    * @param {Function} fn The function to call
    * @param {Object} scope (optional) The <i>this</i> object (defaults to the element)
    * @return {AnimatorComposite} this
    */
    each : function(fn, scope){
        var els = this.animator.actors;
        if(this.syncAnims) this.animator.beginSync();
        for(var i = 0, len = els.length; i < len; i++){
            fn.call(scope || els[i], els[i], this, i);
        }
        if(this.syncAnims) this.animator.endSync();
        return this;
    },
    /**
     * Add a function call to the playlist.
     * @param {Function} fcn The function to call
     * @param {Array} args (optional) The arguments to call the function with
     * @param {Object} scope (optional) The scope of the function
     * @return {AnimatorComposite} this
     */
     addCall : function(fcn, args, scope){
        this.animator.addCall(fcn, args, scope);
        return this;
    },
    /**
     * Add an async function call to the playlist.
     * @param {Function} fcn The function to call
     * @param {Number} callbackIndex The index of the callback parameter on the passed function. <b>A CALLBACK IS REQUIRED</b>.
     * @param {Array} args (optional) The arguments to call the function with
     * @param {Object} scope (optional) The scope of the function
     * @return {AnimatorComposite} this
    */
    addAsyncCall : function(fcn, callbackIndex, args, scope){
        this.animator.addAsyncCall(fcn, callbackIndex, args, scope);
        return this;
    }
};
for(var fnName in Ext.Actor.prototype){
    if(typeof Ext.Actor.prototype[fnName] == "function"){
        Ext.CompositeElement.createCall(Ext.AnimatorComposite.prototype, fnName);
    }
}


Ext.Animator.AnimSequence = function(){
    this.actions = [];
    this.nextDelegate = this.next.createDelegate(this);
    this.playDelegate = this.play.createDelegate(this);
    this.oncomplete = null;
    this.playing = false;
    this.stopping = false;
    this.actionIndex = -1;
 };
 
 Ext.Animator.AnimSequence.prototype = {
 
    add : function(action){
        this.actions.push(action);
    },
    
    next : function(){
        if(this.stopping){
            this.playing = false;
            if(this.oncomplete){
                this.oncomplete(this, false);
            }
            return;
        }
        var nextAction = this.actions[++this.actionIndex];
        if(nextAction){
            nextAction.play(this.nextDelegate);
        }else{
            this.playing = false;
            if(this.oncomplete){
                this.oncomplete(this, true);
            }
        }
    },
    
    play : function(oncomplete){
        if(this.playing) return; // can't play the same sequence twice at once
        this.oncomplete = oncomplete;
        this.stopping = false;
        this.playing = true;
        this.actionIndex = -1;
        this.next();
    },
    
    stop : function(){
        this.stopping = true;
    },
    
    isPlaying : function(){
        return this.playing;
    },
    
    clear : function(){
        this.actions = [];
    },
     
    addCall : function(fcn, args, scope){
        this.actions.push(new Ext.Actor.Action(scope, fcn, args || []));
     },
     
     addAsyncCall : function(fcn, callbackIndex, args, scope){
        this.actions.push(new Ext.Actor.AsyncAction(scope, fcn, args || [], callbackIndex));
     },
     
     pause : function(seconds){
        this.actions.push(new Ext.Actor.PauseAction(seconds));
     }
     
  };

Ext.Animator.CompositeSequence = function(){
    this.sequences = [];
    this.completed = 0;
    this.trackDelegate = this.trackCompletion.createDelegate(this);
};

Ext.Animator.CompositeSequence.prototype = {
    add : function(sequence){
        this.sequences.push(sequence);
    },
    
    play : function(onComplete){
        this.completed = 0;
        if(this.sequences.length < 1){
            if(onComplete)onComplete();
            return;
        }
        this.onComplete = onComplete;
        for(var i = 0; i < this.sequences.length; i++){
            this.sequences[i].play(this.trackDelegate);
        }
    },
    
    trackCompletion : function(){
        ++this.completed;
        if(this.completed >= this.sequences.length && this.onComplete){
            this.onComplete();
        }
    },
    
    stop : function(){
        for(var i = 0; i < this.sequences.length; i++){
            this.sequences[i].stop();
        }
    },
    
    isPlaying : function(){
        for(var i = 0; i < this.sequences.length; i++){
            if(this.sequences[i].isPlaying()){
                return true;
            }
        }
        return false;
    }
};


