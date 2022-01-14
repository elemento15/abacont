/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
      tpl = require('text!tpl/rpt_movements/index.htm'),
      Defaults = require('app/defaults');

  return Backbone.View.extend({
    tpl: tpl,
    className: 'index-container',
    events: {
      'click .btn-generate'  : 'generateRpt',
      'change [name="rpt"]'  : 'changeRpt',
      'change [name="tipo"]' : 'changeCategory',
      'change [name="tipo_fecha"]' : 'changeDateType',
      'change [name="categoria_id"]' : 'fillSubcategoriesList'
    },

    initialize: function (params) {
      
    },
    
    render: function () {
      var template = _.template(this.tpl);
      this.$el.html(template());
      return this;
    },

    onRender: function () {
      var that = this;

      $.fn.datepicker.defaults.format = 'dd/mm/yyyy';

      $('.input-group.date').datepicker({
        language: "es",
        autoclose: true,
        todayHighlight: true
      });

      // accounts
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'accounts/actives',
          dataType: 'json'
        })
      ).then(function (data, textStatus, jqXHR) {
        var nombre;
        data.forEach(function (item) {
          nombre = item.nombre + ' (' + ((item.tipo == 'C') ? 'Crédito' : ( (item.tipo == 'D') ? 'Débito' : ( (item.tipo == 'E') ? 'Efectivo' : 'Inversión'))) + ')';
          that.$("select[name=cuenta_id]").append('<option value="'+ item.id +'">'+ nombre +'</option>')
        });
      });

      this.fillCategoriesList();
    },

    generateRpt: function () {
      this.$("input[name=fecha_ini]").datepicker('setDate');
      this.$("input[name=fecha_fin]").datepicker('setDate');
      
      var rpt = this.$el.find('[name="rpt"]').val();
      var type = this.$el.find('[name="tipo"]').val();
      var type_date = this.$el.find('[name="tipo_fecha"]').val();
      var account = this.$el.find('[name="cuenta_id"]').val();
      var category = this.$el.find('[name="categoria_id"]').val() || 0;
      var subcategory = this.$el.find('[name="subcategoria_id"]').val() || 0;
      var date_ini = this.$el.find('[name="fecha_ini"]').datepicker('getFormattedDate','yyyy-mm-dd');
      var date_end = this.$el.find('[name="fecha_fin"]').datepicker('getFormattedDate','yyyy-mm-dd');
      var comments = this.$el.find('[name="ver_comentarios"]:checked').val() || 0;
      var csv = this.$el.find('[name="descargar_csv"]:checked').val() || 0;

      var params = '?rpt='+ rpt +'&type='+ type +'&account='+ account +'&comments='+ comments;
      params += '&type_date='+ type_date +'&date_ini='+ date_ini +'&date_end='+ date_end;
      params += '&category='+ category + '&subcategory='+ subcategory +'&csv='+ csv;
      window.open('movements/rpt_movements'+ params);
    },

    changeRpt: function (evt) {
      var rpt = evt.target.value;


      if (rpt == 'D') {
        this.$el.find('.cls-ver-comentarios').show();
      } else {
        this.$el.find('.cls-ver-comentarios').hide();
      }

      if (rpt == 'X') {
        this.$el.find('.cls-ver-tipo-fecha').show();
        this.$el.find('.cls-descargar-csv').show();
      } else {
        this.$el.find('.cls-ver-tipo-fecha').hide();
        this.$el.find('.cls-ver-fechas').show();
        this.$el.find('.cls-descargar-csv').hide();
      }
    },

    changeCategory: function () {
      this.fillCategoriesList();
      this.fillSubcategoriesList();
    },

    changeDateType: function (evt) {
      var type = evt.target.value;
      if (type == 'M') {
        this.$el.find('.cls-ver-fechas').show();
      } else {
        this.$el.find('.cls-ver-fechas').hide();
      }
    },

    fillCategoriesList: function () {
      var type = this.$el.find('[name="tipo"]').val();
      var that = this;

      that.$("select[name=categoria_id]").html('<option value="">--Todas--</option>');

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'categories/actives',
          type: 'POST',
          dataType: 'json',
          data: { type: type }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item) {
          that.$("select[name=categoria_id]").append('<option value="'+ item.id +'">'+ item.nombre +'</option>')
        });
      });
    },

    fillSubcategoriesList: function () {
      var category = this.$el.find('[name="categoria_id"]').val();
      var that = this;

      that.$("select[name=subcategoria_id]").html('<option value="">--Todas--</option>');

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'subcategories/actives',
          type: 'POST',
          dataType: 'json',
          data: { category_id: category }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item) {
          that.$("select[name=subcategoria_id]").append('<option value="'+ item.id +'">'+ item.nombre +'</option>')
        });
      });
    }

  });

});