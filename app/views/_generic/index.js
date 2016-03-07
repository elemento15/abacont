/*global define*/
/*global App*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    tplNewBtn = require('text!tpl/_partials/new_button.htm'),
    tplSearch = require('text!tpl/_partials/search_field.htm'),
    tplStatus = require('text!tpl/_partials/filter_active.htm'),
    tplCancel = require('text!tpl/_partials/filter_cancel.htm'),
    tplPaging = require('text!tpl/_partials/pagination.htm');

  return Backbone.View.extend({
    className: 'index-container',
    events: {
      'click button.cmd-add' : 'add_record',
      'click .order-field'   : 'orderData',
      'keyup .div-search'    : 'search',
      'click .clear-search'  : 'clearSearch',
      'change .list-filter'  : 'changeFilter',
      'click .cls-prev'      : 'prevPage',
      'click .cls-next'      : 'nextPage',
      'click .cls-first'     : 'firstPage',
      'click .cls-last'      : 'lastPage'
    },
    initialize: function (params) {
      this.collection = this.listCollection;
      this.pagingOffset = 0;
      this.filterTable = this.filterTable || [];

      if (this.onInit) {
        this.onInit();
      }
    },
    add_record: function () {
      $("#main-container .index-container").hide();
      var view = new this.FormView({ recId: null, listView: this });
      $("#main-container").append(view.render().el);
      if (view.onRender) {
        view.onRender();
      }
    },
    render: function () {
      var template = _.template(this.tpl);
      this.$el.html(template({
        tplNewBtn: _.template(tplNewBtn),
        tplSearch: _.template(tplSearch),
        tplStatus: _.template(tplStatus),
        tplCancel: _.template(tplCancel),
        tplPaging: _.template(tplPaging)
      }));
      this.loadCollection(true);
      return this;
    },
    loadCollection: function (reset) {
      var that = this,
        view;

      App.block();
      this.collection.fetch({
        reset: reset,
        type: 'POST',
        data: { 
          order: that.orderTable || { field: 'nombre', type: 'ASC' }, 
          search: that.searchTable || '', 
          filter: that.filterTable || [],
          start: (that.pagingOffset || 0) * (that.paging || 0),
          length: that.paging || 0
        },
        success: function () {
          that.showCurrentPage();
          if (reset) {
            that.$el.find("tbody.main-data-list").html('');
          }

          _.each(that.collection.models, function (rec) {
            view = new that.RowView({ model: rec, listView: that });
            that.$el.find("tbody.main-data-list").append(view.render().el);
          });
          App.unblock();

          if (that.onLoadCollection) {
            that.onLoadCollection();
          }
        },
        error: function (collection, response) {
          App.unblock();
          that.$el.find("tbody.main-data-list").html(response.responseText);
        }
      });
    },
    orderData: function (evt) {
      var el = $(evt.currentTarget),
        order_field = el.attr('orderfield'),
        order_type = el.attr('ordertype'),
        tdElement;

      $('.order-field span').removeClass('glyphicon-arrow-down');
      $('.order-field span').removeClass('glyphicon-arrow-up');
      tdElement = el.find('span.glyphicon');

      if (order_type === '') {
        $(".order-field").attr('ordertype', '');
        el.attr('ordertype', 'ASC');
        tdElement.addClass('glyphicon-arrow-up');
        this.orderTable = { field: order_field, type: 'ASC' };
      }

      if (order_type === 'ASC') {
        el.attr('ordertype', 'DESC');
        tdElement.addClass('glyphicon-arrow-down');
        this.orderTable = { field: order_field, type: 'DESC' };
      }

      if (order_type === 'DESC') {
        el.attr('ordertype', 'ASC');
        tdElement.addClass('glyphicon-arrow-up');
        this.orderTable = { field: order_field, type: 'ASC' };
      }

      this.loadCollection(true);
    },
    search: function (evt) {
      var search_text = evt.currentTarget.value;
      // search only if >= 3 characters
      if (evt.keyCode === 13 && (search_text.length >= 3 || search_text.length === 0)) {
        this.searchTable = search_text;
        this.pagingOffset = 0;
        this.loadCollection(true);
      }
    },
    clearSearch: function () {
      var search = this.$el.find('.div-search');
      if (search.val().length > 0) {
        search.val('');
        this.searchTable = '';
        this.pagingOffset = 0;
        this.loadCollection(true);
      }
      search.select();
    },
    changeFilter: function (evt) {
      var el = $(evt.currentTarget),
        field = el.attr('name'),
        value = el.val();

      if (value !== '') {
        this.setFilter(field, value);
      } else {
        this.unsetFilter(field);
      }
      this.pagingOffset = 0;
      this.loadCollection(true);
    },
    setFilter: function (field, value) {
      var exists = false;

      _.each(this.filterTable, function (rec) {
        if (rec.field === field) {
          rec.value = value;
          exists = true;
        }
      });

      if (!exists) {
        this.filterTable.push({ field: field, value: value });
      }
    },
    unsetFilter: function (field) {
      var idx = null;

      _.map(this.filterTable, function (rec, index) {
        if (rec.field === field) {
          idx = index;
        }
      });

      if (idx !== null) {
        this.filterTable.splice(idx, 1);
      }
    },
    prevPage: function (evt) {
      evt.preventDefault();
      if (this.pagingOffset > 0) {
        this.pagingOffset--;
        this.loadCollection(true);
      }
    },
    nextPage: function (evt) {
      var count = this.collection.response.count;
      var last = Math.ceil(count / this.paging) - 1;

      evt.preventDefault();
      if (this.pagingOffset < last) {
        this.pagingOffset++;
        this.loadCollection(true);
      }
    },
    firstPage: function (evt) {
      evt.preventDefault();
      this.pagingOffset = 0;
      this.loadCollection(true);
    },
    lastPage: function (evt) {
      var count = this.collection.response.count;
      var last = Math.ceil(count / this.paging) - 1;
      
      if (last >= 0) {
        evt.preventDefault();
        this.pagingOffset = last;
        this.loadCollection(true);
      }
    },
    showCurrentPage: function () {
      var page = this.pagingOffset + 1;
      var count = this.collection.response.count;
      var last = Math.ceil(count / this.paging);

      this.$el.find(".cls-pagination .cls-current-page a").text(page + ' / ' + last);
    }
  });

});