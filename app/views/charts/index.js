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
      'click .updateChart'  : 'updateChart',
      'change .configChart' : 'updateChart',
      'click ul.nav a'      : 'selectChart'
    },

    initialize: function (params) {
      // TODO
    },
    
    render: function () {
      var template = _.template(this.tpl);
      this.$el.html(template());
      return this;
    },

    onRender: function () {
      var that = this;

      this.chart_01 = new CanvasJS.Chart("canvas-chart01", {
        title: { text: "Saldos de Cuentas" },
        axisX: { title: 'Cuentas' },
        axisY: { title: 'Saldo' },
        data: [{
          type: 'column',
          color: "gray",
          dataPoints: [],
          indexLabel: "{y}",
          indexLabelPlacement: "outside",
        }]
      });

      this.chart_02 = new CanvasJS.Chart("canvas-chart02", {
        title: { text: "Gastos por Día" },
        axisX: { title: 'Dias' },
        axisY: { title: 'Importes' },
        data: [{
          type: 'spline',
          dataPoints: []
        }]
      });

      this.chart_03 = new CanvasJS.Chart("canvas-chart03", {
        title: { text: "Gastos Mensuales" },
        axisX: { title: 'Meses' },
        axisY: { title: 'Total' },
        data: [{
          type: 'column',
          // color: "gray",
          dataPoints: [],
          indexLabel: "{y}",
          indexLabelPlacement: "outside",
        }]
      });

      // $.fn.datepicker.defaults.format = 'dd/mm/yyyy';

      // $('.input-group.date').datepicker({
      //   language: "es",
      //   autoclose: true,
      //   todayHighlight: true
      // });

      this.updateChart01();
    },

    selectChart: function (evt) {
      var opt = $(evt.target).attr('opt');

      switch (opt) {
        case '01' : this.updateChart01(); break;
        case '02' : this.updateChart02(); break;
        case '03' : this.updateChart03(); break;
      }
    },

    updateChart: function (evt) {
      var that = this;
      var opt = $(evt.target).attr('opt');

      switch (opt) {
        case '01' : this.updateChart01(); break;
        case '02' : this.updateChart02(); break;
        case '03' : this.updateChart03(); break;
      }
    },
    updateChart01: function () {
      var me = this;
      var dps = [];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'accounts/actives',
          dataType: 'json'
        })
      ).then(function (data, textStatus, jqXHR) {

        data.forEach(function (item, index) {
          dps.push({
            label: item.nombre,
            y: parseFloat(item.saldo),
            color: (parseFloat(item.saldo) < 0) ? 'red' : 'blue' 
          });
        });

        me.chart_01.options.data[0].dataPoints = dps;
        me.chart_01.render();
      });
    },
    updateChart02: function () {
      var me = this;
      var dps = [];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/expenses_day',
          dataType: 'json',
          type: 'POST',
          data: {
            month: $('[name="month_02"]').val(),
            year: $('[name="year_02"]').val()
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
    },
    updateChart03: function () {
      var me = this;
      var dps = [];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/expenses_months',
          dataType: 'json',
          type: 'POST'
        })
      ).then(function (data, textStatus, jqXHR) {

        data.forEach(function (item, index) {
          dps.push({
            label: item.fecha,
            y: parseFloat(item.total)
          });
        });

        me.chart_03.options.data[0].dataPoints = dps;
        me.chart_03.render();
      });
    }

  });

});