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

    // filter subcategories by category selected
    searchSubCategories: function (category_id, callback) {
      // var that = this;

      // App.block();

      // $.when(
      //   $.ajax({
      //     url: Defaults.ROUTE + 'subcategories/actives',
      //     type: 'POST',
      //     dataType: 'json',
      //     data: { category_id: category_id }
      //   })
      // ).then(function (data, textStatus, jqXHR) {
      //   var nombre;
      //   that.$("select[name=subcategoria_id]").html('<option value="">-- Seleccione --</option>');
      //   data.forEach(function (item) {
      //     that.$("select[name=subcategoria_id]").append('<option value="'+ item.id +'">'+ item.nombre +'</option>')
      //   });

      //   if (callback) { callback(); }
      //   App.unblock();
      // });
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
          nombre = item.nombre + ' (' + ((item.tipo == 'C') ? 'Crédito' : ( (item.tipo == 'D') ? 'Débito' : 'Efectivo')) + ')';
          that.$("select[name=cuenta_id]").append('<option value="'+ item.id +'">'+ nombre +'</option>')
        });
      });
    }

  });

});