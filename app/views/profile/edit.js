/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
      tpl = require('text!tpl/profile/edit.htm'),
      Defaults = require('app/defaults');

  return Backbone.View.extend({
    tpl: tpl,
    className: 'index-container',
    events: {
      'click .btnSaveUser' : 'saveUser',
      'click .btnSavePass' : 'savePass'
    },

    initialize: function (params) {
      // TODO
      this.showedChart02 = false;
    },
    
    render: function () {
      var template = _.template(this.tpl);
      this.$el.html(template());
      return this;
    },

    onRender: function () {
      this.getUserData();
    },

    saveUser: function () {
      var data = {
        user:  this.$("input[name=usuario]").val(),
        name:  this.$("input[name=nombre]").val(),
        email: this.$("input[name=email]").val()
      };

      App.block();

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'main/update_user',
          type: 'POST',
          dataType: 'json',
          data: data
        })
      ).then(function (data, textStatus, jqXHR) {
        App.unblock();
        App.User = data.user;
      });
    },

    savePass: function () {
      var that = this;

      var data = {
        pass:  this.$("input[name=password]").val(),
        confirm:  this.$("input[name=confirmation]").val()
      };

      App.block();

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'main/change_pass',
          type: 'POST',
          dataType: 'json',
          data: data
        })
      ).then(function (data, textStatus, jqXHR) {
        App.unblock();

        if (data.success) {
          that.$("input[name=password]").val(''),
          that.$("input[name=confirmation]").val(''),

          alert('Contraseña modificada con exito');
        } else {
          alert(data.error);
        }

      }, function () {
        App.unblock();
        alert('Error on server');
      });
    },

    getUserData: function () {
      var that = this;

      App.block();

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'main/get_user',
          type: 'POST',
          dataType: 'json'
        })
      ).then(function (data, textStatus, jqXHR) {
        that.$("input[name=usuario]").val(data.user.user);
        that.$("input[name=nombre]").val(data.user.name);
        that.$("input[name=email]").val(data.user.email);

        App.unblock();
      });
    }

  });

});