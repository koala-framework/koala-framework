/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/*
 * Portuguese/Brazil Translation by Weber Souza
 * 08 April 2007
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Carregando...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.Grid){
   Ext.grid.Grid.prototype.ddText = "{0} linha(s) selecionada(s)";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Fechar Regio";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "O valor para este campo  invlido";
}

Date.monthNames = [
   "Janeiro",
   "Fevereiro",
   "Maro",
   "Abril",
   "Maio",
   "Junho",
   "Julho",
   "Agosto",
   "Setembro",
   "Outubro",
   "Novembro",
   "Dezembro"
];

Date.dayNames = [
   "Domingo",
   "Segunda",
   "Tera",
   "Quarta",
   "Quinta",
   "Sexta",
   "Sbado"
];

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Cancelar",
      yes    : "Sim",
      no     : "No"
   };
}

if(Ext.util.Format){
   Ext.util.Format.date = function(v, format){
      if(!v) return "";
      if(!(v instanceof Date)) v = new Date(Date.parse(v));
      return v.dateFormat(format || "m/d/Y");
   };
}

if(Ext.DatePicker){
   Ext.apply(Ext.DatePicker.prototype, {
      todayText         : "Hoje",
      minText           : "Esta data  anterior a menor data",
      maxText           : "Esta data  posterior a maior data",
      disabledDaysText  : "",
      disabledDatesText : "",
      monthNames        : Date.monthNames,
      dayNames          : Date.dayNames,
      nextText          : 'Prximo Ms (Control+Direito)',
      prevText          : 'Previous Month (Control+Esquerdo)',
      monthYearText     : 'Choose a month (Control+Cima/Baixo para mover entre os anos)',
      todayTip          : "{0} (Espao)",
      format            : "m/d/y"
   });
}

if(Ext.PagingToolbar){
   Ext.apply(Ext.PagingToolbar.prototype, {
      beforePageText : "Pgina",
      afterPageText  : "de {0}",
      firstText      : "Primeira Pgina",
      prevText       : "Pgina Anterior",
      nextText       : "Prxima Pgina",
      lastText       : "ltima Pgina",
      refreshText    : "Atualizar Listagem",
      displayMsg     : "<b>{0} a {1} de {2} registro(s)</b>",
      emptyMsg       : 'Sem registros para exibir'
   });
}

if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "O tamanho mnimo permitido para este campo  {0}",
      maxLengthText : "O tamanho mximo para este campo  {0}",
      blankText     : "Este campo  obrigatrio, favor preencher.",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "O valor mnimo para este campo  {0}",
      maxText : "O valor mximo para este campo  {0}",
      nanText : "{0} no  um nmero vlido"
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "Desabilitado",
      disabledDatesText : "Desabilitado",
      minText           : "A data deste campo deve ser posterior a {0}",
      maxText           : "A data deste campo deve ser anterior a {0}",
      invalidText       : "{0} no  uma data vlida - deve ser informado no formato {1}",
      format            : "m/d/y"
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "Carregando...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Este campo deve ser um endereo de e-mail vlido no formado "usuario@dominio.com"',
      urlText      : 'Este campo deve ser uma URL no formato "http:/'+'/www.dominio.com"',
      alphaText    : 'Este campo deve conter apenas letras e _',
      alphanumText : 'Este campo devve conter apenas letras, nmeros e _'
   });
}

if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Ordenar Ascendente",
      sortDescText : "Ordenar Descendente",
      lockText     : "Bloquear Coluna",
      unlockText   : "Desbloquear Coluna",
      columnsText  : "Colunas"
   });
}

if(Ext.grid.PropertyColumnModel){
   Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
      nameText   : "Nome",
      valueText  : "Valor",
      dateFormat : "m/j/Y"
   });
}

if(Ext.SplitLayoutRegion){
   Ext.apply(Ext.SplitLayoutRegion.prototype, {
      splitTip            : "Arraste para redimencionar.",
      collapsibleSplitTip : "Arraste para redimencionar. Duplo clique para esconder."
   });
}
