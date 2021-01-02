/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    Defaults = require('app/defaults');

  return Backbone.Model.extend({
    defaults: {
      id: null,
      nombre: '',
      tipo: '',
      activo: true,
      usa_gastos: false,
      usa_ingresos: false,
      num_tarjeta: '',
      num_cuenta: '',
      saldo: 0,
      orden: '100',
      observaciones: ''
    },
    url: Defaults.ROUTE + 'accounts/model',
    isActive: function () {
      if (parseInt(this.get('activo'), 10) === 1) {
        return true;
      }
      return false;
    },
    forExpenses: function () {
      if (parseInt(this.get('usa_gastos'), 10) === 1) {
        return true;
      }
      return false;
    },
    forIncomes: function () {
      if (parseInt(this.get('usa_ingresos'), 10) === 1) {
        return true;
      }
      return false;
    },
    validate: function (attrs) {
      this.errors = [];
      if (!attrs.nombre) {
        this.errors.push({ field: 'Nombre', msg: 'NO puede estar vacío' });
      }
      if (!attrs.tipo) {
        this.errors.push({ field: 'Tipo', msg: 'Debe seleccionar un tipo' });
      }

      if (this.errors.length > 0) {
        return true;
      }
      return false;
    }
  });

});