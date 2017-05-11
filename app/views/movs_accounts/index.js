/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var IndexView = require('app/views/_generic/index'),
    tpl       = require('text!tpl/movs_accounts/index.htm'),
    FormView  = require('app/views/movs_accounts/form'),
    FormTransferView = require('app/views/movs_accounts/form_transfer'),
    RowView   = require('app/views/movs_accounts/row'),
    ListCollection = require('app/collections/movs_accounts'),
    Defaults  = require('app/defaults');

  return IndexView.extend({
    tpl: tpl,
    FormView: FormView,
    FormTransferView: FormTransferView,
    RowView: RowView,
    listCollection: new ListCollection(),
    paging: 10,
    orderTable: { field: 'fecha', type: 'DESC' },
    orderById: true,

    events: function(){
      return _.extend({},IndexView.prototype.events, {
        'click li.cmd-add'          : 'add_record',
        'click li.cmd-add-transfer' : 'add_transfer'
      });
    },

    onInit: function () {
      var that = this;

      this.filterTable = [{ field: 'cancelado', value: 0 }];
      
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

        that.filterTable = [];
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
    },

    add_transfer: function () {
      $("#main-container .index-container").hide();
      var view = new this.FormTransferView({ recId: null, listView: this });
      $("#main-container").append(view.render().el);
      if (view.onRender) {
        view.onRender();
      }
    }
  });

});