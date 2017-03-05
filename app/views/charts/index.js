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
      'click .updateChart'            : 'updateChart',
      'change .configChart'           : 'updateChart',
      'click ul.nav a'                : 'selectChart',
      'change [name="categorias"]'    : 'changeCategory',
      'change [name="subcategorias"]' : 'changeSubCategory',
      'change [name="extraordinary"]' : 'changeExtraordinary'
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

      this.chart_04 = new CanvasJS.Chart("canvas-chart04", {
        title: { text: "Promedios Mensuales" },
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

      this.updateChart01();
    },

    getActivePanel: function () {
      var opt = this.$el.find('ul.nav li.active a').attr('opt');
      return opt;
    },

    selectChart: function (evt) {
      var opt = $(evt.target).attr('opt');

      switch (opt) {
        case '01' : this.updateChart01(); break;
        case '02' : this.updateChart02(); break;
        case '03' : this.updateChart03(); break;
        case '04' : this.updateChart04(); break;
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
    changeExtraordinary: function (evt) {
      this.updateChart(false);
    },

    updateChart: function (evt) {
      var that = this;
      var opt = (evt) ? $(evt.target).attr('opt') : this.getActivePanel();

      switch (opt) {
        case '01' : this.updateChart01(); break;
        case '02' : this.updateChart02(); break;
        case '03' : this.updateChart03(); break;
        case '04' : this.updateChart04(); break;
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

      // if year not setted, set with current year
      if (! year) {
        year = new Date();
        year = year.getFullYear();
        $('[name="year_02"]').val(year);
      }

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/expenses_day',
          dataType: 'json',
          type: 'POST',
          data: {
            month: $('[name="month_02"]').val(),
            year: $('[name="year_02"]').val(),
            category: me.getCategory(),
            subcategory: me.getSubCategory(),
            extraordinary: me.checkedExtraordinary()
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
          type: 'POST',
          data: {
            category: me.getCategory(),
            subcategory: me.getSubCategory(),
            extraordinary: me.checkedExtraordinary()
          }
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
    },
    updateChart04: function () {
      var me = this;
      var dps = [];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'charts/average_months',
          dataType: 'json',
          type: 'POST',
          data: {
            category: me.getCategory(),
            subcategory: me.getSubCategory(),
            extraordinary: me.checkedExtraordinary()
          }
        })
      ).then(function (data, textStatus, jqXHR) {

        data.forEach(function (item, index) {
          dps.push({
            label: item.fecha,
            y: parseFloat(item.total)
          });
        });

        me.chart_04.options.data[0].dataPoints = dps;
        me.chart_04.render();
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

    checkedExtraordinary: function () {
      var value = $('[name="extraordinary"]:checked').val() || 0;
      return value;
    },
    getCategory: function () {
      var value = $('select[name="categorias"]').val() || 0;
      return value;
    },
    getSubCategory: function () {
      var value = $('select[name="subcategorias"]').val() || 0;
      return value;
    }

  });

});