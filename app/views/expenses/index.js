/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var IndexView = require('app/views/_generic/index'),
    tpl       = require('text!tpl/expenses/index.htm'),
    FormView  = require('app/views/expenses/form'),
    RowView   = require('app/views/expenses/row'),
    ListCollection = require('app/collections/movements');

  return IndexView.extend({
    tpl: tpl,
    FormView: FormView,
    RowView: RowView,
    listCollection: new ListCollection(),
    paging: 10,
    orderTable: { field: 'fecha', type: 'DESC' },
    orderById: true,
    filterTable: [{ field: 'tipo', value: 'G' }]
  });

});