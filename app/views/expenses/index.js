/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var IndexView = require('app/views/_generic/index'),
    tpl       = require('text!tpl/expenses/index.htm'),
    FormView  = require('app/views/expenses/form'),
    RowView   = require('app/views/expenses/row'),
    ListCollection = require('app/collections/movements'),
    Defaults  = require('app/defaults');

  return IndexView.extend({
    tpl: tpl,
    FormView: FormView,
    RowView: RowView,
    listCollection: new ListCollection(),
    paging: 10,
    orderTable: { field: 'fecha', type: 'DESC' },
    orderById: true,

    onInit: function () {
      var that = this;

      this.filterTable = [
        { field: 'tipo', value: 'G' }, 
        { field: 'cancelado', value: 0 }
      ];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'categories/actives',
          dataType: 'json',
          method: 'POST',
          data: { type: 'G' }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item) {
          that.$("select[name='subcategorias.categoria_id']").append('<option value="'+ item.id +'">'+ item.nombre +'</option>')
        });

        that.loadCollection(true);
      });
    }
  });

});