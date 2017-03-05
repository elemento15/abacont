/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    tpl      = require('text!tpl/header.htm'),
    template = _.template(tpl);

  return Backbone.View.extend({
    events: {
      'click #optLogOut' : 'logout'
    },

    render: function () {
      this.$el.html(template());
      return this;
    },

    logout: function () {
      if (confirm('Close session?')) {
        $.ajax({
          url: 'main/logout',
          type: 'POST',
          dataType: 'json',
          success: function (response) {
            if (response.success) {
              location.href = './';
            } else {
              alert(response.msg);
            }
          }
        });
      }
    }
  });

});