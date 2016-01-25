/*global define*/
define(function (require) {
  "use strict";

  var RowView = require('app/views/_generic/row'),
    FormView = require('app/views/subcategories/form'),
    tpl      = require('text!tpl/subcategories/row.htm');

  return RowView.extend({
    tpl: tpl,
    FormView: FormView,
    hasActiveAttr: true
  });

});