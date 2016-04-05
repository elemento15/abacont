/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
      tpl = require('text!tpl/rpt_movements/index.htm');

  return Backbone.View.extend({
    tpl: tpl,
    className: 'index-container',
    events: {
      
    },

    initialize: function (params) {
      //this.collection = this.listCollection;
      //this.pagingOffset = 0;
      //this.filterTable = this.filterTable || [];
    },
    
    render: function () {
      var template = _.template(this.tpl);
      this.$el.html(template());
      return this;
    }

  });

});