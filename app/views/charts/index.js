/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
      CanvasJS = require('canvasjs'),
      tpl = require('text!tpl/charts/index.htm'),
      Defaults = require('app/defaults');

  return Backbone.View.extend({
    tpl: tpl,
    className: 'index-container',
    events: {
      'change .configChart'           : 'updateChart',
      'click ul.nav a'                : 'selectChart',
      'change [name="categorias"]'    : 'changeCategory',
      'change [name="subcategorias"]' : 'changeSubCategory',
      'change [name="tipo"]'          : 'changeTypeAccount',
      'change [name="cuentas"]'       : 'changeAccount',
      'click .cls-expense-detail'     : 'showComments',
      'change [name=omit_inversion]'  : 'changeOmitInversion'
    },

    initialize: function (params) {
      this.showedChart02 = false;
      this.omitInversionsVisible = true;
    },
    
    render: function () {
      var template = _.template(this.tpl);
      this.$el.html(template());
      return this;
    },

    onRender: function () {
      var that = this;

      this.chart_01 = new CanvasJS.Chart("canvas-chart01", {
        theme: "theme3",
        title: { 
          text: "Saldos de Cuentas",
          fontSize: 18
        },
        axisX: {
          title: 'Cuentas', 
          titleFontSize: 16,
          labelFontSize: 12,
          labelAutoFit: true
        },
        axisY: {
          title: 'Saldo',
          titleFontSize: 16,
          labelFontSize: 12,
          gridColor: "#CCCCCC"
        },
        data: [{
          type: 'column',
          color: "gray",
          dataPoints: [],
          indexLabel: "{y}",
          indexLabelPlacement: "outside",
          indexLabelBackgroundColor: "#fff",
          indexLabelFontSize: 12,
          indexLabelFontColor: "#333333",
          fillOpacity: .7,
          bevelEnabled: false
        }]
      });

      this.chart_02 = new CanvasJS.Chart("canvas-chart02", {
        theme: "theme3",
        title: { text: "Gastos por Día", fontSize: 18 },
        axisX: {
          title: 'Dias',
          titleFontSize: 16,
          labelFontSize: 12,
          labelAutoFit: true
        },
        axisY: {
          title: '$',
          titleFontSize: 16,
          labelFontSize: 12,
          gridColor: "#CCCCCC"
        },
        zoomEnabled: true,
        data: [{
          type: 'spline',
          cursor: 'pointer',
          dataPoints: [],
          click: function (e) {
            var date = e.dataPoint.label;
            that.showExpensesDetails(date);
          }
        }]
      });

      this.chart_03 = new CanvasJS.Chart("canvas-chart03", {
        theme: "theme3",
        title: { text: "Gastos Mensuales", fontSize: 18 },
        axisX: {
          title: 'Meses',
          titleFontSize: 16,
          labelFontSize: 12,
          labelAutoFit: true
        },
        axisY: {
          title: '$',
          titleFontSize: 16,
          labelFontSize: 13,
          gridColor: "#CCCCCC"
        },
        legend: {
          fontFamily: 'Verdana',
          fontSize: 12,
          cursor: 'pointer',
          itemclick: function (e) {
            e.dataSeries.visible = !e.dataSeries.visible;
            e.chart.render();
          }
        },
        data: [
          {
            type: 'stackedColumn',
            color: '#ff7272',
            visible: true,
            dataPoints: [],
            showInLegend: true,
            legendText: "Gastos",
            //indexLabel: "{y}",
            indexLabelPlacement: "inside",
            indexLabelFontSize: 12,
            indexLabelFontColor: "#333333",
            fillOpacity: .6,
            bevelEnabled: false
          },{
            type: 'stackedColumn',
            color: '#ffaeae',
            visible: true,
            dataPoints: [],
            showInLegend: true,
            legendText: "Gastos MSI",
            //indexLabel: "{y}",
            indexLabelPlacement: "inside",
            indexLabelFontSize: 12,
            indexLabelFontColor: "#333333",
            fillOpacity: .6,
            bevelEnabled: false
          },{
            type: 'line',
            color: '#4F81BC',
            visible: true,
            dataPoints: [],
            showInLegend: true,
            legendText: "Ingresos",
            markerSize: 5
          }
        ]
      });

      this.chart_04 = new CanvasJS.Chart("canvas-chart04", {
        theme: "theme3",
        title: { text: "Promedios Diario por Mes", fontSize: 18 },
        axisX: {
          title: 'Meses',
          titleFontSize: 16,
          labelFontSize: 12,
          labelAutoFit: true
        },
        axisY: {
          title: '$',
          titleFontSize: 16,
          labelFontSize: 13,
          gridColor: "#CCCCCC"
        },
        legend: {
          fontFamily: 'Verdana',
          fontSize: 12,
          cursor: 'pointer',
          itemclick: function (e) {
            e.dataSeries.visible = !e.dataSeries.visible;
            e.chart.render();
          }
        },
        data: [
          {
            type: 'stackedColumn',
            color: '#ff7272',
            visible: true,
            dataPoints: [],
            showInLegend: true,
            legendText: "Gastos",
            //indexLabel: "{y}",
            indexLabelPlacement: "inside",
            indexLabelFontSize: 12,
            indexLabelFontColor: "#333333",
            fillOpacity: .6,
            bevelEnabled: false
          },{
            type: 'stackedColumn',
            color: '#ffaeae',
            visible: true,
            dataPoints: [],
            showInLegend: true,
            legendText: "Gastos MSI",
            //indexLabel: "{y}",
            indexLabelPlacement: "inside",
            indexLabelFontSize: 12,
            indexLabelFontColor: "#333333",
            fillOpacity: .6,
            bevelEnabled: false
          },{
            type: 'line',
            color: '#4F81BC',
            visible: true,
            dataPoints: [],
            showInLegend: true,
            legendText: "Ingresos",
            markerSize: 5
          }
        ]
      });

      this.chart_05 = new CanvasJS.Chart("canvas-chart05", {
        theme: "theme3",
        title: { text: "Saldos Mensuales de Cuentas (Débito/Crédito)", fontSize: 18 },
        axisX: {
          title: 'Meses',
          titleFontSize: 16,
          labelFontSize: 12,
          labelAutoFit: true
        },
        axisY: {
          title: '$',
          titleFontSize: 16,
          labelFontSize: 13,
          gridColor: "#CCCCCC"
        },
        legend: {
          fontFamily: 'Verdana',
          fontSize: 12,
          cursor: 'pointer',
          itemclick: function (e) {
            e.dataSeries.visible = !e.dataSeries.visible;
            e.chart.render();
          }
        },
        data: [
          {
            type: 'splineArea',
            color: '#6694bb',
            visible: true,
            dataPoints: [],
            showInLegend: true,
            legendText: "Débito",
            indexLabelPlacement: "inside",
            indexLabelFontSize: 12,
            indexLabelFontColor: "#333333",
            fillOpacity: .6,
            bevelEnabled: false,
            markerSize: 4
          },{
            type: 'splineArea',
            color: '#ff7272',
            visible: true,
            dataPoints: [],
            showInLegend: true,
            legendText: "Crédito",
            indexLabelPlacement: "inside",
            indexLabelFontSize: 12,
            indexLabelFontColor: "#333333",
            fillOpacity: .6,
            bevelEnabled: false,
            markerSize: 4
          }
        ]
      });


      // fill the categories select, with all the categorias for expenses
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'categories/actives',
          type: 'POST',
          dataType: 'json',
          data: { type: 'G' }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item) {
          that.$("select[name=categorias]").append('<option value="'+ item.id +'">'+ item.nombre +'</option>')
        });
      });

      $('.divFrm01').show();

      this.updateChart01();
    },

    getActivePanel: function () {
      var opt = this.$el.find('ul.nav li.active a').attr('opt');
      return opt;
    },

    selectChart: function (evt) {
      var opt = $(evt.target).attr('opt');
      
      // hide all options forms
      $('.divFrm01').hide();
      $('.divFrm02').hide();
      $('.divFrm03').hide();
      $('.divFrm04').hide();
      $('.divFrm05').hide();

      // show the selected options forms
      $('.divFrm'+opt).show();

      // depending on charts show or hide the omitInversions checkbox
      if (opt == '01' || opt == '05') {
        if (this.getTypeAccount()) {
          $('.divOmitInversion').hide();
        } else {
          $('.divOmitInversion').show();
        }
      } else {
          $('.divOmitInversion').hide();
      }

      switch (opt) {
        case '01' : this.updateChart01(); break;
        case '02' : this.updateChart02(); break;
        case '03' : this.updateChart03(); break;
        case '04' : this.updateChart04(); break;
        case '05' : this.updateChart05(); break;
      }
    },
    changeCategory: function (evt) {
      var that = this;
      var value = evt.target.value;
      this.searchSubCategories(parseInt(value || 0), function () {
        that.updateChart(false);
      });
    },
    changeSubCategory: function (evt) {
      this.updateChart(false);
    },
    changeTypeAccount: function (evt) {
      var that = this;
      var value = evt.target.value;

      // if "type" selected, omitInversion checkbox is not needed
      if (value) {
        this.$el.find('[name="omit_inversion"]').prop("checked", false);
        $('.divOmitInversion').hide();
      } else {
        $('.divOmitInversion').show();
      }

      this.searchAccounts(value || false, function () {
        that.updateChart(false);
      });
    },
    changeAccount: function (evt) {
      this.updateChart(false);
    },
    changeOmitInversion: function (evt) {
      this.updateChart(false);
    },

    updateChart: function (evt) {
      var that = this;
      var opt = (evt && $(evt.target).attr('opt')) ? $(evt.target).attr('opt') : this.getActivePanel();

      switch (opt) {
        case '01' : this.updateChart01(); break;
        case '02' : this.updateChart02(); break;
        case '03' : this.updateChart03(); break;
        case '04' : this.updateChart04(); break;
        case '05' : this.updateChart05(); break;
      }
    },
    updateChart01: function () {
      var me = this;
      var dps = [];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'accounts/actives',
          dataType: 'json',
          type: 'POST',
          data: { 
            type: me.getTypeAccount(),
            omitInversions: me.getOmitInversion(),
            order: { field: 'orden', type: 'ASC' }
          }
        })
      ).then(function (data, textStatus, jqXHR) {

        data.forEach(function (item, index) {
          dps.push({
            label: item.nombre,
            y: parseFloat(item.saldo),
            color: (parseFloat(item.saldo) < 0) ? '#e45d5d' : '#6694bb' 
          });
        });

        me.chart_01.options.data[0].dataPoints = dps;
        me.chart_01.render();
      });
    },
    updateChart02: function () {
      var me = this;
      var dps = [];
      var year = $('[name="year_02"]').val();
      var month = $('[name="month_02"]').val();

      var today = new Date;

      // if year not setted, set with current year
      if (! year) {
        year = today.getFullYear();
        $('[name="year_02"]').val(year);
      }

      // if month not setted, set with current month
      if (! month && ! this.showedChart02) {
        month = today.getMonth() + 1;
        month = (month < 10) ? '0'+ month : month;
        $('[name="month_02"]').val(month);
      }

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/expenses_day',
          dataType: 'json',
          type: 'POST',
          data: {
            month: parseInt($('[name="month_02"]').val()) || '',
            year: $('[name="year_02"]').val(),
            category: me.getCategory(),
            subcategory: me.getSubCategory()
          }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item, index) {
          dps.push({
            label: item.fecha,
            y: parseFloat(item.total)
          });
        });

        me.chart_02.options.data[0].dataPoints = dps;
        me.chart_02.render();
      });

      this.showedChart02 = true;
    },
    updateChart03: function () {
      var me = this;
      var dp_exp = [];
      var dp_msi = [];
      var dp_inc = [];

      // expenses
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/expenses_months',
          dataType: 'json',
          type: 'POST',
          data: {
            category: me.getCategory(),
            subcategory: me.getSubCategory(),
            months: me.getLastMonths(),
            msi: 0
          }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item, index) {
          dp_exp.push({
            label: item.mov_fecha,
            y: parseFloat(item.total)
          });
        });

        me.chart_03.options.data[0].dataPoints = dp_exp;
        me.chart_03.render();
      });

      // expenses msi
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/expenses_months',
          dataType: 'json',
          type: 'POST',
          data: {
            category: me.getCategory(),
            subcategory: me.getSubCategory(),
            months: me.getLastMonths(),
            msi: 1
          }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item, index) {
          dp_msi.push({
            label: item.mov_fecha,
            y: parseFloat(item.total)
          });
        });

        me.chart_03.options.data[1].dataPoints = dp_msi;
        me.chart_03.render();
      });

      // incomes
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/incomes_months',
          dataType: 'json',
          type: 'POST',
          data: {
            category: '',
            subcategory: '',
            months: me.getLastMonths()
          }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item, index) {
          dp_inc.push({
            label: item.mov_fecha,
            y: parseFloat(item.total)
          });
        });

        me.chart_03.options.data[2].dataPoints = dp_inc;
        me.chart_03.render();
      });
    },
    updateChart04: function () {
      var me = this;
      var dp_exp = [];
      var dp_msi = [];
      var dp_inc = [];

      // expenses
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/expenses_months_avg',
          dataType: 'json',
          type: 'POST',
          data: {
            category: me.getCategory(),
            subcategory: me.getSubCategory(),
            months: me.getLastMonths(),
            msi: 0
          }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item, index) {
          dp_exp.push({
            label: item.mov_fecha,
            y: parseFloat(item.total)
          });
        });

        me.chart_04.options.data[0].dataPoints = dp_exp;
        me.chart_04.render();
      });

      // expenses msi
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/expenses_months_avg',
          dataType: 'json',
          type: 'POST',
          data: {
            category: me.getCategory(),
            subcategory: me.getSubCategory(),
            months: me.getLastMonths(),
            msi: 1
          }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item, index) {
          dp_msi.push({
            label: item.mov_fecha,
            y: parseFloat(item.total)
          });
        });

        me.chart_04.options.data[1].dataPoints = dp_msi;
        me.chart_04.render();
      });

      // incomes
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/incomes_months_avg',
          dataType: 'json',
          type: 'POST',
          data: {
            category: '',
            subcategory: '',
            months: me.getLastMonths()
          }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item, index) {
          dp_inc.push({
            label: item.mov_fecha,
            y: parseFloat(item.total)
          });
        });

        me.chart_04.options.data[2].dataPoints = dp_inc;
        me.chart_04.render();
      });
    },
    updateChart05: function () {
      var me = this;
      var debit = [];
      var credit = [];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/balance_months',
          dataType: 'json',
          type: 'POST',
          data: { 
            type: me.getTypeAccount(), 
            account: me.getAccount(),
            omitInversions: me.getOmitInversion()
          }
        })
      ).then(function (data, textStatus, jqXHR) {

        data.debit.forEach(function (item, index) {
          debit.push({
            label: item.anio_mes,
            y: parseFloat(item.saldo)
          });
        });

        data.credit.forEach(function (item, index) {
          credit.push({
            label: item.anio_mes,
            y: parseFloat(item.saldo)
          });
        });

        me.chart_05.options.data[0].dataPoints = debit;
        me.chart_05.options.data[1].dataPoints = credit;
        me.chart_05.render();
      });
    },

    searchAccounts: function (type, callback) {
      var that = this;

      App.block();

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'accounts/actives',
          type: 'POST',
          dataType: 'json',
          data: { type: type }
        })
      ).then(function (data, textStatus, jqXHR) {
        that.$("select[name=cuentas]").html('<option value="">-- Todas --</option>');
        data.forEach(function (item) {
          that.$("select[name=cuentas]").append('<option value="'+ item.id +'">'+ item.nombre +'</option>')
        });

        if (callback) { callback(); }
        App.unblock();
      });
    },

    searchSubCategories: function (category_id, callback) {
      var that = this;

      App.block();

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'subcategories/actives',
          type: 'POST',
          dataType: 'json',
          data: { category_id: category_id }
        })
      ).then(function (data, textStatus, jqXHR) {
        that.$("select[name=subcategorias]").html('<option value="">-- Todas --</option>');
        data.forEach(function (item) {
          that.$("select[name=subcategorias]").append('<option value="'+ item.id +'">'+ item.nombre +'</option>')
        });

        if (callback) { callback(); }
        App.unblock();
      });
    },

    getCategory: function () {
      var value = $('select[name="categorias"]').val() || 0;
      return value;
    },
    getSubCategory: function () {
      var value = $('select[name="subcategorias"]').val() || 0;
      return value;
    },
    getAccount: function () {
      var value = $('select[name="cuentas"]').val() || 0;
      return value;
    },
    getLastMonths: function () {
      var value = $('select[name="last_months"]').val() || 0;
      return value;
    },
    getTypeAccount: function () {
      var value = $('select[name="tipo"]').val() || 0;
      return value;
    },
    getOmitInversion: function() {
      return this.$el.find('[name="omit_inversion"]:checked').val() || 0;
    },

    showExpensesDetails: function (date) {
      var category = this.getCategory();
      var subcategory = this.getSubCategory();

      var filter = [
        { field: 'tipo',      value: 'G' },
        { field: 'cancelado', value: 0 },
        { field: 'fecha',     value: date },
      ];

      if (category) {
        filter.push({ field: 'subcategorias.categoria_id', value: category });
      }

      if (subcategory) {
        filter.push({ field: 'subcategorias.id', value: subcategory });
      }

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'movements/read',
          dataType: 'json',
          data: {
            order_by_id: true,
            filter: filter
          },
          method: 'POST'
        })
      ).then(function (data, textStatus, jqXHR) {
        var html = '';
        
        if (! data) {
          alert('Error reading movements');
        } else {

          data.data.forEach(function (item) {
            html += '<tr>';
            html += '   <td>';
            html += '      <div class="text-info">';
            html += '         <span class="cls-expense-detail" expId="'+ item.id +'">'+ item.subcategoria_nombre +'</span>';
            html += '      </div>';
            html += '      <div class="cls-sub-text">'+ item.categoria_nombre +'</div>';
            html += '   </td>';
            html += '   <td>';
            html += '      <div class="cls-sub-text">'+ item.cuenta_nombre +'</div>';
            html += '   </td>';
            html += '   <td class="text-right">$'+ item.importe.formatMoney() +'</td>';
            
            if (parseInt(item.es_meses_sin_intereses)) {
              html += '   <td class="text-center"><span class="label label-warning">MSI</span></td>';
            } else {
              html += '   <td>&nbsp;</td>';
            }
            
            html += '</tr>';
          });
          
          $('#dialog-expenses table tbody').html(html);
          $('#dialog-expenses').modal();
        }
      });

      this.cleanComments();
    },

    showComments: function (evt) {
      var me = this;
      var id = evt.currentTarget.getAttribute('expId');

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'movements/model',
          dataType: 'json',
          data: { id: id },
          method: 'GET'
        })
      ).then(function (data, textStatus, jqXHR) {
        if (data.observaciones) {
          var html = '<span>'+ data.observaciones +'</span>';
          $('.cls-expenses-detail-comments').html(html);
        } else {
          me.cleanComments();
        }
      });
    },

    cleanComments: function () {
      var html = '<span class="text-muted">(Sin Comentarios)</span>';
      $('.cls-expenses-detail-comments').html(html);
    }
  });

});