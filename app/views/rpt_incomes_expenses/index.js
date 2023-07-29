/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
      tpl = require('text!tpl/rpt_incomes_expenses/index.htm'),
      Defaults = require('app/defaults');

  return Backbone.View.extend({
    tpl: tpl,
    className: 'index-container',
    events: {
      'click .btn-generate'  : 'generateRpt',
      /*'change [name="rpt"]'  : 'changeRpt',
      'change [name="tipo"]' : 'changeCategory',
      'change [name="categoria_id"]' : 'fillSubcategoriesList'*/
    },

    initialize: function (params) {
      
    },
    
    render: function () {
      var template = _.template(this.tpl);
      this.$el.html(template());
      return this;
    },

    onRender: function () {
      var that = this;
    },

    generateRpt: function () {
      var months = this.$el.find('[name="months"]').val() || 0;
      var curr_month = this.$el.find('[name="curr_month"]:checked').val() || 0;
      var option = this.$el.find('[name="option_report"]:checked').val();

      var params = '?months='+ months +'&current='+ curr_month +'&option='+ option;
      window.open('movements/rpt_incomes_expenses'+ params);
    },
  });

});