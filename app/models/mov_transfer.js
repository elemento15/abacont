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
      cuenta_id_destino: 0,
      importe: 0,
      concepto: '',
      observaciones: ''
    },
    url: Defaults.ROUTE + 'movs_accounts/model',
    urlSave: Defaults.ROUTE + 'movs_accounts/save_transfer',
    
    validate: function (attrs) {
      this.errors = [];

      if (!attrs.fecha) {
        this.errors.push({ field: 'Fecha', msg: 'NO puede estar vacía' });
      }

      if (! attrs.cuenta_id) {
        this.errors.push({ field: 'Cuenta Origen', msg: 'Debe seleccionar una cuenta' });
      }

      if (! attrs.cuenta_id_destino) {
        this.errors.push({ field: 'Cuenta Destino', msg: 'Debe seleccionar una cuenta' });
      }

      if (attrs.cuenta_id == attrs.cuenta_id_destino) {
        this.errors.push({ field: 'Cuenta Origen y Cuenta Destino', msg: 'Debe ser diferentes' });
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