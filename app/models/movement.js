/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    Defaults = require('app/defaults');

  return Backbone.Model.extend({
    defaults: {
      id: null,
      fecha: '',
      tipo: '',
      movimiento_cuenta_id: 0,
      subcategoria_id: 0,
      importe: 0,
      cancelado: 0,
      observaciones: ''
    },
    url: Defaults.ROUTE + 'movements/model',
    urlSave: Defaults.ROUTE + 'movements/save_movement',
    isActive: function () {
      if (parseInt(this.get('cancelado'), 10) === 0) {
        return true;
      }
      return false;
    },
    validate: function (attrs) {
      this.errors = [];

      if (!attrs.fecha) {
        this.errors.push({ field: 'Fecha', msg: 'NO puede estar vacía' });
      }

      if (!attrs.cuenta_id) {
        this.errors.push({ field: 'Cuenta', msg: 'Debe seleccionar una cuenta' });
      }

      if (!attrs.subcategoria_id) {
        this.errors.push({ field: 'Subcategoría', msg: 'Debe seleccionar una subcategoría' });
      }

      if (attrs.importe < 0) {
        this.errors.push({ field: 'Importe', msg: 'Inválido' });
      }

      if (this.errors.length > 0) {
        return true;
      }
      return false;
    }
  });

});