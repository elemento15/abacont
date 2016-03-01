/*global define*/
define(function (require) {
  "use strict";

  var RowView = require('app/views/_generic/row'),
    FormView = require('app/views/movs_accounts/form'),
    tpl      = require('text!tpl/movs_accounts/row.htm');

  return RowView.extend({
    tpl: tpl,
    FormView: FormView,
    hasActiveAttr: true
  });

});