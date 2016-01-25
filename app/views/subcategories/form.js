/*global define*/
define(function (require) {
  "use strict";

  var FormView = require('app/views/_generic/form'),
    tpl   = require('text!tpl/subcategories/form.htm'),
    Model = require('app/models/subcategory'),
    Defaults = require('app/defaults');

  return FormView.extend({
    tpl: tpl,
    moduleName: 'subcategories',
    Model: Model,
    
    setForm: function () {
      this.$("input[name=nombre]").val(this.model.get('nombre'));
      this.$("select[name=categoria_id]").val(this.model.get('categoria_id'));
      this.$("input[name=activo]").attr('checked', this.model.isActive());
    },
    
    getForm: function () {
      this.model.set('nombre', this.$("input[name=nombre]").val());
      this.model.set('categoria_id', this.$("select[name=categoria_id]").val());
      this.model.set('activo', (this.$("input[name=activo]:checked").length));
    },

    onInit: function () {
      var that = this; 
      
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'categories/actives',
          dataType: 'json'
        })
      ).then(function (data, textStatus, jqXHR) {
        var nombre;
        data.forEach(function (item) {
          nombre = item.nombre + ' (' + ((item.tipo == 'I') ? 'Ingreso' : 'Gasto') + ')';
          that.$("select[name=categoria_id]").append('<option value="'+ item.id +'">'+ nombre +'</option>')
        });
      });
    }
  });

});