/*global define*/
/*global App*/
/*global confirm*/
/*global alert*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    tplBtns  = require('text!tpl/_partials/form_buttons.htm'),
    Defaults = require('app/defaults');

  return Backbone.View.extend({
    className: 'form-container',

    events: {
      'click button.cmd-save'   : 'saveModel',
      'click button.cmd-back'   : 'closeForm',
      'click button.cmd-remove' : 'deleteModel',
      'click button.cmd-cancel' : 'cancelModel',
      'click .div-errors' : 'closeErrorsPanel'
    },
    initialize: function () {
      var that = this,
        id;

      that.options = arguments[0];
      id = that.options.recId

      this.model = new this.Model();

      if (this.onInit) {
        this.onInit();
      }

      if (id) {
        App.block();
        that.model.fetch({
          data: { id: id },
          success: function (model, response, options) {
            that.setForm();

            App.unblock();
            
            if (that.onLoad) {
              that.onLoad();
            }
          },
          error: function (model, response, options) {
            App.unblock();
            // @LMNT: Actions on error
            alert('Error: '+ response.statusText);
          }
        });
      }
    },
    saveModel: function () {
      var that = this;

      this.closeErrorsPanel();
      this.getForm();

      if (that.model.isValid()) {
        that.disableForm();
        that.model.save(null, {
          url: that.model.urlSave || that.model.url,
          success: function (model, response, jqXHR) {
            that.enableForm();
            if (response.success) {
              that.closeForm(true);
            } else {
              alert('Error: '+ response.msg);
            }
          },
          error: function (model, response, jqXHR) {
            that.enableForm();
            alert('Error: '+ response.statusText);
          }
        });
      } else {
        this.renderErrors(that.model.errors);
      }
    },
    closeForm: function (load) {
      $("#main-container .index-container").fadeIn();
      if (load === true) {
        this.options.listView.loadCollection(true);
      }
      this.undelegateEvents();
      Backbone.View.prototype.remove.call(this);
    },
    deleteModel: function () {
      var that = this;

      if (confirm('Do you want to delete the current record?')) {
        $.ajax({
          url: Defaults.ROUTE + that.moduleName + '/remove',
          type: 'POST',
          data: { id: that.model.id },
          success: function () {
            App.unblock();
            alert('Record deleted');
            that.closeForm(true);
          }
        });
      }
    },
    cancelModel: function () {
      var that = this;

      if (confirm('Do you want to cancel the current record?')) {
        $.ajax({
          url: Defaults.ROUTE + that.moduleName + '/cancel',
          type: 'POST',
          data: { id: that.model.id },
          success: function () {
            App.unblock();
            alert('Record canceled');
            that.closeForm(true);
          }
        });
      }
    },
    render: function () {
      var template = _.template(this.tpl);
      this.$el.html(template({
        recId: this.options.recId,
        tplBtns: _.template(tplBtns)
      }));

      if (this.afterRender) {
        this.afterRender();
      }

      return this;
    },
    disableForm: function () {
      this.$el.find('fieldset').attr('disabled', 'disabled');
    },
    enableForm: function () {
      this.$el.find('fieldset').removeAttr('disabled');
    },
    renderErrors: function (errors) {
      var that = this;
      that.$el.find('.div-errors').css('display', 'block');
      _.each(errors, function (err) {
        that.$el.find('.div-errors').append('<div><b>' + err.field + ':</b> ' + err.msg + '</div>');
      });
      that.$el.find('.div-errors').delay(5000).fadeOut('slow');
    },
    closeErrorsPanel: function () {
      this.$el.find('.div-errors').html('');
      this.$el.find('.div-errors').css('display', 'none');
    },
    setForm: function () {
      alert('method setForm not implemented yet.');
    },
    getForm: function () {
      alert('method getForm not implemented yet.');
    }
  });

});