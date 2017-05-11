/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var IndexView = require('app/views/_generic/index'),
    tpl       = require('text!tpl/categories/index.htm'),
    FormView  = require('app/views/categories/form'),
    RowView   = require('app/views/categories/row'),
    ListCollection = require('app/collections/categories');

  return IndexView.extend({
    tpl: tpl,
    FormView: FormView,
    RowView: RowView,
    listCollection: new ListCollection(),
    paging: 10,

    onInit: function () {
      this.filterTable = [{ field: 'activo', value: 1 }];
    }
  });

});