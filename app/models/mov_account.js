/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    Defaults = require('app/defaults');

  return Backbone.Model.extend({
    defaults: {
      id: null,
      fecha: '',
      cuenta_id: 0,
      tipo: '',
      cancelado: 0,
      concepto: '',
      observaciones: ''
    },
    url: Defaults.ROUTE + 'movs_accounts/model',
    isActive: function () {
      if (parseInt(this.get('cancelado'), 10) === 0) {
        return true;
      }
      return false;
    },
    validate: function (attrs) {
      this.errors = [];

      if (this.errors.length > 0) {
        return true;
      }
      return false;
    }
  });

});