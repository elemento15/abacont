/*global define*/
define(function (require) {
  "use strict";

  var RowView = require('app/views/_generic/row'),
    FormView = require('app/views/movs_accounts/form'),
    tpl      = require('text!tpl/movs_accounts/row.htm'),
    Defaults = require('app/defaults');

  return RowView.extend({
    tpl: tpl,
    FormView: FormView,
    hasActiveAttr: true,

    events: function(){
      return _.extend({},RowView.prototype.events,{
        'click span.cls-mov-auto' : 'showMovement'
      });
    },

    showMovement: function (evt) {
      var id_mov_acc = this.model.id;

      $.when(
        $.ajax({
          url: Defaults.ROUTE + 'movements/find_by_mov_account',
          dataType: 'json',
          data: { id: id_mov_acc },
          method: 'POST'
        })
      ).then(function (data, textStatus, jqXHR) {
        if (! data.success) {
          alert(data.msg || 'Error reading movement');
        } else {
          $('#dialog-movement').find('[attr="type"]').text( (data.data.tipo == 'G') ? 'GASTO' : 'INGRESO' );
          $('#dialog-movement').find('[attr="date"]').text(data.data.fecha);
          $('#dialog-movement').find('[attr="total"]').text(data.data.importe.formatMoney());
          $('#dialog-movement').find('[attr="category"]').text(data.data.nombre_categoria);
          $('#dialog-movement').find('[attr="subcategory"]').text(data.data.nombre_subcategoria);
          $('#dialog-movement').find('[attr="comments"]').text(data.data.observaciones);
          $('#dialog-movement').find('[attr="status"]').text( (parseInt(data.data.cancelado)) ? 'CANCELADO' : 'ACTIVO' );

          $('#dialog-movement').modal();
        }
      });
    }
  });

});