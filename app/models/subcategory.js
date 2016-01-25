/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    Defaults = require('app/defaults');

  return Backbone.Model.extend({
    defaults: {
      id: null,
      nombre: '',
      categoria_id: 0,
      activo: true,
    },
    url: Defaults.ROUTE + 'subcategories/model',
    isActive: function () {
      if (parseInt(this.get('activo'), 10) === 1) {
        return true;
      }
      return false;
    },
    validate: function (attrs) {
      this.errors = [];
      if (!attrs.nombre) {
        this.errors.push({ field: 'Nombre', msg: 'NO puede estar vacío' });
      }
      if (!attrs.categoria_id) {
        this.errors.push({ field: 'Categoria', msg: 'Debe seleccionar una categoría' });
      }

      if (this.errors.length > 0) {
        return true;
      }
      return false;
    }
  });

});