/*
 * timeago: a jQuery plugin, version: 0.7.2 (2009-07-30)
 * @requires jQuery v1.2 or later
 *
 * Timeago is a jQuery plugin that makes it easy to support automatically
 * updating fuzzy timestamps (e.g. "4 minutes ago" or "about 1 day ago").
 *
 * For usage and examples, visit:
 * http://timeago.yarp.com/
 *
 * Licensed under the MIT:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright (c) 2008-2009, Ryan McGeary (ryanonjavascript -[at]- mcgeary [*dot*] org)
 */

Kwf.Utils.TimeAgo = {
    settings: {
        allowFuture: false,
        strings: {
            prefixAgo: null,
            prefixFromNow: null,
            suffixAgo: trlKwf("ago"),
            suffixFromNow: trlKwf("from now"),
            seconds: trlKwf("less than a minute"),
            minute: trlKwf("about a minute"),
            minutes: trlKwf("{0} minutes"),
            hour: trlKwf("about an hour"),
            hours: trlKwf("about {0} hours"),
            day: trlKwf("a day"),
            days: trlKwf("{0} days"),
            month: trlKwf("about a month"),
            months: trlKwf("{0} months"),
            year: trlKwf("about a year"),
            years: trlKwf("{0} years")
        }
    },
    inWords: function(v)
    {
        if(!v){
            return '';
        }
        if(!(v instanceof Date)){
            var tmpv = new Date(Date.parseDate(v, 'Y-m-d'));
            if (isNaN(tmpv.getYear())) {
                tmpv = new Date(Date.parseDate(v, 'Y-m-d H:i:s'));
            }
            v = tmpv;
        }
        if(isNaN(v.getYear())){
            return '';
        }

        var distanceMillis = (new Date()).getTime() - v.getTime();

        var settings = Kwf.Utils.TimeAgo.settings;
        var $l = settings.strings;
        var prefix = $l.prefixAgo;
        var suffix = $l.suffixAgo;
        if (settings.allowFuture) {
            if (distanceMillis < 0) {
                prefix = $l.prefixFromNow;
                suffix = $l.suffixFromNow;
            }
            distanceMillis = Math.abs(distanceMillis);
        }

        var seconds = distanceMillis / 1000;
        var minutes = seconds / 60;
        var hours = minutes / 60;
        var days = hours / 24;
        var years = days / 365;

        var words = seconds < 45 && String.format($l.seconds, Math.round(seconds)) ||
            seconds < 90 && String.format($l.minute, 1) ||
            minutes < 45 && String.format($l.minutes, Math.round(minutes)) ||
            minutes < 90 && String.format($l.hour, 1) ||
            hours < 24 && String.format($l.hours, Math.round(hours)) ||
            hours < 48 && String.format($l.day, 1) ||
            days < 30 && String.format($l.days, Math.floor(days)) ||
            days < 60 && String.format($l.month, 1) ||
            days < 365 && String.format($l.months, Math.floor(days / 30)) ||
            years < 2 && String.format($l.year, 1) ||
            String.format($l.years, Math.floor(years));

        return [prefix, words, suffix].join(" ").replace(/^\s+|\s+$/g, '');
    }
};

Ext2.util.Format.timeAgo = function(v, p)
{
    return Kwf.Utils.TimeAgo.inWords(v);
};
