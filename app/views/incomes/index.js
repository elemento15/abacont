/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var IndexView = require('app/views/_generic/index'),
    tpl       = require('text!tpl/incomes/index.htm'),
    FormView  = require('app/views/incomes/form'),
    RowView   = require('app/views/incomes/row'),
    ListCollection = require('app/collections/movements');

  return IndexView.extend({
    tpl: tpl,
    FormView: FormView,
    RowView: RowView,
    listCollection: new ListCollection(),
    paging: 10,
    orderTable: { field: 'fecha', type: 'DESC' },
    filterTable: [{ field: 'tipo', value: 'I' }]
  });

});