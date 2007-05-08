/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.DatePicker
 * @extends Ext.Component
 * Simple date picker class.
 * @constructor
 * Create a new DatePicker
 * @param {Object} config The config object
 */
Ext.DatePicker = function(config){
    Ext.DatePicker.superclass.constructor.call(this, config);

    this.value = config && config.value ?
                 config.value.clearTime() : new Date().clearTime();

    this.addEvents({
        /**
	     * @event select
	     * Fires when a date is selected
	     * @param {DatePicker} this
	     * @param {Date} date The selected date
	     */
        select: true
    });

    if(this.handler){
        this.on("select", this.handler,  this.scope || this);
    }
    // build the disabledDatesRE
    if(!this.disabledDatesRE && this.disabledDates){
        var dd = this.disabledDates;
        var re = "(?:";
        for(var i = 0; i < dd.length; i++){
            re += dd[i];
            if(i != dd.length-1) re += "|";
        }
        this.disabledDatesRE = new RegExp(re + ")");
    }
};

Ext.extend(Ext.DatePicker, Ext.Component, {
    /**
     * @cfg {String} todayText
     * The text to display on the button that selects the current date (defaults to "Today")
     */
    todayText : "Today",
    /**
     * @cfg {String} todayTip
     * The tooltip to display for the button that selects the current date (defaults to "{current date} (Spacebar)")
     */
    todayTip : "{0} (Spacebar)",
    /**
     * @cfg {Date} minDate
     * Minimum allowable date (JavaScript date object, defaults to null)
     */
    minDate : null,
    /**
     * @cfg {Date} maxDate
     * Maximum allowable date (JavaScript date object, defaults to null)
     */
    maxDate : null,
    /**
     * @cfg {String} minText
     * The error text to display if the minDate validation fails (defaults to "This date is before the minimum date")
     */
    minText : "This date is before the minimum date",
    /**
     * @cfg {String} maxText
     * The error text to display if the maxDate validation fails (defaults to "This date is after the maximum date")
     */
    maxText : "This date is after the maximum date",
    /**
     * @cfg {String} format
     * The default date format string which can be overriden for localization support.  The format must be
     * valid according to {@link Date#parseDate} (defaults to 'm/d/y').
     */
    format : "m/d/y",
    /**
     * @cfg {Array} disabledDays
     * An array of days to disable, 0-based. For example, [0, 6] disables Sunday and Saturday (defaults to null).
     */
    disabledDays : null,
    /**
     * @cfg {String} disabledDaysText
     * The tooltip to display when the date falls on a disabled day (defaults to "")
     */
    disabledDaysText : "",
    /**
     * @cfg {RegExp} disabledDatesRE
     * JavaScript regular expression used to disable a pattern of dates (defaults to null)
     */
    disabledDatesRE : null,
    /**
     * @cfg {String} disabledDatesText
     * The tooltip text to display when the date falls on a disabled date (defaults to "")
     */
    disabledDatesText : "",
    /**
     * @cfg {Boolean} constrainToViewport
     * True to constrain the date picker to the viewport (defaults to true)
     */
    constrainToViewport : true,
    /**
     * @cfg {Array} monthNames
     * An array of textual month names which can be overriden for localization support (defaults to Date.monthNames)
     */
    monthNames : Date.monthNames,
    /**
     * @cfg {Array} dayNames
     * An array of textual day names which can be overriden for localization support (defaults to Date.dayNames)
     */
    dayNames : Date.dayNames,
    /**
     * @cfg {String} nextText
     * The next month navigation button tooltip (defaults to 'Next Month (Control+Right)')
     */
    nextText: 'Next Month (Control+Right)',
    /**
     * @cfg {String} prevText
     * The previous month navigation button tooltip (defaults to 'Previous Month (Control+Left)')
     */
    prevText: 'Previous Month (Control+Left)',
    /**
     * @cfg {String} monthYearText
     * The header month selector tooltip (defaults to 'Choose a month (Control+Up/Down to move years)')
     */
    monthYearText: 'Choose a month (Control+Up/Down to move years)',
    /**
     * @cfg {Number} startDay
     * Day index at which the week should begin, 0-based (defaults to 0, which is Sunday)
     */
    startDay : 0,

    /**
     * Sets the value of the date field
     * @param {Date} value The date to set
     */
    setValue : function(value){
        var old = this.value;
        this.value = value.clearTime(true);
        if(this.el){
            this.update(this.value);
        }
    },

    /**
     * Gets the current selected value of the date field
     * @return {Date} The selected date
     */
    getValue : function(){
        return this.value;
    },

    // private
    focus : function(){
        if(this.el){
            this.update(this.activeDate);
        }
    },

    // private
    onRender : function(container, position){
        var m = [
             '<table cellspacing="0">',
                '<tr><td class="x-date-left"><a href="#" title="', this.prevText ,'">&#160;</a></td><td class="x-date-middle" align="center"></td><td class="x-date-right"><a href="#" title="', this.nextText ,'">&#160;</a></td></tr>',
                '<tr><td colspan="3"><table class="x-date-inner" cellspacing="0"><thead><tr>'];
        var dn = this.dayNames;
        for(var i = 0; i < 7; i++){
            var d = this.startDay+i;
            if(d > 6){
                d = d-7;
            }
            m.push("<th><span>", dn[d].substr(0,1), "</span></th>");
        }
        m[m.length] = "</tr></thead><tbody><tr>";
        for(var i = 0; i < 42; i++) {
            if(i % 7 == 0 && i != 0){
                m[m.length] = "</tr><tr>";
            }
            m[m.length] = '<td><a href="#" hidefocus="on" class="x-date-date" tabIndex="1"><em><span></span></em></a></td>';
        }
        m[m.length] = '</tr></tbody></table></td></tr><tr><td colspan="3" class="x-date-bottom" align="center"></td></tr></table>';

        var el = document.createElement("div");
        el.className = "x-date-picker";
        el.innerHTML = m.join("");

        container.dom.insertBefore(el, position);

        this.el = Ext.get(el);
        new Ext.util.ClickRepeater(this.el.child("td.x-date-left a"), {handler: this.showPrevMonth, scope: this});
        new Ext.util.ClickRepeater(this.el.child("td.x-date-right a"), {handler: this.showNextMonth, scope: this});

        this.el.on("mousewheel", this.handleMouseWheel,  this);


        var kn = new Ext.KeyNav(this.el, {
            "left" : function(e){
                e.ctrlKey ?
                    this.showPrevMonth() :
                    this.update(this.activeDate.add("d", -1));
            },

            "right" : function(e){
                e.ctrlKey ?
                    this.showNextMonth() :
                    this.update(this.activeDate.add("d", 1));
            },

            "up" : function(e){
                e.ctrlKey ?
                    this.showNextYear() :
                    this.update(this.activeDate.add("d", -7));
            },

            "down" : function(e){
                e.ctrlKey ?
                    this.showPrevYear() :
                    this.update(this.activeDate.add("d", 7));
            },

            "pageUp" : function(e){
                this.showNextMonth();
            },

            "pageDown" : function(e){
                this.showPrevMonth();
            },

            "enter" : function(e){
                e.stopPropagation();
                return true;
            },

            scope : this
        });

        this.el.on("click", this.handleDateClick,  this, {delegate: "a.x-date-date"});

        this.el.addKeyListener(Ext.EventObject.SPACE, this.selectToday,  this);

        this.el.unselectable();
        
        this.cells = this.el.select("table.x-date-inner tbody td");
        this.textNodes = this.el.query("table.x-date-inner tbody span");

        var mmenu = new Ext.menu.Menu({
            plain:true,
            cls: "x-date-mmenu",
            allowOtherMenus : true
        });

        var menuGroup = Ext.id()+"months";
        for(var i = 0; i < 12; i++){
            mmenu.add(new Ext.menu.CheckItem({
                id: "mm-"+i,
                text: this.monthNames[i],
                group:menuGroup,
                month: i
            }));
        }

        mmenu.on({
            "beforeshow" : function(){
                mmenu.items.get("mm-"+(this.activeDate || this.value).getMonth()).setChecked(true);
            },
            "itemclick" : function(item){
                var d = (this.activeDate || this.value).clone();
                d.setMonth(item.month);
                this.update(d);
            },
            "show" : function(m){
                this.visibleRegion = m.el.getRegion().adjust(2, 2, -2, -2);
            },
            "mouseout" : function(m, e){
                if(!this.visibleRegion.contains(e.getPoint())){
                    m.hide();
                }
            },
            scope: this
        });

        this.mbtn = new Ext.Button(this.el.child("td.x-date-middle", true), {
            menu: mmenu,
            text: "&#160;",
            menuAlign: "c-c?",
            tooltip: this.monthYearText
        });

        var today = (new Date()).dateFormat(this.format);
        var todayBtn = new Ext.Button(this.el.child("td.x-date-bottom", true), {
            text: String.format(this.todayText, today),
            tooltip: String.format(this.todayTip, today),
            handler: this.selectToday,
            scope: this
        });
        
        if(Ext.isIE){
            this.el.repaint();
        }
        this.update(this.value);
    },

    // private
    showPrevMonth : function(e){
        this.update(this.activeDate.add("mo", -1));
    },

    // private
    showNextMonth : function(e){
        this.update(this.activeDate.add("mo", 1));
    },

    // private
    showPrevYear : function(){
        this.update(this.activeDate.add("y", -1));
    },

    // private
    showNextYear : function(){
        this.update(this.activeDate.add("y", 1));
    },

    // private
    handleMouseWheel : function(e){
        var delta = e.getWheelDelta();
        if(delta > 0){
            this.showPrevMonth();
            e.stopEvent();
        } else if(delta < 0){
            this.showNextMonth();
            e.stopEvent();
        }
    },

    // private
    handleDateClick : function(e, t){
        e.stopEvent();
        if(t.dateValue && !Ext.fly(t.parentNode).hasClass("x-date-disabled")){
            this.setValue(new Date(t.dateValue));
            this.fireEvent("select", this, this.value);
        }
    },

    // private
    selectToday : function(){
        this.setValue(new Date().clearTime());
        this.fireEvent("select", this, this.value);
    },

    // private
    update : function(date){
        var vd = this.activeDate;
        this.activeDate = date;

        if(vd && this.el){
            var t = date.getTime();
            if(vd.getMonth() == date.getMonth() && vd.getFullYear() == date.getFullYear()){
                this.cells.removeClass("x-date-selected");
                this.cells.each(function(c){
                   if(c.dom.firstChild.dateValue == t){
                       c.addClass("x-date-selected");
                       setTimeout(function(){
                            try{c.dom.firstChild.focus();}catch(e){}
                       }, 50);
                       return false;
                   }
                });
                return;
            }
        }
        var days = date.getDaysInMonth();
        var firstOfMonth = date.getFirstDateOfMonth();
        var startingPos = firstOfMonth.getDay()-this.startDay;

        if(startingPos <= this.startDay){
            startingPos += 7;
        }

        var pm = date.add("mo", -1);
        var prevStart = pm.getDaysInMonth()-startingPos;

        var cells = this.cells.elements;
        var textEls = this.textNodes;
        days += startingPos;

        // convert everything to numbers so it's fast
        var day = 86400000;
        var d = (new Date(pm.getFullYear(), pm.getMonth(), prevStart)).clearTime();
        var today = new Date().clearTime().getTime();
        var sel = date.clearTime().getTime();
        var min = this.minDate ? this.minDate.clearTime() : Number.NEGATIVE_INFINITY;
        var max = this.maxDate ? this.maxDate.clearTime() : Number.POSITIVE_INFINITY;
        var ddMatch = this.disabledDatesRE;
        var ddText = this.disabledDatesText;
        var ddays = this.disabledDays ? this.disabledDays.join("") : false;
        var ddaysText = this.disabledDaysText;
        var format = this.format;

        var setCellClass = function(cal, cell){
            cell.title = "";
            var t = d.getTime();
            cell.firstChild.dateValue = t;
            if(t == today){
                cell.className += " x-date-today";
                cell.title = cal.todayText;
            }
            if(t == sel){
                cell.className += " x-date-selected";
                setTimeout(function(){
                    try{cell.firstChild.focus();}catch(e){}
                }, 50);
            }
            // disabling
            if(t < min) {
                cell.className = " x-date-disabled";
                cell.title = cal.minText;
                return;
            }
            if(t > max) {
                cell.className = " x-date-disabled";
                cell.title = cal.maxText;
                return;
            }
            if(ddays){
                if(ddays.indexOf(d.getDay()) != -1){
                    cell.title = ddaysText;
                    cell.className = " x-date-disabled";
                }
            }
            if(ddMatch && format){
                var fvalue = d.dateFormat(format);
                if(ddMatch.test(fvalue)){
                    cell.title = ddText.replace("%0", fvalue);
                    cell.className = " x-date-disabled";
                }
            }
        };

        var i = 0;
        for(; i < startingPos; i++) {
            textEls[i].innerHTML = (++prevStart);
            d.setDate(d.getDate()+1);
            cells[i].className = "x-date-prevday";
            setCellClass(this, cells[i]);
        }
        for(; i < days; i++){
            intDay = i - startingPos + 1;
            textEls[i].innerHTML = (intDay);
            d.setDate(d.getDate()+1);
            cells[i].className = "x-date-active";
            setCellClass(this, cells[i]);
        }
        var extraDays = 0;
        for(; i < 42; i++) {
             textEls[i].innerHTML = (++extraDays);
             d.setDate(d.getDate()+1);
             cells[i].className = "x-date-nextday";
             setCellClass(this, cells[i]);
        }

        this.mbtn.setText(this.monthNames[date.getMonth()] + " " + date.getFullYear());

        if(!this.internalRender){
            var main = this.el.dom.firstChild;
            var w = main.offsetWidth;
            this.el.setWidth(w + this.el.getBorderWidth("lr"));
            Ext.fly(main).setWidth(w);
            this.internalRender = true;
            // opera does not respect the auto grow header center column
            // then, after it gets a width opera refuses to recalculate
            // without a second pass
            if(Ext.isOpera && !this.secondPass){
                main.rows[0].cells[1].style.width = (w - (main.rows[0].cells[0].offsetWidth+main.rows[0].cells[2].offsetWidth)) + "px";
                this.secondPass = true;
                this.update.defer(10, this, [date]);
            }
        }
    }
});