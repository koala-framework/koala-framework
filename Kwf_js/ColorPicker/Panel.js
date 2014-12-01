Kwf.ColorPicker.Radio = {
    groups: {},
    callbacks: {},

    registerGroup: function(group)
    {
        if(!group.radioGroupId) {
            group.radioGroupId = Ext2.id();
        }
        if(!this.groups[group.radioGroupId]) {
            this.groups[group.radioGroupId] = {
                dialed: null,
                members: []
            };
        }
    },

    registerMember: function(group, member, callback)
    {
        this.registerGroup(group);
        if(!member.radioGroupMemberId) {
            member.radioGroupMemberId = Ext2.id();
        }
        if (this.groups[group.radioGroupId].members.indexOf(member.radioGroupMemberId) === -1) {
            this.groups[group.radioGroupId].members.push(member.radioGroupMemberId);
            this.callbacks[member.radioGroupMemberId] = callback;
        }
    },

    unregisterMember: function(group, member)
    {
        var members = this.groups[group.radioGroupId].members;
        Ext2.each(members, function(member, i)
        {
            if(member === member.radioGroupMember.id) {
                members = members.splice(i, 1);
            }
        });
    },

    dial: function(group, member)
    {
        this.groups[group.radioGroupId].dialed = member.radioGroupMemberId;
        Ext2.each(this.groups[group.radioGroupId].members, function(member)
        {
            this.callbacks[member].un();
        }, this);
        this.callbacks[member.radioGroupMemberId].dial();
    },

    dialed: function(group, member)
    {
        return (this.groups[group.radioGroupId].dialed === member.radioGroupMemberId);
    }
};

/*
     This project began as a port of a Color Picker written by:
      Copyright (c) 2007 John Dyer (http://johndyer.name)

    Permission is hereby granted, free of charge, to any person
    obtaining a copy of this software and associated documentation
    files (the "Software"), to deal in the Software without
    restriction, including without limitation the rights to use,
    copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the
    Software is furnished to do so, subject to the following
    conditions:

    The above copyright notice and this permission notice shall be
    included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
    OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
    HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
    WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
    FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
    OTHER DEALINGS IN THE SOFTWARE.
*/

/**
 * @class Ext2.ux.color.ColorPickerPanel
 * Experimental Color class. Supports and converts between RGB, HSV and HEX
 * @cfg {Object} hex A hexidecimal value to start the colorpicker with.
 * @constructor
 * Create a new ColorPanel.
 * @param {Object} config The config object
 */

Kwf.ColorPicker.Panel = function()
{
    this.modeFields = {};
    this.preloads = [];
    Kwf.Form.ColorPicker.Panel.superclass.constructor.apply(this, arguments);
};

Ext2.extend(Kwf.ColorPicker.Panel, Ext2.Panel,
{
    width: 400,
    height: 305,
    hex: "FF0000",

    mode: 'saturation',

    baseCls: 'x2-color-picker x2-panel',
    iconCls: 'x2-color-wheel',


    images:
    [
        'bar-blue-bl',
        'bar-blue-br',
        'bar-blue-tl',
        'bar-blue-tr',
        'bar-brightness',
        'bar-green-bl',
        'bar-green-br',
        'bar-green-tl',
        'bar-green-tr',
        'bar-hue',
        'bar-red-bl',
        'bar-red-br',
        'bar-red-tl',
        'bar-red-tr',
        'bar-saturation',
        'map-blue-max',
        'map-blue-min',
        'map-brightness',
        'map-green-max',
        'map-green-min',
        'map-hue',
        'map-red-max',
        'map-saturation',
        'map-saturation-overlay'
    ],

    modes: {
        red: {
            name: 'red',
            abbr: 'R',
            min: 0,
            max: 255
        },
        green: {
            name: 'green',
            abbr: 'G',
            min: 0,
            max: 255
        },
        blue: {
            name: 'blue',
            abbr: 'B',
            min: 0,
            max: 255
        },
        hue: {
            name: 'hue',
            abbr: 'H',
            min: 0,
            max: 359,
            unit: '�'
        },
        saturation: {
            name: 'saturation',
            abbr: 'S',
            min: 0,
            max: 100,
            unit: '%'
        },
        brightness: {
            name: 'brightness',
            abbr: 'V',
            min: 0, max: 100,
            unit: '%'
        }
    },

    write: function(mode, value)
    {
        var field = this.modeFields[mode.name],
            val = parseInt(value, 10);

        val = val.constrain(mode.min, mode.max);
        field.setRawValue(val.toString().replace(new RegExp(mode.unit||''), '') + (mode.unit||''));
    },

    isRgb:function(mode)
    {
         return !('red green blue rgb'.indexOf(mode.name) === -1);
    },
    isHsv: function(mode)
    {
        return !('hue saturation brightness hsv'.indexOf(mode.name) === -1);
    },

    setFromConsole: function(mode) {
        if (this.isRgb({name:mode})) {
            this.color.setRgb({
                red: this.modeFields.red.getValue(),
                green: this.modeFields.green.getValue(),
                blue: this.modeFields.blue.getValue()
            });
            this.writeToConsole('Hsv');
        }
        else {
            this.color.setHsv({
                hue: this.modeFields.hue.getValue(),
                saturation: this.modeFields.saturation.getValue(),
                brightness: this.modeFields.brightness.getValue()
            });
            this.writeToConsole('Rgb');
        }
    },

    setFromTrack: function(y) {
        switch(this.mode) {
            case 'hue':
                this.write(this.modes.hue, 360 - (y/255) * 360);
                break;
            case 'saturation':
                this.write(this.modes.saturation, 100 - (y/255) * 100);
                break;
            case 'brightness':
                this.write(this.modes.brightness, 100 - (y / 255) * 100);
                break;

            case 'red':
                this.write(this.modes.red, 255 - (y/255) * 255);
                break;
            case 'green':
                this.write(this.modes.green, 255 - (y/255) * 255);
                break;
            case 'blue':
                this.write(this.modes.blue, 255 - (y/255) * 255);
                break;
        }

        switch(this.mode) {
            case 'hue':
            case 'saturation':
            case 'brightness':
                this.setFromConsole('hsv');
                break;

            case 'red':
            case 'green':
            case 'blue':
                this.setFromConsole('rgb');
                break;
        }

        this.paint();
    },

    setFromMap: function(x, y) {
        switch(this.mode) {
            case 'hue':
                this.write(this.modes.saturation, (x/255)*100);
                this.write(this.modes.brightness, 100 - ((y/255)*100));
                break;

            case 'saturation':
                this.write(this.modes.hue, (x/255)*360);
                this.write(this.modes.brightness, 100 - ((y/255)*100));
                break;

            case 'brightness':
                this.write(this.modes.hue, (x/255)*360);
                this.write(this.modes.saturation, 100 - ((y/255)*100));
                break;

            case 'red':
                this.write(this.modes.blue, x);
                this.write(this.modes.green, 255 - y);
                break;

            case 'green':
                this.write(this.modes.blue, x);
                this.write(this.modes.red, 255 - y);
                break;

            case 'blue':
                this.write(this.modes.red, x);
                this.write(this.modes.green, 255 - y);
                break;
        }

        switch(this.mode) {
            case 'hue':
            case 'saturation':
            case 'brightness':
                this.setFromConsole('hsv');
                break;

            case 'red':
            case 'green':
            case 'blue':
                this.setFromConsole('rgb');
                break;
        }

        this.paint();
    },

    writeToConsole: function(group) {
        var slot, mode;
        for(slot in this.modes) {
            mode = this.modes[slot];
            if ((group && this['is' + group](mode)) || !group) {
                this.write(mode, this.color[mode.name]);
            }
        }

        this.hex.setValue(this.color.hex);
    },

    setMode: function(name)
    {
        this.mode = name;
        Ext2.each(this.layers.map.concat(this.layers.track), function(layer)
        {
            this.setClass(layer, 'x2-layer');
            this.setAlpha(layer, 100);
            this.setBackground(layer, null);
        }, this);
        this[this.mode+"Mode"]();
        this.paint();
        this.paintSliders();
    },

    hueMode: function() {
        this.setBackground(this.layers.map[0], this.color.hex);

        // add a hue map on the top
        this.setClass(this.layers.map[1], "map-hue");

        // simple hue bar
        this.setClass(this.layers.track[3], 'bar-hue');
    },

    saturationMode: function() {
        // bottom has saturation map
        this.setClass(this.layers.map[0], 'map-saturation');

        // top has overlay
        this.setClass(this.layers.map[1], 'map-saturation-overlay');

        // bottom: color
        this.setBackground(this.layers.track[2], this.color.hex);

        // top: graduated overlay
        this.setClass(this.layers.track[3], 'bar-saturation');
    },

    brightnessMode: function() {
        // MAP
        // bottom: nothing

        // top
        this.setBackground(this.layers.map[0], '000000');
        this.setClass(this.layers.map[1], 'map-brightness');

        // SLIDER
        // bottom
        this.setBackground(this.layers.track[2], this.color.hex);

        // top
        this.setClass(this.layers.track[3], 'bar-brightness');
    },

    redMode: function() { this.colorMode('red'); },
    greenMode: function() { this.colorMode('green'); },
    blueMode: function() { this.colorMode('blue'); },

    colorMode: function(color) {
        this.setClass(this.layers.map[1], 'map-'+color+'-max');
        this.setClass(this.layers.map[0], 'map-'+color+'-min');

        this.setClass(this.layers.track[3], 'bar-'+color+'-tl');
        this.setClass(this.layers.track[2], 'bar-'+color+'-tr');
        this.setClass(this.layers.track[1], 'bar-'+color+'-br');
        this.setClass(this.layers.track[0], 'bar-'+color+'-bl');
    },

    onRender: function()
    {
//         debugger;
        if (this.el)
        {
//             debugger;
            Kwf.ColorPicker.Panel.superclass.onRender.apply(this, arguments);
            this.initMarkup();
            this.initLayers();
            this.initConsole();
            this.initMap();
            this.initSlider();
            this.writeToConsole();
            this.setMode(this.mode);
        }
    },

    initComponent: function()
    {
        if (this.frame) {
            this.width = 410;
            this.height = 310;
        }
        this.preloadImages();
        Kwf.ColorPicker.Panel.superclass.initComponent.apply(this, arguments);
        this.color = new Kwf.Colorpicker.color.Color();
        this.color.setHex(this.hex);
    },

    initMarkup: function()
    {
            this.body.dom.innerHTML += [
                '<div class="x2-map">',
                    '<div class="x2-layer"></div>',
                    '<div class="x2-layer"></div>',
                    '<div class="x2-pointer"></div>',
                '</div>',
                '<div class="x2-track">',
                    '<div class="x2-layer"></div>',
                    '<div class="x2-layer"></div>',
                    '<div class="x2-layer"></div>',
                    '<div class="x2-layer"></div>',
                    '<div class="x2-slider"></div>',
                '</div>',
                '<ul class="x2-console">',
                    '<li class="preview"></li>',
                    '<li class="hue"></li>',
                    '<li class="saturation"></li>',
                    '<li class="brightness"></li>',
                    '<li class="red"></li>',
                    '<li class="green"></li>',
                    '<li class="blue"></li>',
                    '<li class="hex"></li>',
                '</ul>'
            ].join('');
    },

    initConsole: function()
    {
        var _console = this.body.first('.x2-console'),
            radio,
            group = Ext2.id(),
            slot, mode,
            that = this;

        this.preview = _console.first('.preview');

        for (slot in this.modes) {
            (function(mode){
                el = _console.first('.' + mode.name);

                radio = new Ext2.Element(document.createElement('div'));
                radio.addClass('x2-radio');

                Ext2.ux.Radio.registerMember(that, radio.dom, {
                    dial: function(){
                        Ext2.fly(this).addClass('x2-dialed');
                    }.createDelegate(radio),
                    un: function(){
                        Ext2.fly(this).removeClass('x2-dialed');
                    }.createDelegate(radio)
                });

                mode.radio = radio;
                radio.dom.mode = mode.name;
                el.appendChild(radio);

                if (that.mode === mode.name) {
                    Ext2.ux.Radio.dial(that, radio.dom);
                }

                radio.on({
                    'click': function(event, radio){
                        that.setMode(radio.mode);
                        Ext2.ux.Radio.dial(that, radio);
                    }
                });

                that.modeFields[mode.name] = new Ext2.form.NumberField({
                    allowBlank: false,
                    allowNegative: false,
                    allowDecimals: false,
                    fieldLabel: mode.unit,
                    minValue: mode.min,
                    maxValue: mode.max,
                    renderTo: el,
                    validator: function(value){
                        that.write(mode, value);
                        that.setFromConsole(mode);
                        that.paint();
                        that.paintSliders();
                        return true;
                    }
                });

                that.modeFields[mode.name].el.on({
                    'click': function(event){
//                        console.warn("STUB");
                    }
                });
            })(that.modes[slot]);
        };

        this.hex = new Ext2.form.TextField({
            renderTo: _console.first('.hex'),
            allowBlank: false,
            fieldLabel: 'hex',
            label: true,
            validator: function(value)
            {
                this.setRawValue("#"+Ext2.ux.color.Color.prototype.validateHex(value.replace(/^#/, '')));
                return true;
            }
        });
    },

    initMap: function()
    {
        var that = this;
        var map = this.body.first('.x2-map');
        map.dom.id = Ext2.id();

        var pointer = map.first('.x2-pointer');
        pointer.dom.id = Ext2.id();

        this.map = new Ext2.dd.DragDrop({
            id: map.dom.id
        });

        Ext2.apply(this.map, {
            onMouseDown: function(event) {
                this.onDrag(event);
            },

            onDrag: function(event, dontSet) {
                var width = pointer.getWidth() - 2,
                    height = pointer.getHeight() - 2,
                    halfWidth = (width / 2),
                    halfHeight = (height / 2),
                    x = event.xy[0] - map.getLeft() - halfWidth,
                    y = event.xy[1] - map.getTop() - halfHeight;


                x = x.constrain(0 - halfWidth, map.getWidth() - halfWidth);
                pointer.setLeft(x - 1);

                y = y.constrain(0 - halfHeight, map.getHeight() - halfHeight);
                pointer.setTop(y - 1);

                if (!dontSet) {
                    that.setFromMap(event.xy[0] - map.getLeft(), event.xy[1] - map.getTop());
                }
            },

            endDrag: function() {
                document.documentElement.style.cursor = 'default';
            }
        });
    },

    initSlider: function()
    {
        var that = this;

        var track = this.body.first('.x2-track');
        track.dom.id = Ext2.id();

        var slider = track.first('.x2-slider');
        slider.dom.id = Ext2.id();

        this.track = new Ext2.dd.DragDrop({
            id: track.dom.id
        });

        Ext2.apply(this.track, {
            onMouseDown: function(event) {
                this.onDrag(event);
            },

            onDrag: function(event, dontSet) {
                var height = slider.getHeight() -2,
                    halfHeight = height / 2,
                    y = event.xy[1] - track.getTop() - halfHeight;

                y = y.constrain(0 - halfHeight, track.getHeight() - halfHeight);
                slider.setTop(y - 1);

                if (!dontSet) {
                    that.setFromTrack(event.xy[1] - track.getTop());
                }
            },

            endDrag: function() {
                document.documentElement.style.cursor = 'default';
            }
        });
    },

    setAlpha: function(layer, value)
    {
        Ext2.fly(layer).setOpacity(value/100);
    },

    setClass: function(layer, name)
    {
        layer.className = 'x2-layer ' + name;
    },
    setBackground: function(layer, hex)
    {
        Ext2.fly(layer).setStyle({
            backgroundColor: (hex==null ? 'transparent' : "#" + hex)
        });
    },

    initLayers: function()
    {
        this.layers = {
            track: this.body.query('.x2-track > .x2-layer'),
            map: this.body.query('.x2-map > .x2-layer')
        };
    },

    preloadImages: function()
    {
        var img;
        Ext2.each(this.images, function(image)
        {
            img = new Image();
            img.src = "/images/" + image + ".png";
            this.preloads.push(img);
        }, this);
    },

    paint: function()
    {
        this.paintMap();
        this.paintTrack();
        this.paintPreview();
//        this.paintSliders();
    },

    paintMap: function()
    {
        switch(this.mode) {
            case 'hue':
                // fake color with only hue
                var color = new Ext2.ux.color.Color();
                color.setHsv({hue:this.color.hue, saturation:100, brightness:100});
                this.setBackground(this.layers.map[0], color.hex);
                break;

            case 'saturation':
                this.setAlpha(this.layers.map[1], 100 - this.color.saturation);
                break;

            case 'brightness':
                this.setAlpha(this.layers.map[1], this.color.brightness);
                break;

            case 'red':
                this.setAlpha(this.layers.map[1], (this.color.red/255)*100);
                break;

            case 'green':
                this.setAlpha(this.layers.map[1], (this.color.green/255)*100);
                break;

            case 'blue':
                this.setAlpha(this.layers.map[1], (this.color.blue/255)*100);
                break;
        }
    },

    paintTrack: function()
    {
        switch(this.mode) {
            case 'hue':
                break;

            case 'saturation':
                var saturatedColor = new Ext2.ux.color.Color();
                saturatedColor.setHsv({hue:this.color.hue, saturation:100, brightness:this.color.brightness});
                this.setBackground(this.layers.track[2], saturatedColor.hex);
                break;

            case 'brightness':
                var brightnessColor = new Ext2.ux.color.Color();
                brightnessColor.setHsv({hue:this.color.hue, saturation:this.color.saturation, brightness:100});
                this.setBackground(this.layers.track[2], brightnessColor.hex);
                break;
            case 'red':
            case 'green':
            case 'blue':

                var hValue = 0;
                var vValue = 0;

                switch(this.mode) {
                    case 'red':
                        hValue = this.modeFields.blue.getValue();
                        vValue = this.modeFields.green.getValue();
                        break;
                    case 'green':
                        hValue = this.modeFields.blue.getValue();
                        vValue = this.modeFields.red.getValue();
                        break;
                    case 'blue':
                        hValue = this.modeFields.red.getValue();
                        vValue = this.modeFields.green.getValue();
                }

                var horzPer = (hValue/255)*100;
                var vertPer = (vValue/255)*100;

                var horzPerRev = ((255-hValue)/255)*100;
                var vertPerRev = ((255-vValue)/255)*100;

                this.setAlpha(this.layers.track[3], (vertPer>horzPerRev) ? horzPerRev : vertPer);
                this.setAlpha(this.layers.track[2], (vertPer>horzPer) ? horzPer : vertPer);
                this.setAlpha(this.layers.track[1], (vertPerRev>horzPer) ? horzPer : vertPerRev);
                this.setAlpha(this.layers.track[0], (vertPerRev>horzPerRev) ? horzPerRev : vertPerRev);

                break;
        }
    },

    paintPreview: function()
    {
        this.setBackground(this.preview, this.color.hex);
    },

    paintSliders: function()
    {
        var sliderValue = 0,
            sliderValues = {
                hue: 360,
                saturation: 100,
                brightness: 100,
                red: 255,
                green: 255,
                blue: 255
            },
            modeValue = sliderValues[this.mode];
        sliderValue = modeValue - this.color[this.mode];

        this.track.onDrag({
            xy:[0, (255 * (sliderValue / modeValue)) + Ext2.fly(this.track.getEl()).getTop()]
        }, true);

        var mapTop = 0, mapLeft = 0;

        switch(this.mode) {
            case 'hue':
                mapLeft = this.color.saturation /100 *255;
                mapTop = (100 - this.color.brightness) /100 *255;
                break;

            case 'saturation':
                mapLeft = this.color.hue /360 *255;
                mapTop = (100 - this.color.brightness) /100 *255;
                break;

            case 'brightness':
                mapLeft = this.color.hue /360 *255;
                mapTop = (100 - this.color.saturation) /100 *255;
                break;

            case 'red':
                mapLeft = this.color.blue;
                mapTop = 255 - this.color.green;
                break;

            case 'green':
                mapLeft = this.color.blue;
                mapTop = 255 - this.color.red;
                break;

            case 'blue':
                mapLeft = this.color.red;
                mapTop = 255 - this.color.green;
                break;
        }

        this.map.onDrag({
            xy: [
                mapLeft + Ext2.fly(this.map.getEl()).getLeft(),
                mapTop + Ext2.fly(this.map.getEl()).getTop()
            ]
        }, true);
//            mapPointValues = {
//                hue:        ['saturation', [100, 'brightness']],
//                saturation: ['hue',        [100, 'brightness']],
//                brightness: ['hue',        [100, 'saturation']],
//                red:        ['blue',       [255, 'green']],
//                green:      ['blue',       [255, 'red']],
//                blue:       ['red',        [255, 'green']]
//            },
//            modeValue = mapPointValues[this.pickerMode][1][0];
//        mapLeft = this.color[mapPointValues[this.pickerMode][0]];
//        mapTop = modeValue - this.color[mapPointValues[this.pickerMode][1][1]];
//
//        console.log(mapTop, mapLeft);
//
//        mapTop = (mapTop / modeValue) * 255;
//        mapLeft = (mapLeft / modeValue) * 255;

//        console.log(mapTop, mapLeft);
//        console.log(mapLeft, mapTop)

    }
});
