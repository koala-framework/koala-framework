//from http://extjs.com/forum/showthread.php?t=20802

Ext.ns("Ext.grid.GridSummary"); // namespace Ext.grid.GridSummary

Ext.grid.GridSummary = function(config) {
    Ext.apply(this, config);
};

Ext.extend(Ext.grid.GridSummary, Ext.util.Observable, {
  init : function(grid) {
    this.grid = grid;
    this.cm = grid.getColumnModel();
    this.view = grid.getView();
    var v = this.view;

    v.onLayout = this.onLayout; // override GridView's onLayout() method

    v.afterMethod('render', this.refreshSummary, this);
    v.afterMethod('refresh', this.refreshSummary, this);
    v.afterMethod('syncScroll', this.syncSummaryScroll, this);
    v.afterMethod('onColumnWidthUpdated', this.doWidth, this);
    v.afterMethod('onAllColumnWidthsUpdated', this.doAllWidths, this);
    v.afterMethod('onColumnHiddenUpdated', this.doHidden, this);
    v.afterMethod('onUpdate', this.refreshSummary, this);
    v.afterMethod('onRemove', this.refreshSummary, this);

    if (!this.rowTpl) {
      this.rowTpl = new Ext.Template(
        '<div class="x-grid3-summary-row x-grid3-gridsummary-row-offset">',
          '<table class="x-grid3-summary-table" border="0" cellspacing="0" cellpadding="0" style="{tstyle}">',
            '<tbody><tr>{cells}</tr></tbody>',
          '</table>',
        '</div>'
      );
      this.rowTpl.disableFormats = true;
    }
    this.rowTpl.compile();

    if (!this.cellTpl) {
      this.cellTpl = new Ext.Template(
        '<td class="x-grid3-col x-grid3-cell x-grid3-td-{id} {css}" style="{style}">',
          '<div class="x-grid3-cell-inner x-grid3-col-{id}" unselectable="on">{value}</div>',
        "</td>"
      );
      this.cellTpl.disableFormats = true;
    }
    this.cellTpl.compile();
  },

  calculate : function(rs, cs) {
    var data = {}, r, c, cfg = this.cm.config, cf;
    for (var j = 0, jlen = rs.length; j < jlen; j++) {
      r = rs[j];
      for (var i = 0, len = cs.length; i < len; i++) {
        c = cs[i];
        cf = cfg[i];
        if (cf && cf.summaryType) {
          data[c.name] = Ext.grid.GridSummary.Calculations[cf.summaryType](data[c.name] || 0, r, c.name, data);
        }
      }
    }

    return data;
  },

  onLayout : function(vw, vh) {
    // note: this method is scoped to the GridView
    this.scroller.setHeight(vh - this.summary.getHeight());
  },

  syncSummaryScroll : function() {
    var mb = this.view.scroller.dom;
    this.view.summaryWrap.dom.scrollLeft = mb.scrollLeft;
    this.view.summaryWrap.dom.scrollLeft = mb.scrollLeft; // second time for IE (1/2 time first fails, other browsers ignore)
  },

  doWidth : function(col, w, tw) {
    var s = this.view.summary.dom;
    s.firstChild.style.width = tw;
    s.firstChild.rows[0].childNodes[col].style.width = w;
  },

  doAllWidths : function(ws, tw) {
    var s = this.view.summary.dom, wlen = ws.length;
    s.firstChild.style.width = tw;
    cells = s.firstChild.rows[0].childNodes;
    for (var j = 0; j < wlen; j++) {
      cells[j].style.width = ws[j];
    }
  },

  doHidden : function(col, hidden, tw) {
    var s = this.view.summary.dom;
    var display = hidden ? 'none' : '';
    s.firstChild.style.width = tw;
    s.firstChild.rows[0].childNodes[col].style.display = display;
  },

  renderSummary : function(o, cs) {
    cs = cs || this.view.getColumnData();
    var cfg = this.cm.config;
    var buf = [], c, p = {}, cf, last = cs.length-1;

    for (var i = 0, len = cs.length; i < len; i++) {
      c = cs[i];
      cf = cfg[i];
      p.id = c.id;
      p.style = c.style;
      p.css = i == 0 ? 'x-grid3-cell-first ' : (i == last ? 'x-grid3-cell-last ' : '');
      if (cf.summaryType || cf.summaryRenderer) {
        p.value = (cf.summaryRenderer || c.renderer)(o.data[c.name], p, o);
      } else {
        p.value = '';
      }
      if (p.value == undefined || p.value === "") p.value = "&#160;";
      buf[buf.length] = this.cellTpl.apply(p);
    }

    return this.rowTpl.apply({
      tstyle: 'width:' + this.view.getTotalWidth() + ';',
      cells: buf.join('')
    });
  },

  refreshSummary : function() {
    var g = this.grid, ds = g.store;
    var cs = this.view.getColumnData();
    var rs = ds.getRange();
    var data = this.calculate(rs, cs);
    var buf = this.renderSummary({data: data}, cs);

    if (!this.view.summaryWrap) {
      this.view.summaryWrap = Ext.DomHelper.insertAfter(this.view.scroller, {
        tag: 'div',
        cls: 'x-grid3-gridsummary-row-inner'
      }, true);
    } else {
      this.view.summary.remove();
    }
    this.view.summary = this.view.summaryWrap.insertHtml('afterbegin', buf, true);
  },

  toggleSummary : function(visible) { // true to display summary row
    var el = this.grid.getGridEl();
    if (el) {
      if (visible === undefined) {
        visible = el.hasClass('x-grid-hide-gridsummary');
      }
      el[visible ? 'removeClass' : 'addClass']('x-grid-hide-gridsummary');
    }

    this.view.layout();
  },

  getSummaryNode : function() {
    return this.view.summary;
  }
});

Ext.grid.GridSummary.Calculations = {
  'sum' : function(v, record, field) {
    return v + (record.data[field]||0);
  },

  'count' : function(v, record, field, data) {
    return data[field+'count'] ? ++data[field+'count'] : (data[field+'count'] = 1);
  },

  'max' : function(v, record, field, data) {
    var v = record.data[field];
    var max = data[field+'max'] === undefined ? (data[field+'max'] = v) : data[field+'max'];
    return v > max ? (data[field+'max'] = v) : max;
  },

  'min' : function(v, record, field, data) {
    var v = record.data[field];
    var min = data[field+'min'] === undefined ? (data[field+'min'] = v) : data[field+'min'];
    return v < min ? (data[field+'min'] = v) : min;
  },

  'average' : function(v, record, field, data) {
    var c = data[field+'count'] ? ++data[field+'count'] : (data[field+'count'] = 1);
    var t = (data[field+'total'] = ((data[field+'total']||0) + (record.data[field]||0)));
    return t === 0 ? 0 : t / c;
  }
};