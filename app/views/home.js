/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    tpl      = require('text!tpl/home.htm'),
    template = _.template(tpl),
    Defaults = require('app/defaults');

  return Backbone.View.extend({
    render: function () {
      this.$el.html(template());
      return this;
    },

    onRender: function () {
      this.getKPIData();

      // chart-balances
      this.chart_balances = new CanvasJS.Chart("chart-balances", {
        theme: "theme3",
        axisX: {
          //title: 'Cuentas', 
          //titleFontSize: 16,
          labelFontSize: 12,
          labelAutoFit: true
        },
        axisY: {
          title: '.',
          titleFontSize: 6,
          labelFontSize: 10,
          gridColor: "#EEEEEE"
        },
        dataPointMaxWidth: 20,
        data: [{
          type: 'bar',
          color: "gray",
          dataPoints: [],
          //indexLabel: "{y}",
          //indexLabelPlacement: "outside",
          //indexLabelBackgroundColor: "#fff",
          //indexLabelFontSize: 12,
          //indexLabelFontColor: "#333333",
          fillOpacity: .7,
          bevelEnabled: false
        }]
      });

      // income-expense-month
      this.income_expense_month = new CanvasJS.Chart("income-expense-month", {
        theme: "theme3",
        axisX: {
          labelFontSize: 12,
          labelAutoFit: true
        },
        axisY: {
          title: '.',
          titleFontSize: 6,
          labelFontSize: 10,
          gridColor: "#EEEEEE"
        },
        data: [{
          type: 'bar',
          color: "gray",
          dataPoints: [],
          //indexLabel: "{y}",
          //indexLabelPlacement: "outside",
          //indexLabelBackgroundColor: "#fff",
          //indexLabelFontSize: 12,
          //indexLabelFontColor: "#333333",
          fillOpacity: .7,
          bevelEnabled: false
        }]
      });

      // daily-balance
      this.daily_balance = new CanvasJS.Chart("daily-balance", {
        theme: "theme3",
        //title: { text: "Saldo Mensual", fontSize: 18 },
        axisX: {
          title: '.',
          titleFontSize: 8,
          labelFontSize: 10,
          labelAutoFit: true
        },
        axisY: {
          //title: '$',
          //titleFontSize: 16,
          labelFontSize: 10,
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
        zoomEnabled: true,
        data: [
          {
            type: 'area',
            color: '#6694bb',
            visible: true,
            dataPoints: [],
            //showInLegend: true,
            legendText: "Saldo",
            indexLabelPlacement: "inside",
            indexLabelFontSize: 12,
            indexLabelFontColor: "#333333",
            fillOpacity: .6,
            bevelEnabled: false,
            markerSize: 4
          }
        ]
      });

      // income-expense-year
      /*this.income_expense_year = new CanvasJS.Chart("income-expense-year", {
        theme: "theme3",
        axisX: {
          labelFontSize: 12,
          labelAutoFit: true
        },
        axisY: {
          title: '.',
          titleFontSize: 6,
          labelFontSize: 10,
          gridColor: "#EEEEEE"
        },
        data: [{
          type: 'bar',
          color: "gray",
          dataPoints: [],
          //indexLabel: "{y}",
          //indexLabelPlacement: "outside",
          //indexLabelBackgroundColor: "#fff",
          //indexLabelFontSize: 12,
          //indexLabelFontColor: "#333333",
          fillOpacity: .7,
          bevelEnabled: false
        }]
      });*/

      this.updateChartBalances();
      this.updateChartIncomeExpenseMonth();
      this.updateChartDailyBalance();
      //this.updateChartIncomeExpenseYear();
    },

    getKPIData: function () {
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'main/kpis',
          type: 'POST',
          dataType: 'json'
        })
      ).then(function (data, textStatus, jqXHR) {
        $('#kpiIng30d').html('$'+(data.ing30d || 0).formatMoney());
        $('#kpiExp30d').html('$'+(data.exp30d || 0).formatMoney());
        $('#kpiIng6m').html('$'+(data.ing6m || 0).formatMoney());
        $('#kpiExp6m').html('$'+(data.exp6m || 0).formatMoney());
        $('#kpiIng12m').html('$'+(data.ing12m || 0).formatMoney());
        $('#kpiExp12m').html('$'+(data.exp12m || 0).formatMoney());

        $('#kpiIngTot30d').html('$'+(data.ingtot30d || 0).formatMoney());
        $('#kpiExpTot30d').html('$'+(data.exptot30d || 0).formatMoney());
        $('#kpiIngTot6m').html('$'+((data.ingtot6m || 0) / 6).formatMoney());
        $('#kpiExpTot6m').html('$'+((data.exptot6m || 0) / 6).formatMoney());
        $('#kpiIngTot12m').html('$'+((data.ingtot12m || 0) / 12).formatMoney());
        $('#kpiExpTot12m').html('$'+((data.exptot12m || 0) / 12).formatMoney());
      });
    },

    updateChartBalances: function () {
      var me = this;
      var dps = [];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'main/account_balances',
          dataType: 'json',
          type: 'POST'
        })
      ).then(function (data, textStatus, jqXHR) {

        data.forEach(function (item, index) {
          dps.push({
            label: item.label,
            y: parseFloat(item.saldo),
            color: item.color,
          });
        });

        me.chart_balances.options.data[0].dataPoints = dps;
        me.chart_balances.render();
      });
    },

    updateChartIncomeExpenseMonth: function () {
      var me = this;
      var dps = [];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'main/income_expense_month',
          dataType: 'json',
          type: 'POST'
        })
      ).then(function (data, textStatus, jqXHR) {

        data.forEach(function (item) {
          dps.push({
            label: item.label,
            y: parseFloat(item.total),
            color: (item.tipo == 'I') ? '#37658e' : '#e45d5d', 
          });
        });

        me.income_expense_month.options.data[0].dataPoints = dps;
        me.income_expense_month.render();
      });
    },

    updateChartIncomeExpenseYear: function () {
      var me = this;
      var dps = [];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'main/income_expense_year',
          dataType: 'json',
          type: 'POST'
        })
      ).then(function (data, textStatus, jqXHR) {

        data.forEach(function (item) {
          dps.push({
            label: item.label,
            y: parseFloat(item.total),
            color: (item.tipo == 'I') ? '#37658e' : '#e45d5d', 
          });
        });

        me.income_expense_year.options.data[0].dataPoints = dps;
        me.income_expense_year.render();
      });
    },

    updateChartDailyBalance: function () {
      var me = this;
      var dps = [];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'main/daily_balance_month',
          dataType: 'json',
          type: 'POST'
        })
      ).then(function (data, textStatus, jqXHR) {

        data.forEach(function (item, index) {
          dps.push({
            label: item.fecha,
            y: parseFloat(item.saldo),
          });
        });

        me.daily_balance.options.data[0].dataPoints = dps;
        me.daily_balance.render();
      });
    }
  });

});