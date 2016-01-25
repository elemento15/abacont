/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone');

  return Backbone.View.extend({
    tagName: 'tr',
    hasActiveAttr: false,

    className: function () {
      if (!this.model.isActive() && this.hasActiveAttr) {
        return "inactive";
      }
    },

    events: {
      'mouseover'  : 'over_record',
      'mouseleave' : 'leave_record',
      'click .row-btn-edit' : 'edit_record'
    },

    initialize: function () {
      var that = this;
      that.options = arguments[0];
    },

    render: function () {
      var template = _.template(this.tpl);
      this.$el.html(template(this.model.attributes));
      return this;
    },

    over_record: function () {
      this.$el.find(".row-btn-edit").css('visibility', '');
    },

    leave_record: function () {
      this.$el.find(".row-btn-edit").css('visibility', 'hidden');
    },

    edit_record: function () {
      $("#main-container .index-container").hide();
      var view = new this.FormView({ recId: this.model.id, listView: this.options.listView });
      $("#main-container").append(view.render().el);
    }

  });

});