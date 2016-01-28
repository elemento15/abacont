/*global define*/
define(function (require) {
  "use strict";

  var RowView = require('app/views/_generic/row'),
    FormView = require('app/views/incomes/form'),
    tpl      = require('text!tpl/incomes/row.htm');

  return RowView.extend({
    tpl: tpl,
    FormView: FormView,
    hasActiveAttr: true
  });

});