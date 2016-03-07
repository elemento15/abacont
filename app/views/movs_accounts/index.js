/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var IndexView = require('app/views/_generic/index'),
    tpl       = require('text!tpl/movs_accounts/index.htm'),
    FormView  = require('app/views/movs_accounts/form'),
    RowView   = require('app/views/movs_accounts/row'),
    ListCollection = require('app/collections/movs_accounts'),
    Defaults  = require('app/defaults');

  return IndexView.extend({
    tpl: tpl,
    FormView: FormView,
    RowView: RowView,
    listCollection: new ListCollection(),
    paging: 10,
    orderTable: { field: 'fecha', type: 'DESC' },
    filterTable: [{ field: 'cuenta_id', value: '0' }],

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

        that.filterTable = [{ field: 'cuenta_id', value: '0' }];
        that.loadCollection(true);
      });
    },

    onLoadCollection: function () {
      var account = this.$('[name="cuenta_id"]').val();
      this.searchAccountData(account);
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
        that.$('#div-account-balance-grid').text('$0.00');
        that.$('#div-account-balance-grid').removeClass('cls-negative');

        if (data.saldo) {
          that.$('#div-account-balance-grid').text('$'+ data.saldo.formatMoney());
          if (parseFloat(data.saldo) < 0) {
            that.$('#div-account-balance-grid').addClass('cls-negative');
          }
        }
      });
    }
  });

});