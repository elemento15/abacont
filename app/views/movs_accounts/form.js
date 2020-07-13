/*global define*/
define(function (require) {
  "use strict";

  var FormView = require('app/views/_generic/form'),
    tpl   = require('text!tpl/movs_accounts/form.htm'),
    Model = require('app/models/mov_account'),
    Defaults = require('app/defaults');

  return FormView.extend({
    tpl: tpl,
    moduleName: 'movs_accounts',
    Model: Model,
    
    setForm: function () {
      if (this.model.get('tipo') == 'A') {
        this.$("input[name=tipo][value='A']").attr('checked','checked');
      } else {
        this.$("input[name=tipo][value='C']").attr('checked','checked');
      }
      this.$("input[name=fecha]").val(this.model.get('fecha'));
      this.$("select[name=cuenta_id]").val(this.model.get('cuenta_id'));
      this.$("input[name=importe]").val(this.model.get('importe'));
      this.$("input[name=concepto]").val(this.model.get('concepto'));
      this.$("textarea[name=observaciones]").val(this.model.get('observaciones'));
    },
    
    getForm: function () {
      this.$("input[name=fecha]").datepicker('setDate');

      this.model.set('tipo', this.$("input[name=tipo]:checked").val());
      this.model.set('fecha', this.$("input[name=fecha]").datepicker('getFormattedDate','yyyy-mm-dd'));
      this.model.set('cuenta_id', this.$("select[name=cuenta_id]").val());
      this.model.set('importe', this.$("input[name=importe]").val());
      this.model.set('concepto', this.$("input[name=concepto]").val());
      this.model.set('observaciones', this.$("textarea[name=observaciones]").val());
    },

    afterRender: function () {
      if (this.options.recId) {
        this.$("input[name=fecha]").attr('disabled', 'disabled');
        this.$("input[name=tipo]").attr('disabled', 'disabled');
        this.$("select[name=cuenta_id]").attr('disabled', 'disabled');
        this.$("input[name=importe]").attr('disabled', 'disabled');
      }
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
        });
      });
    }

  });

});