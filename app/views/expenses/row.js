/*global define*/
define(function (require) {
  "use strict";

  var RowView = require('app/views/_generic/row'),
    FormView = require('app/views/expenses/form'),
    tpl      = require('text!tpl/expenses/row.htm'),
    Defaults = require('app/defaults');

  return RowView.extend({
    tpl: tpl,
    FormView: FormView,
    hasActiveAttr: true

    /*events: function(){
      return _.extend({},RowView.prototype.events, {
        'click .btn-mark' : 'changeExtra'
      });
    },*/

    /*changeExtra: function (evt) {
      var me = this;
      var id = this.model.get('id');

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'movements/change_extraordinary',
          type: 'POST',
          dataType: 'json',
          data: { id: id }
        })
      ).then(function (data, textStatus, jqXHR) {
      	me.options.listView.loadCollection(true);
      });
    }*/
  });

});