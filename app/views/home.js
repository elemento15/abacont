/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    tpl      = require('text!tpl/home.htm'),
    template = _.template(tpl),
    Defaults = require('app/defaults');

  return Backbone.View.extend({
    render: function () {
      this.$el.html(template());
      return this;
    },

    onRender: function () {
      this.getKPIData(); 
    },

    getKPIData: function () {
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'main/kpis',
          type: 'POST',
          dataType: 'json'
        })
      ).then(function (data, textStatus, jqXHR) {
        $('#kpiIng30d').html('$'+(data.ing30d || 0).formatMoney());
        $('#kpiExp30d').html('$'+(data.exp30d || 0).formatMoney());
        $('#kpiIng6m').html('$'+(data.ing6m || 0).formatMoney());
        $('#kpiExp6m').html('$'+(data.exp6m || 0).formatMoney());
      });
    }

  });

});