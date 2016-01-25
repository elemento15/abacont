/*global define*/
define(function (require) {
  "use strict";

  var RowView = require('app/views/_generic/row'),
    FormView = require('app/views/categories/form'),
    tpl      = require('text!tpl/categories/row.htm');

  return RowView.extend({
    tpl: tpl,
    FormView: FormView,
    hasActiveAttr: true
  });

});