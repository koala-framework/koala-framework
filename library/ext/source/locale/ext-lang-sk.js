/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * List compiled by mystix on the extjs.com forums.
 * Thank you Mystix!
 */
 
 /*  Translation to Slovak by Michal Thomka
  *  14 April 2007
  */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Nahrvam...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.Grid){
   Ext.grid.Grid.prototype.ddText = "{0} oznaench riadkov";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Zavrie tto zloku";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Hodnota v tomto poli je nesprvna";
}

Date.monthNames = [
   "Janur",
   "Februr",
   "Marec",
   "Aprl",
   "Mj",
   "Jn",
   "Jl",
   "August",
   "September",
   "Oktber",
   "November",
   "December"
];

Date.dayNames = [
   "Nedea",
   "Pondelok",
   "Utorok",
   "Streda",
   "tvrtok",
   "Piatok",
   "Sobota"
];

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Zrui",
      yes    : "no",
      no     : "Nie"
   };
}

if(Ext.util.Format){
   Ext.util.Format.date = function(v, format){
      if(!v) return "";
      if(!(v instanceof Date)) v = new Date(Date.parse(v));
      return v.dateFormat(format || "m/d/R");
   };
}


if(Ext.DatePicker){
   Ext.apply(Ext.DatePicker.prototype, {
      todayText         : "Dnes",
      minText           : "Tento dtum je men ako minimlny mon dtum",
      maxText           : "Tento dtum je v ako maximlny mon dtum",
      disabledDaysText  : "",
      disabledDatesText : "",
      monthNames        : Date.monthNames,
      dayNames          : Date.dayNames,
      nextText          : 'al Mesiac (Control+Doprava)',
      prevText          : 'Predch. Mesiac (Control+Doava)',
      monthYearText     : 'Vyberte Mesiac (Control+Hore/Dole pre posun rokov)',
      todayTip          : "{0} (Medzernk)",
      format            : "m/d/r"
   });
}


if(Ext.PagingToolbar){
   Ext.apply(Ext.PagingToolbar.prototype, {
      beforePageText : "Strana",
      afterPageText  : "z {0}",
      firstText      : "Prv Strana",
      prevText       : "Predch. Strana",
      nextText       : "alia Strana",
      lastText       : "Posledn strana",
      refreshText    : "Obnovi",
      displayMsg     : "Zobrazujem {0} - {1} z {2}",
      emptyMsg       : 'iadne dta'
   });
}


if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "Minimlna dka pre toto pole je {0}",
      maxLengthText : "Maximlna dka pre toto pole je {0}",
      blankText     : "Toto pole je povinn",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "Minimlna hodnota pre toto pole je {0}",
      maxText : "Maximlna hodnota pre toto pole je {0}",
      nanText : "{0} je nesprvne slo"
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "Zablokovan",
      disabledDatesText : "Zablokovan",
      minText           : "Dtum v tomto poli mus by a po {0}",
      maxText           : "Dtum v tomto poli mus by pred {0}",
      invalidText       : "{0} nie je sprvny dtum - mus by vo formte {1}",
      format            : "m/d/r"
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "Nahrvam...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Toto pole mus by e-mailov adresa vo formte "user@domain.com"',
      urlText      : 'Toto pole mus by URL vo formte "http:/'+'/www.domain.com"',
      alphaText    : 'Toto poe moe obsahova iba psmen a znak _',
      alphanumText : 'Toto poe moe obsahova iba psmen,sla a znak _'
   });
}

if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Zoradi vzostupne",
      sortDescText : "Zoradi zostupne",
      lockText     : "Zamkn stpec",
      unlockText   : "Odomkn stpec",
      columnsText  : "Stpce"
   });
}

if(Ext.grid.PropertyColumnModel){
   Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
      nameText   : "Nzov",
      valueText  : "Hodnota",
      dateFormat : "m/j/Y"
   });
}

if(Ext.SplitLayoutRegion){
   Ext.apply(Ext.SplitLayoutRegion.prototype, {
      splitTip            : "Potiahnite pre zmenu rozmeru",
      collapsibleSplitTip : "Potiahnite pre zmenu rozmeru. Dvojklikom schovte."
   });
}
