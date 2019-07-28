/*global define*/
define(function (require) {
  "use strict";

  var FormView = require('app/views/_generic/form'),
    tpl   = require('text!tpl/incomes/form.htm'),
    Model = require('app/models/movement'),
    Defaults = require('app/defaults');

  return FormView.extend({
    tpl: tpl,
    moduleName: 'movements',
    Model: Model,
    
    setForm: function () {
      this.$("input[name=fecha]").val(this.model.get('fecha'));
      this.$("input[name=importe]").val(this.model.get('importe'));
      this.$("select[name=cuenta_id]").val(this.model.get('cuenta_id'));
      this.$("textarea[name=observaciones]").val(this.model.get('observaciones'));
    },
    
    getForm: function () {
      this.$("input[name=fecha]").datepicker('setDate');

      this.model.set('tipo', 'I');
      this.model.set('fecha', this.$("input[name=fecha]").datepicker('getFormattedDate','yyyy-mm-dd'));
      this.model.set('importe', this.$("input[name=importe]").val());
      this.model.set('cuenta_id', this.$("select[name=cuenta_id]").val());
      this.model.set('subcategoria_id', this.$("select[name=subcategoria_id]").val());
      this.model.set('observaciones', this.$("textarea[name=observaciones]").val());
    },

    afterRender: function () {
      if (this.options.recId) {
        this.$("select[name=cuenta_id]").attr('disabled', 'disabled');
        this.$("input[name=fecha]").attr('disabled', 'disabled');
        this.$("input[name=importe]").attr('disabled', 'disabled');
        this.$("select[name=categoria_id]").attr('disabled', 'disabled');
        this.$("select[name=subcategoria_id]").attr('disabled', 'disabled');

        this.$('.form-group:has(> #div-account-balance-form)').hide();
      }
    },

    // filter subcategories by category selected
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
        var nombre;
        that.$("select[name=subcategoria_id]").html('<option value="">-- Seleccione --</option>');
        data.forEach(function (item) {
          that.$("select[name=subcategoria_id]").append('<option value="'+ item.id +'">'+ item.nombre +'</option>')
        });

        if (callback) { callback(); }
        App.unblock();
      });
    },

    searchAccountData: function (account_id) {
      var that = this;

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'accounts/model?id='+ account_id,
          type: 'GET',
          dataType: 'json'
        })
      ).then(function (data, textStatus, jqXHR) {
        that.$('#div-account-balance-form').text('$0.00');
        that.$('#div-account-balance-form').removeClass('cls-negative');

        if (data.saldo) {
          that.$('#div-account-balance-form').text('$'+ data.saldo.formatMoney());
          if (parseFloat(data.saldo) < 0) {
            that.$('#div-account-balance-form').addClass('cls-negative');
          }
        }
      });
    },

    onRender: function () {
      var that = this;

      $.fn.datepicker.defaults.format = 'dd/mm/yyyy';

      $('.input-group.date').datepicker({
        language: "es",
        autoclose: true,
        todayHighlight: true
      });

      $("select[name=categoria_id]").change(function (e) {
        that.searchSubCategories( parseInt(e.target.value || 0) );
      });

      this.$('[name="cuenta_id"]').change( function (e) {
        var account = $(this).val();
        that.searchAccountData(account);
      });


    },

    onInit: function () {
      var that = this;
      
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'accounts/actives',
          type: 'POST',
          dataType: 'json',
          data: { incomes: 1 }
        })
      ).then(function (data, textStatus, jqXHR) {
        var nombre;
        data.forEach(function (item) {
          nombre = item.nombre + ' (' + ((item.tipo == 'C') ? 'Crédito' : ( (item.tipo == 'D') ? 'Débito' : 'Efectivo')) + ')';
          that.$("select[name=cuenta_id]").append('<option value="'+ item.id +'">'+ nombre +'</option>')
        });
      });

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'categories/actives',
          type: 'POST',
          dataType: 'json',
          data: { type: 'I' }
        })
      ).then(function (data, textStatus, jqXHR) {
        var nombre;
        data.forEach(function (item) {
          nombre = item.nombre + ' (' + ((item.tipo == 'I') ? 'Ingreso' : 'Gasto') + ')';
          that.$("select[name=categoria_id]").append('<option value="'+ item.id +'">'+ nombre +'</option>')
        });
      });
    },

    onLoad: function () {
      var that = this;
      var category_id = this.model.get('categoria_id');
      var subcategory_id = this.model.get('subcategoria_id');

      this.$("select[name=categoria_id]").val(category_id);
      this.searchSubCategories(category_id, function () {
        that.$("select[name=subcategoria_id]").val(subcategory_id);
      });
    }

  });

});