/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    tpl      = require('text!tpl/header.htm'),
    template = _.template(tpl),
    Defaults = require('app/defaults');;

  return Backbone.View.extend({
    events: {
      'click #optLogOut' : 'logout'
    },

    render: function () {
      this.$el.html(template());
      this.getUserData();
      return this;
    },

    logout: function () {
      if (confirm('Close session?')) {
        $.ajax({
          url: Defaults.ROUTE + 'main/logout',
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
    },

    getUserData: function () {
      if (! App.User) {
        $.when(
          $.ajax({
            url: Defaults.ROUTE + 'main/get_user',
            type: 'POST',
            dataType: 'json'
          })
        ).then(function (data, textStatus, jqXHR) {
          App.User = data.user;
          $('#userName').text(App.User.display || '');
        });
      }
    }

  });

});