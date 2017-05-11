/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var IndexView = require('app/views/_generic/index'),
    tpl       = require('text!tpl/subcategories/index.htm'),
    FormView  = require('app/views/subcategories/form'),
    RowView   = require('app/views/subcategories/row'),
    ListCollection = require('app/collections/subcategories');

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