/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var IndexView = require('app/views/_generic/index'),
    tpl       = require('text!tpl/accounts/index.htm'),
    FormView  = require('app/views/accounts/form'),
    RowView   = require('app/views/accounts/row'),
    ListCollection = require('app/collections/accounts');

  return IndexView.extend({
    tpl: tpl,
    FormView: FormView,
    RowView: RowView,
    listCollection: new ListCollection(),
    paging: 10
  });

});