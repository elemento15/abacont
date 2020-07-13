/*global define*/
define(function (require) {
  "use strict";

  var FormView = require('app/views/_generic/form'),
    tpl   = require('text!tpl/movs_accounts/form_transfer.htm'),
    Model = require('app/models/mov_transfer'),
    Defaults = require('app/defaults');

  return FormView.extend({
    tpl: tpl,
    moduleName: 'movs_accounts',
    Model: Model,
    
    getForm: function () {
      this.$("input[name=fecha]").datepicker('setDate');

      // this.model.set('tipo', this.$("input[name=tipo]:checked").val());
      this.model.set('fecha', this.$("input[name=fecha]").datepicker('getFormattedDate','yyyy-mm-dd'));
      this.model.set('cuenta_id', this.$("select[name=cuenta_id]").val());
      this.model.set('cuenta_id_destino', this.$("select[name=cuenta_id_destino]").val());
      this.model.set('importe', this.$("input[name=importe]").val());
      this.model.set('concepto', this.$("input[name=concepto]").val());
      this.model.set('observaciones', this.$("textarea[name=observaciones]").val());
    },

    onRender: function () {
      var that = this;

      $.fn.datepicker.defaults.format = 'dd/mm/yyyy';

      $('.input-group.date').datepicker({
        language: "es",
        autoclose: true,
        todayHighlight: true
      });
    },

    onInit: function () {
      var that = this;
      
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
          that.$("select[name=cuenta_id_destino]").append('<option value="'+ item.id +'">'+ nombre +'</option>')
        });
      });
    }

  });

});