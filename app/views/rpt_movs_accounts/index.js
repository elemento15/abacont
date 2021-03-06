/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
      tpl = require('text!tpl/rpt_movs_accounts/index.htm'),
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
          nombre = item.nombre + ' (' + ((item.tipo == 'C') ? 'Crédito' : ( (item.tipo == 'D') ? 'Débito' : ( (item.tipo == 'E') ? 'Efectivo' : 'Inversión'))) + ')';
          that.$("select[name=cuenta_id]").append('<option value="'+ item.id +'">'+ nombre +'</option>')
        });
      });
    },

    generateRpt: function () {
      this.$("input[name=fecha_ini]").datepicker('setDate');
      
      var account = this.$el.find('[name="cuenta_id"]').val();
      var date_ini = this.$el.find('[name="fecha_ini"]').datepicker('getFormattedDate','yyyy-mm-dd');
      var option = this.$el.find('[name="option_report"]:checked').val();

      var params = '?account='+ account + '&date_ini='+ date_ini +'&option='+ option;
      window.open('movs_accounts/rpt_movs_accounts'+ params);
    }

  });

});