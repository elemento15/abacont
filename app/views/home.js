/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    tpl      = require('text!tpl/home.htm'),
    template = _.template(tpl);

  return Backbone.View.extend({
    render: function () {
      this.$el.html(template());
      return this;
    }
  });

});