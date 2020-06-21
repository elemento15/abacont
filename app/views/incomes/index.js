/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var IndexView = require('app/views/_generic/index'),
    tpl       = require('text!tpl/incomes/index.htm'),
    FormView  = require('app/views/incomes/form'),
    RowView   = require('app/views/incomes/row'),
    ListCollection = require('app/collections/movements'),
    Defaults  = require('app/defaults');

  return IndexView.extend({
    tpl: tpl,
    FormView: FormView,
    RowView: RowView,
    listCollection: new ListCollection(),
    paging: 10,
    orderTable: { field: 'fecha', type: 'DESC' },
    orderById: true,

    events: function(){
      return _.extend({},IndexView.prototype.events, {
        'change [name="subcategorias.categoria_id"]' : 'changeCategory'
      });
    },

    changeCategory: function (evt) {
      var category = evt.currentTarget.value;
      this.fillSubcategories(category);
      $('[name="subcategorias.id"]').val('').trigger('change');

    },

    fillSubcategories: function (category) {
      var that = this;
      
      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'subcategories/actives',
          type: 'POST',
          dataType: 'json',
          data: { category_id: category }
        })
      ).then(function (data, textStatus, jqXHR) {
        that.$("select[name='subcategorias.id']").html('<option value="">Todos</option>');
        
        data.forEach(function (item) {
          that.$("select[name='subcategorias.id']").append('<option value="'+ item.id +'">'+ item.nombre +'</option>')
        });
      });
    },

    onInit: function () {
      var that = this;

      this.filterTable = [
        { field: 'tipo', value: 'I' }, 
        { field: 'cancelado', value: 0 }
      ];

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'categories/actives',
          dataType: 'json',
          method: 'POST',
          data: { type: 'I' }
        })
      ).then(function (data, textStatus, jqXHR) {
        data.forEach(function (item) {
          that.$("select[name='subcategorias.categoria_id']").append('<option value="'+ item.id +'">'+ item.nombre +'</option>')
        });

        that.loadCollection(true);
      });
    }
  });

});