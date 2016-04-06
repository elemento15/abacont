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
      'click .btn-generate' : 'generateRpt'
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

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'accounts/actives',
          dataType: 'json'
        })
      ).then(function (data, textStatus, jqXHR) {
        var nombre;
        data.forEach(function (item) {
          nombre = item.nombre + ' (' + ((item.tipo == 'C') ? 'Crédito' : ( (item.tipo == 'D') ? 'Débito' : 'Efectivo')) + ')';
          that.$("select[name=cuenta_id]").append('<option value="'+ item.id +'">'+ nombre +'</option>')
        });
      });
    },

    generateRpt: function () {
      this.$("input[name=fecha_ini]").datepicker('setDate');
      this.$("input[name=fecha_fin]").datepicker('setDate');
      
      var rpt = this.$el.find('[name="rpt"]').val();
      var type = this.$el.find('[name="tipo"]').val();
      var account = this.$el.find('[name="cuenta_id"]').val();
      var date_ini = this.$el.find('[name="fecha_ini"]').datepicker('getFormattedDate','yyyy-mm-dd');
      var date_end = this.$el.find('[name="fecha_fin"]').datepicker('getFormattedDate','yyyy-mm-dd');

      var params = '?rpt='+ rpt +'&type='+ type +'&account='+ account +'&date_ini='+ date_ini +'&date_end='+ date_end;
      window.open('movements/rpt_movements'+ params);
    }

  });

});