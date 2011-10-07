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

Ext.namespace("Vps.ColorPicker.color");

/*
    todo:
        * CYMK conversion
        * research other color spaces
        * clean up "setter" code
*/

/**
 * @class Ext.ux.Color
 * Experimental Color class. Supports and converts between RGB, HSV and HEX
 * @cfg {Number} red Red value, between 0 and 255
 * @cfg {Number} green Green value, between 0 and 255
 * @cfg {Number} blue Red value, between 0 and 255
 * @cfg {Number} hue Hue value, between 0 and 360 degrees
 * @cfg {Number} saturation Saturation value, between 0 and 100 percent
 * @cfg {Number} brightness Brightness value, between 0 and 100 percent
 * @constructor
 * Create a new Color, ready to convert between RGB, HSV and HEX.
 * @param {Object} config The config object
 */

Vps.ColorPicker.color.Color = function(config) {
    Ext.apply(this, this.defaultValues);
    Ext.apply(this, config);
};

Vps.ColorPicker.color.Color.prototype = {

    /**
     * Sets red, green or blue values to your liking. After this method completes,
     * values will be used to update HSV and HEX
     * @param {Object} rgb The red:, green: or blue: values to set with
     */
    setRgb: function(rgb) {
        this.set('red',   rgb);
        this.set('green', rgb);
        this.set('blue',  rgb);
        this.rgbToHsv();
        this.produceHex();
    },


    /**
     * Sets hue, saturation or brughtness values to your liking. After this method completes,
     * values will be used to update RGB and HEX
     * @param {Object} hsv The hue:, saturation: or brightness: values to set with
     */
    setHsv: function(hsv) {
        this.set('hue',        hsv);
        this.set('saturation', hsv);
        this.set('brightness', hsv);
        this.hsvToRgb();
        this.produceHex();
    },

    /**
     * Sets hex for color you your liking. After this method completes,
     * values will be used to update RGB and HSV
     * @param {Object} hex The hex value to set with
     */
    setHex: function(hex) {
        var part, splitHex;
        hex = this.validateHex(hex);
        splitHex = this.splitHex(hex);
        for (part in splitHex) { hex = splitHex[part];
            switch(part) {
                case 'red':
                    this.setRed(parseInt(hex, 16));
                    break;

                case 'green':
                    this.setGreen(parseInt(hex, 16));
                    break;

                case 'blue':
                    this.setBlue(parseInt(hex, 16));
                    break;
            }
        }
        this.rgbToHsv();
        this.produceHex();
    },

    // The numbered version for this Class
    version: {
        major: 0,
        minor: 1,
        tiny: 0,
        toString: function() {
            return [this.major, this.minor, this.tiny].join('.');
        }
    },

    //private
    defaultValues: {
        red:        0,
        green:      0,
        blue:       0,
        hue:        0,
        saturation: 0,
        brightness: 0,
        hex:  "000000"
    },

    produceHex: function() {
        this.hex = this.intToHex(this.red) + this.intToHex(this.green) + this.intToHex(this.blue);
    },

    //private
    intToHex: function (dec){
        var result = (parseInt(dec, 10).toString(16));
        if (result.length == 1)
            result = ("0" + result);
        return result.toUpperCase();
    },

    //private
    constrainWithWarnings: function(value, key, min, max) {
        if (isNaN(value)) {
            //console.warn(key+" value must be between numerical. You supplied "+value+" for "+key+". Using "+min+" instead.");
            value = min;
        }
        else if (value < min) {
            //console.warn(key+" value must be between "+min+" and "+max+". You supplied "+value+" for "+key+". Using "+min+" instead.");
            value = min;
        }
        else if (value > max) {
            //console.warn(key+" value must be between "+min+" and "+max+". You supplied "+value+" for "+key+". Using "+max+" instead.");
            value = max;
        }
        return value;
    },

    //private
    constrainRgb: function(value, color) {
        value = this.constrainWithWarnings(value, color, 0, 255);

        if(color.match(/^(red|blue|green)$/) !== null) {
            this[color] = value;
        }
    },

    //private
    // http://www.cs.rit.edu/~ncs/color/t_convert.html
    // The Hue/Saturation/Value model was created by A. R. Smith in 1978.
    rgbToHsv: function() {
        var min, max, delta, hue,
            saturation, brightness,
            red, green, blue;

        red   = this.red   / 255;
        green = this.green / 255;
        blue  = this.blue  / 255;

        max = Math.max(red, green, blue);
        min = Math.min(red, green, blue);

        delta = max - min;

        brightness = max;
        saturation = (max) ? ((max - min) / max) : 0;

        if(!saturation) {
            hue = 0;
        }
        else {
            if (red === max) {
                hue = (green - blue) / delta;
            }
            else if (green === max) {
                hue = 2 + (blue - red) / delta;
            }
            else {
                hue = 4 + (red - green) / delta;
            }

            hue = hue * 60;
            if (hue < 0) { hue += 360; }
        }
        this.setHue(parseInt(hue, 10));
        this.setSaturation(parseInt(saturation * 100));
        this.setBrightness(parseInt(brightness * 100));
    },

    //private
    hsvToRgb: function() {
        var i, hue, f, p, q, t, rgbSetter,
            brightness = this.brightness/100,
            saturation = this.saturation / 100;


        if (this.saturation === 0) {
            var monotone = brightness * 255;
            this.setRed(monotone);
            this.setGreen(monotone);
            this.setBlue(monotone);
        }

        hue = this.hue / 60;
        i   = parseInt(hue, 10);

        f = hue - i;
        p = brightness * (1 - saturation);
        q = brightness * (1 - saturation * f);
        t = brightness * (1 - saturation * (1 - f));

        rgbSetter = [
            [brightness,t,p],
            [q,brightness,p],
            [p,brightness,t],
            [p,q,brightness],
            [t,p,brightness],
            [brightness,p,q]
        ][i];

        this.setRed(rgbSetter[0] * 255);
        this.setGreen(rgbSetter[1] * 255);
        this.setBlue(rgbSetter[2] * 255);
    },

    //private
    validateHex: function(value) {
        var hex = value || this.hex;
        hex = hex.toUpperCase();
        if (hex.charAt[0] === '#') { hex = hex.slice(1); }
        hex = hex.replace(/[^A-F0-9]/g, '0');
        if (hex.length > 6) { hex = hex.substring(0, 6); }
        return hex;
    },

    //private
    capitalize: function (string) {
        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    },

    //private
    set: function(property, source) {
        if(source[property]) {
            this['set'+this.capitalize(property)](source[property]);
        }
    },

    //private
    splitHex: function(hex) {
        return {
            red:   hex.slice(0, 2),
            green: hex.slice(2, 4),
            blue:  hex.slice(4, 6)
        };
    },

    //private
    setRed: function(value) {
        this.constrainRgb(value, 'red');
    },


    //private
    setGreen: function(value) {
        this.constrainRgb(value, 'green');
    },


    //private
    setBlue: function(value) {
        this.constrainRgb(value, 'blue');
    },


    //private
    setHue: function(value) {
        value = this.constrainWithWarnings(value, 'hue', 0, 359);
        this.hue = value;
    },

    //private
    setSaturation: function(value) {
        value = this.constrainWithWarnings(value, 'saturation', 0, 100);
        this.saturation = value;
    },


    //private
    setBrightness: function(value) {
        value = this.constrainWithWarnings(value, 'brightness', 0, 100);
        this.brightness = value;
    }
};
