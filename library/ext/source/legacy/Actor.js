/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


/**
 * @class Ext.Actor
 * Provides support for syncing and chaining of Element Yahoo! UI based animation and some common effects. Actors support "self-play" without an Animator.<br><br>
 * <b>Note: Along with the animation methods defined below, this class inherits and captures all of the "set" or animation methods of {@link Ext.Element}. "get" methods are not captured and execute immediately.</b>
 * <br><br>Usage:<br>
 * <pre><code>
 * var actor = new Ext.Actor("myElementId");
 * actor.startCapture(true);
 * actor.moveTo(100, 100, true);
 * actor.squish();
 * actor.play();
 * <br>
 * // or to start capturing immediately, with no Animator (the null second param)
 * <br>
 * var actor = new Ext.Actor("myElementId", null, true);
 * actor.moveTo(100, 100, true);
 * actor.squish();
 * actor.play();
 * </code></pre>
 * @extends Ext.Element
 * @requires Ext.Element
 * @requires YAHOO.util.Dom
 * @requires YAHOO.util.Event
 * @requires YAHOO.util.CustomEvent 
 * @requires YAHOO.util.Anim
 * @requires YAHOO.util.ColorAnim
 * @requires YAHOO.util.Motion
 * @className Ext.Actor
 * @constructor
 * Create new Actor.
 * @param {String/HTMLElement} el The dom element or element id 
 * @param {Ext.Animator} animator (optional) The Animator that will capture this Actor's actions
 * @param {Boolean} selfCapture (optional) Whether this actor should capture its own actions to support self playback without an animator (defaults to false)
 */
Ext.Actor = function(element, animator, selfCapture){
    this.el = Ext.get(element); // cache el object for playback
    Ext.Actor.superclass.constructor.call(this, this.el.dom, true);
    this.onCapture = new Ext.util.Event();
    if(animator){
        /**
        * The animator used to sync this actor with other actors
        * @member Ext.Actor
        */
        animator.addActor(this);
    }
    /**
    * Whether this actor is currently capturing
    * @member Ext.Actor
    */
    this.capturing = selfCapture;
    this.playlist = selfCapture ? new Ext.Animator.AnimSequence() : null;
};

(function(){

/** @ignore */
var qa = function(method, animParam, onParam){
    return function(){
        if(!this.capturing){
            return method.apply(this, arguments);
        }
        var args = Array.prototype.slice.call(arguments, 0);
        if(args[animParam] === true){
            return this.capture(new Ext.Actor.AsyncAction(this, method, args, onParam));
        }else{
            return this.capture(new Ext.Actor.Action(this, method, args));
        }
    };
};

/** @ignore */
var q = function(method){
    return function(){
        if(!this.capturing){
            return method.apply(this, arguments);
        }
        var args = Array.prototype.slice.call(arguments, 0);
        return this.capture(new Ext.Actor.Action(this, method, args));
    };
};

var spr = Ext.Element.prototype;

Ext.extend(Ext.Actor, Ext.Element, {
    
    /**
     * Captures an action for this actor. Generally called internally but can be called directly.
     * @param {Ext.Actor.Action} action
     */
    capture : function(action){
        if(this.playlist != null){
            this.playlist.add(action);
        }
        this.onCapture.fire(this, action);
        return this;
    },
    // basic
    setVisibilityMode : q(spr.setVisibilityMode),
    enableDisplayMode : q(spr.enableDisplayMode),
    focus : q(spr.focus),
    addClass : q(spr.addClass),
    removeClass : q(spr.removeClass),
    replaceClass : q(spr.replaceClass),
    setStyle : q(spr.setStyle),
    setLeft : q(spr.setLeft),
    setTop : q(spr.setTop),
    clearPositioning : q(spr.clearPositioning),
    setPositioning : q(spr.setPositioning),
    clip : q(spr.clip),
    unclip : q(spr.unclip),
    clearOpacity : q(spr.clearOpacity),
    update : q(spr.update),
    remove : q(spr.remove),
    fitToParent : q(spr.fitToParent),
    appendChild : q(spr.appendChild),
    createChild : q(spr.createChild),
    appendTo : q(spr.appendTo),
    insertBefore : q(spr.insertBefore),
    insertAfter : q(spr.insertAfter),
    wrap : q(spr.wrap),
    replace : q(spr.replace),
    insertHtml : q(spr.insertHtml),
    set : q(spr.set),
    // anims
    setVisible : qa(spr.setVisible, 1, 3),
    toggle : qa(spr.toggle, 0, 2),
    setXY : qa(spr.setXY, 1, 3),
    setLocation : qa(spr.setLocation, 2, 4),
    setWidth : qa(spr.setWidth, 1, 3),
    setHeight : qa(spr.setHeight, 1, 3),
    setSize : qa(spr.setSize, 2, 4),
    setBounds : qa(spr.setBounds, 4, 6),
    setOpacity : qa(spr.setOpacity, 1, 3),
    moveTo : qa(spr.moveTo, 2, 4),
    move : qa(spr.move, 2, 4),
    alignTo : qa(spr.alignTo, 3, 5),
    hide : qa(spr.hide, 0, 2),
    show : qa(spr.show, 0, 2),
    setBox : qa(spr.setBox, 2, 4),
    autoHeight : qa(spr.autoHeight, 0, 2),
    setX : qa(spr.setX, 1, 3),
    setY : qa(spr.setY, 1, 3),
    
    load : function(){
       if(!this.capturing){
            return spr.load.apply(this, arguments);
       }
       var args = Array.prototype.slice.call(arguments, 0);
       return this.capture(new Ext.Actor.AsyncAction(this, spr.load, 
            args, 2));
    },
    
    animate : function(args, duration, onComplete, easing, animType){
        if(!this.capturing){
            return spr.animate.apply(this, arguments);
        }
        return this.capture(new Ext.Actor.AsyncAction(this, spr.animate, 
            [args, duration, onComplete, easing, animType], 2));
    },
    
    /**
     * Start self capturing calls on this Actor. All subsequent calls are captured and executed when play() is called.
     */
    startCapture : function(){
        this.capturing = true;
        this.playlist = new Ext.Animator.AnimSequence();
     },
     
     /**
     * Stop self capturing calls on this Actor.
     */
     stopCapture : function(){
         this.capturing = false;
     },
    
    /**
     * Clears any calls that have been self captured.
     */
    clear : function(){
        this.playlist = new Ext.Animator.AnimSequence();
    },
    
    /**
     * Starts playback of self captured calls.
     * @param {Function} oncomplete (optional) Callback to execute when playback has completed
     */
    play : function(oncomplete){
        this.capturing = false;
        if(this.playlist){
            this.playlist.play(oncomplete);
        }
    },
    /**
     * Stops the sequence if this actor is being used without an animator
     */
    stop : function(){
        if(this.playlist.isPlaying()){
            this.playlist.stop();
        }
    },
    /**
     * Returns true if this actor is animated and not part of an animator
     * @return {Boolean}
     */
    isPlaying : function(){
        return this.playlist.isPlaying();
    },
    /**
     * Capture a function call.
     * @param {Function} fcn The function to call
     * @param {Array} args (optional) The arguments to call the function with
     * @param {Object} scope (optional) The scope of the function
     */
    addCall : function(fcn, args, scope){
        if(!this.capturing){
            fcn.apply(scope || this, args || []);
        }else{
            this.capture(new Ext.Actor.Action(scope, fcn, args || []));
        }
    },
    
    /**
     * Capture an async function call.
     * @param {Function} fcn The function to call
     * @param {Number} callbackIndex The index of the callback parameter on the passed function. A CALLBACK IS REQUIRED.
     * @param {Array} args The arguments to call the function with
     * @param {Object} scope (optional) The scope of the function
     */
    addAsyncCall : function(fcn, callbackIndex, args, scope){
        if(!this.capturing){
            fcn.apply(scope || this, args || []);
        }else{
           this.capture(new Ext.Actor.AsyncAction(scope, fcn, args || [], callbackIndex));
        }
     },
     
    /**
     * Capture a pause (in seconds).
     * @param {Number} seconds The seconds to pause
     */
    pause : function(seconds){
        this.capture(new Ext.Actor.PauseAction(seconds));
     },
     
    /**
    * Shake this element from side to side
    */
    shake : function(){
        this.move("left", 20, true, .05);
        this.move("right", 40, true, .05);
        this.move("left", 40, true, .05);
        this.move("right", 20, true, .05);
    },
    
    /**
    * Bounce this element from up and down
    */
    bounce : function(){
        this.move("up", 20, true, .05);
        this.move("down", 40, true, .05);
        this.move("up", 40, true, .05);
        this.move("down", 20, true, .05);
    },
    
    /**
    * Show the element using a "blinds" effect
    * @param {String} anchor The part of the element that it should appear to exapand from. 
                            The short/long options currently are t/top, l/left
    * @param {Number} newSize (optional) The size to animate to. (Default to current size)
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    * @param {Function} easing (optional) YAHOO.util.Easing method to use. (Defaults to YAHOO.util.Easing.easeOut)
    */
    blindShow : function(anchor, newSize, duration, easing){
        var size = this.getSize();
        this.clip();
        anchor = anchor.toLowerCase();
        switch(anchor){
            case "t":
            case "top":
                this.setHeight(1);
                this.setVisible(true);
                this.setHeight(newSize || size.height, true, duration || .5, null, easing || YAHOO.util.Easing.easeOut);
            break;
            case "l":
            case "left":
                this.setWidth(1);
                this.setVisible(true);
                this.setWidth(newSize || size.width, true, duration || .5, null, easing || YAHOO.util.Easing.easeOut);
            break;
        }
        this.unclip();
        return size;
    },
    
    /**
    * Hide the element using a "blinds" effect
    * @param {String} anchor The part of the element that it should appear to collapse to.
                            The short/long options are t/top, l/left, b/bottom, r/right.
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    * @param {Function} easing (optional) YAHOO.util.Easing method to use. (Defaults to YAHOO.util.Easing.easeIn)
    */
    blindHide : function(anchor, duration, easing){
        var size = this.getSize();
        this.clip();
        anchor = anchor.toLowerCase();
        switch(anchor){
            case "t":
            case "top":
                this.setSize(size.width, 1, true, duration || .5, null, easing || YAHOO.util.Easing.easeIn);
                this.setVisible(false);
            break;
            case "l":
            case "left":
                this.setSize(1, size.height, true, duration || .5, null, easing || YAHOO.util.Easing.easeIn);
                this.setVisible(false);
            break;
            case "r":
            case "right":
                this.animate({width: {to: 1}, points: {by: [size.width, 0]}}, 
                duration || .5, null, YAHOO.util.Easing.easeIn, YAHOO.util.Motion);
                this.setVisible(false);
            break;
            case "b":
            case "bottom":
                this.animate({height: {to: 1}, points: {by: [0, size.height]}}, 
                duration || .5, null, YAHOO.util.Easing.easeIn, YAHOO.util.Motion);
                this.setVisible(false);
            break;
        }
        return size;
    },
    
    /**
    * Show the element using a "slide in" effect - In order for this effect to work the element MUST have a child element container that can be "slid" otherwise a blindShow effect is rendered. 
    * @param {String} anchor The part of the element that it should appear to slide from. 
                            The short/long options currently are t/top, l/left
    * @param {Number} newSize (optional) The size to animate to. (Default to current size)
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    * @param {Function} easing (optional) YAHOO.util.Easing method to use. (Defaults to YAHOO.util.Easing.easeOuth)
    */
    slideShow : function(anchor, newSize, duration, easing, clearPositioning){
        var size = this.getSize();
        this.clip();
        var firstChild = this.dom.firstChild;
        if(!firstChild || (firstChild.nodeName && "#TEXT" == firstChild.nodeName.toUpperCase())) { // can't do a slide with only a textnode
            this.blindShow(anchor, newSize, duration, easing);
            return;
        }
        var child = Ext.get(firstChild, true);
        var pos = child.getPositioning();
        this.addCall(child.position, ["absolute"], child);
        this.setVisible(true);
        anchor = anchor.toLowerCase();
        switch(anchor){
            case "t":
            case "top":
                this.addCall(child.setStyle, ["right", ""], child);
                this.addCall(child.setStyle, ["top", ""], child);
                this.addCall(child.setStyle, ["left", "0px"], child);
                this.addCall(child.setStyle, ["bottom", "0px"], child);
                this.setHeight(1);
                this.setHeight(newSize || size.height, true, duration || .5, null, easing || YAHOO.util.Easing.easeOut);
            break;
            case "l":
            case "left":
                this.addCall(child.setStyle, ["left", ""], child);
                this.addCall(child.setStyle, ["bottom", ""], child);
                this.addCall(child.setStyle, ["right", "0px"], child);
                this.addCall(child.setStyle, ["top", "0px"], child);
                this.setWidth(1);
                this.setWidth(newSize || size.width, true, duration || .5, null, easing || YAHOO.util.Easing.easeOut);
            break;
            case "r":
            case "right":
                this.addCall(child.setStyle, ["left", "0px"], child);
                this.addCall(child.setStyle, ["top", "0px"], child);
                this.addCall(child.setStyle, ["right", ""], child);
                this.addCall(child.setStyle, ["bottom", ""], child);
                this.setWidth(1);
                this.setWidth(newSize || size.width, true, duration || .5, null, easing || YAHOO.util.Easing.easeOut);
            break;
            case "b":
            case "bottom":
                this.addCall(child.setStyle, ["right", ""], child);
                this.addCall(child.setStyle, ["top", "0px"], child);
                this.addCall(child.setStyle, ["left", "0px"], child);
                this.addCall(child.setStyle, ["bottom", ""], child);
                this.setHeight(1);
                this.setHeight(newSize || size.height, true, duration || .5, null, easing || YAHOO.util.Easing.easeOut);
            break;
        }
        if(clearPositioning !== false){
          this.addCall(child.setPositioning, [pos], child);
        }
        this.unclip();
        return size;
    },
    
    /**
    * Hide the element using a "slide in" effect - In order for this effect to work the element MUST have a child element container that can be "slid" otherwise a blindHide effect is rendered. 
    * @param {String} anchor The part of the element that it should appear to slide to.
                            The short/long options are t/top, l/left, b/bottom, r/right.
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    * @param {Function} easing (optional) YAHOO.util.Easing method to use. (Defaults to YAHOO.util.Easing.easeIn)
    */
    slideHide : function(anchor, duration, easing){
        var size = this.getSize();
        this.clip();
        var firstChild = this.dom.firstChild;
        if(!firstChild || (firstChild.nodeName && "#TEXT" == firstChild.nodeName.toUpperCase())) { // can't do a slide with only a textnode
            this.blindHide(anchor, duration, easing);
            return;
        }
        var child = Ext.get(firstChild, true);
        var pos = child.getPositioning();
        this.addCall(child.position, ["absolute"], child);
        anchor = anchor.toLowerCase();
        switch(anchor){
            case "t":
            case "top":
                this.addCall(child.setStyle, ["right", ""], child);
                this.addCall(child.setStyle, ["top", ""], child);
                this.addCall(child.setStyle, ["left", "0px"], child);
                this.addCall(child.setStyle, ["bottom", "0px"], child);
                this.setSize(size.width, 1, true, duration || .5, null, easing || YAHOO.util.Easing.easeIn);
                this.setVisible(false);
            break;
            case "l":
            case "left":
                this.addCall(child.setStyle, ["left", ""], child);
                this.addCall(child.setStyle, ["bottom", ""], child);
                this.addCall(child.setStyle, ["right", "0px"], child);
                this.addCall(child.setStyle, ["top", "0px"], child);
                this.setSize(1, size.height, true, duration || .5, null, easing || YAHOO.util.Easing.easeIn);
                this.setVisible(false);
            break;
            case "r":
            case "right":
                this.addCall(child.setStyle, ["right", ""], child);
                this.addCall(child.setStyle, ["bottom", ""], child);
                this.addCall(child.setStyle, ["left", "0px"], child);
                this.addCall(child.setStyle, ["top", "0px"], child);
                this.setSize(1, size.height, true, duration || .5, null, easing || YAHOO.util.Easing.easeIn);
                this.setVisible(false);
            break;
            case "b":
            case "bottom":
                this.addCall(child.setStyle, ["right", ""], child);
                this.addCall(child.setStyle, ["top", "0px"], child);
                this.addCall(child.setStyle, ["left", "0px"], child);
                this.addCall(child.setStyle, ["bottom", ""], child);
                this.setSize(size.width, 1, true, duration || .5, null, easing || YAHOO.util.Easing.easeIn);
                this.setVisible(false);
            break;
        }
        this.addCall(child.setPositioning, [pos], child);
        return size;
    },
    
    /**
    * Hide the element by "squishing" it into the corner
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    */
    squish : function(duration){
        var size = this.getSize();
        this.clip();
        this.setSize(1, 1, true, duration || .5);
        this.setVisible(false);
        return size;
    },
    
    /**
    * Fade an element in
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    */
    appear : function(duration){
        this.setVisible(true, true, duration);
        return this;
    },
    
    /**
    * Fade an element out
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    */
    fade : function(duration){
        this.setVisible(false, true, duration);
        return this;
    },
    
    /**
    * Blink the element as if it was clicked and then collapse on its center
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    */
    switchOff : function(duration){
        this.clip();
        this.setOpacity(0.3, true, 0.1);
        this.clearOpacity();
        this.setVisible(true);
        this.pause(0.1);
        this.animate({height:{to:1}, points:{by:[0, this.getHeight() / 2]}}, duration || 0.3, null, YAHOO.util.Easing.easeIn, YAHOO.util.Motion);
        this.setVisible(false);
        return this;
    },
    
    /**
    * Fade the element in and out the specified amount of times
    * @param {Number} count (optional) How many times to pulse (Defaults to 3)
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    */
    pulsate : function(count, duration){
        count = count || 3;
        for(var i = 0; i < count; i++){
            this.toggle(true, duration || .25);
            this.toggle(true, duration || .25);
        }
        return this;
    },
    
    /**
    * Fade the element as it is falling from its current position
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    */
    dropOut : function(duration){
        this.animate({opacity: {to: 0}, points: {by: [0, this.getHeight()]}}, 
                duration || .5, null, YAHOO.util.Easing.easeIn, YAHOO.util.Motion);
        this.setVisible(false);
        return this;
    },
    
    /**
    * Hide the element in a way that it appears as if it is flying off the screen
    * @param {String} anchor The part of the page that the element should appear to move to. 
                            The short/long options are t/top, l/left, b/bottom, r/right, tl/top-left, 
                            tr/top-right, bl/bottom-left or br/bottom-right.
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    * @param {Function} easing (optional) YAHOO.util.Easing method to use. (Defaults to YAHOO.util.Easing.easeIn)
    */
    moveOut : function(anchor, duration, easing){
        var Y = YAHOO.util;
        var vw = Y.Dom.getViewportWidth();
        var vh = Y.Dom.getViewportHeight();
        var cpoints = this.getCenterXY();
        var centerX = cpoints[0];
        var centerY = cpoints[1];
        anchor = anchor.toLowerCase();
        var p;
        switch(anchor){
            case "t":
            case "top":
                p = [centerX, -this.getHeight()];
            break;
            case "l":
            case "left":
                p = [-this.getWidth(), centerY];
            break;
            case "r":
            case "right":
                p = [vw+this.getWidth(), centerY];
            break;
            case "b":
            case "bottom":
                p = [centerX, vh+this.getHeight()];
            break;
            case "tl":
            case "top-left":
                p = [-this.getWidth(), -this.getHeight()];
            break;
            case "bl":
            case "bottom-left":
                p = [-this.getWidth(), vh+this.getHeight()];
            break;
            case "br":
            case "bottom-right":
                p = [vw+this.getWidth(), vh+this.getHeight()];
            break;
            case "tr":
            case "top-right":
                p = [vw+this.getWidth(), -this.getHeight()];
            break;
        }
        this.moveTo(p[0], p[1], true, duration || .35, null, easing || Y.Easing.easeIn);
        this.setVisible(false);
        return this;
    },
    
    /**
    * Show the element in a way that it appears as if it is flying onto the screen
    * @param {String} anchor The part of the page that the element should appear to move from. 
                            The short/long options are t/top, l/left, b/bottom, r/right, tl/top-left, 
                            tr/top-right, bl/bottom-left or br/bottom-right.
    * @param {Array} to (optional) Array of x and y position to move to like [x, y] (Defaults to center screen)
    * @param {Float} duration (optional) How long the effect lasts (in seconds)
    * @param {Function} easing (optional) YAHOO.util.Easing method to use. (Defaults to YAHOO.util.Easing.easeOut)
    */
    moveIn : function(anchor, to, duration, easing){
        to = to || this.getCenterXY();
        this.moveOut(anchor, .01);
        this.setVisible(true);
        this.setXY(to, true, duration || .35, null, easing || YAHOO.util.Easing.easeOut);
        return this;
    },
    /**
    * Show a ripple of exploding, attenuating borders to draw attention to an Element.
    * @param {Number<i>} color (optional) The color of the border.
    * @param {Number} count (optional) How many ripples.
    * @param {Float} duration (optional) How long each ripple takes to expire
    */
    frame : function(color, count, duration){
        color = color || "red";
        count = count || 3;
        duration = duration || .5;
        var frameFn = function(callback){
            var box = this.getBox();
            var animFn = function(){ 
                var proxy = this.createProxy({
                     tag:"div",
                     style:{
                        visbility:"hidden",
                        position:"absolute",
                        "z-index":"35000", // yee haw
                        border:"0px solid " + color
                     }
                  });
                var scale = proxy.isBorderBox() ? 2 : 1;
                proxy.animate({
                    top:{from:box.y, to:box.y - 20},
                    left:{from:box.x, to:box.x - 20},
                    borderWidth:{from:0, to:10},
                    opacity:{from:1, to:0},
                    height:{from:box.height, to:(box.height + (20*scale))},
                    width:{from:box.width, to:(box.width + (20*scale))}
                }, duration, function(){
                    proxy.remove();
                });
                if(--count > 0){
                     animFn.defer((duration/2)*1000, this);
                }else{
                    if(typeof callback == "function"){
                        callback();
                    }
                }
           }
           animFn.call(this);
       }
       this.addAsyncCall(frameFn, 0, null, this);
       return this;
    }
});

})();

Ext.Actor.Action = function(actor, method, args){
      this.actor = actor;
      this.method = method;
      this.args = args;
  }
  
Ext.Actor.Action.prototype = {
    play : function(onComplete){
        this.method.apply(this.actor || window, this.args);
        onComplete();
    }  
};


Ext.Actor.AsyncAction = function(actor, method, args, onIndex){
    Ext.Actor.AsyncAction.superclass.constructor.call(this, actor, method, args);
    this.onIndex = onIndex;
    this.originalCallback = this.args[onIndex];
}
Ext.extend(Ext.Actor.AsyncAction, Ext.Actor.Action, {
    play : function(onComplete){
        var callbackArg = this.originalCallback ? 
                            this.originalCallback.createSequence(onComplete) : onComplete;
        this.args[this.onIndex] = callbackArg;
        this.method.apply(this.actor, this.args);
    }
});


Ext.Actor.PauseAction = function(seconds){
    this.seconds = seconds;
};
Ext.Actor.PauseAction.prototype = {
    play : function(onComplete){
        setTimeout(onComplete, this.seconds * 1000);
    }
};