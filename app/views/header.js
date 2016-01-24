/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    tpl      = require('text!tpl/header.htm'),
    template = _.template(tpl);

  return Backbone.View.extend({
    events: {
      // 'click a[href="#sign-out"]' : 'signOut'
    },

    render: function () {
      this.$el.html(template());
      return this;
    } //,

    // signOut: function () {
    //   if (confirm('Close session?')) {
    //     $.ajax({
    //       url: 'users/logout',
    //       type: 'POST',
    //       dataType: 'json',
    //       success: function (response) {
    //         if (response.success) {
    //           location.href = './';
    //         } else {
    //           alert(response.msg);
    //         }
    //       }
    //     });
    //   }
    // }
  });

});