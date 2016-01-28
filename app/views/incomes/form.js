/*global define*/
define(function (require) {
  "use strict";

  var FormView = require('app/views/_generic/form'),
    tpl   = require('text!tpl/incomes/form.htm'),
    Model = require('app/models/mov_account');

  return FormView.extend({
    tpl: tpl,
    moduleName: 'movs_accounts',
    Model: Model,
    
    setForm: function () {
      // this.$("input[name=nombre]").val(this.model.get('nombre'));
      // this.$("select[name=tipo]").val(this.model.get('tipo'));
      // this.$("input[name=activo]").attr('checked', this.model.isActive());
      // this.$("input[name=num_tarjeta]").val(this.model.get('num_tarjeta'));
      // this.$("input[name=num_cuenta]").val(this.model.get('num_cuenta'));
      // this.$("textarea[name=observaciones]").val(this.model.get('observaciones'));
    },
    
    getForm: function () {
      // this.model.set('nombre', this.$("input[name=nombre]").val());
      // this.model.set('tipo', this.$("select[name=tipo]").val());
      // this.model.set('activo', (this.$("input[name=activo]:checked").length));
      // this.model.set('num_tarjeta', this.$("input[name=num_tarjeta]").val());
      // this.model.set('num_cuenta', this.$("input[name=num_cuenta]").val());
      // this.model.set('observaciones', this.$("textarea[name=observaciones]").val());
    },

    onRender: function () {
      $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true,
        todayHighlight: true
    });
    }
  });

});