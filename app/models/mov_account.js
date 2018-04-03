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
      importe: 0,
      cancelado: 0,
      concepto: '',
      automatico: 0,
      observaciones: '',
      tipo_nombre: function() {
        return (this.tipo == 'A') ? 'Abono' : 'Cargo';
      },
      dayOfWeek: function() {
        return this.fecha.getDayOfWeek();
      }
    },
    url: Defaults.ROUTE + 'movs_accounts/model',
    urlSave: Defaults.ROUTE + 'movs_accounts/save_mov_account',
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

      if (attrs.tipo != 'A' && attrs.tipo != 'C') {
        this.errors.push({ field: 'Tipo', msg: 'Debe ser Abono ó Cargo' });
      }

      if (! attrs.cuenta_id) {
        this.errors.push({ field: 'Cuenta', msg: 'Debe seleccionar una cuenta' });
      }

      if (isNaN(attrs.importe)) {
        this.errors.push({ field: 'Importe', msg: 'Debe indicar un valor válido' });
      } else if (attrs.importe <= 0) {
        this.errors.push({ field: 'Importe', msg: 'Debe ser mayor a cero' });
      }

      if (! attrs.concepto) {
        this.errors.push({ field: 'Concepto', msg: 'No puede estar vacío' });
      }

      if (this.errors.length > 0) {
        return true;
      }
      return false;
    }
  });

});