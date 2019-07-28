/*global define*/
define(function (require) {
  "use strict";

  var FormView = require('app/views/_generic/form'),
    tpl   = require('text!tpl/categories/form.htm'),
    Model = require('app/models/category');

  return FormView.extend({
    tpl: tpl,
    moduleName: 'categories',
    Model: Model,
    
    setForm: function () {
      this.$("input[name=nombre]").val(this.model.get('nombre'));
      this.$("select[name=tipo]").val(this.model.get('tipo'));
      this.$("input[name=activo]").attr('checked', this.model.isActive());
    },
    
    getForm: function () {
      this.model.set('nombre', this.$("input[name=nombre]").val());
      this.model.set('tipo', this.$("select[name=tipo]").val());
      this.model.set('activo', (this.$("input[name=activo]:checked").length));
    },

    afterRender: function () {
      if (this.options.recId) {
        this.$("select[name=tipo]").attr('disabled', 'disabled');
      }
    }
  });

});