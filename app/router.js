/*global define*/
define(function (require) {

  "use strict";

  var Backbone = require('backbone'),
    HeaderView = require('app/views/header'),
    HomeView   = require('app/views/home'),
    AccountsView = require('app/views/accounts/index'),
    CategoriesView = require('app/views/categories/index'),
    SubCategoriesView = require('app/views/subcategories/index');

  return Backbone.Router.extend({
    routes: {
      'cuentas'   : 'accounts',
      'clases'    : 'categories',
      'subclases' : 'subcategories',
      '*default'  : 'home'
    },

    home: function () {
      this.showView(new HomeView([]));
    },
    accounts: function () {
      this.showView(new AccountsView([]), 'cuentas');
    },
    categories: function () {
      this.showView(new CategoriesView([]), 'clases');
    },
    subcategories: function () {
      this.showView(new SubCategoriesView([]), 'subclases');
    },
    showView: function (view, opt) {
      $("body").html(new HeaderView([]).render().el);
      $("#main-container").html(view.render().el);
      this.setActiveOption(opt);
    },
    setActiveOption: function (opt) {
      // set the active menu-option
      if (opt) {
        $('#navbar li a[href="#'+ opt +'"]').parent().addClass('active');
      } else {
        $('#navbar li a[href="#home"]').parent().addClass('active');
      }
    }

  });

});