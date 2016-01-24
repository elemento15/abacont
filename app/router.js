/*global define*/
define(function (require) {

  "use strict";

  var Backbone = require('backbone'),
    HeaderView   = require('app/views/header'),
    HomeView     = require('app/views/home');

  return Backbone.Router.extend({
    routes: {
      // 'products'   : 'products',
      // 'categories' : 'categories',
      // 'users'      : 'users',
      '*default'   : 'home'
    },

    home: function () {
      this.showView(new HomeView([]));
    },
    // products: function () {
    //   this.showView(new ProductsView([]), 'products', 'Products');
    // },
    // categories: function () {
    //   this.showView(new CategoriesView([]), 'categories', 'Categories');
    // },
    // users: function () {
    //   this.showView(new UsersView([]), 'users', 'Users');
    // },
    showView: function (view, opt, title) {
      // $(document).find('title').html('Gift Shop - ' + (title || 'Home'));
      $("body").html(new HeaderView([]).render().el);
      $("#main-container").html(view.render().el);
      this.setActiveOption(opt);
    },
    setActiveOption: function (opt) {
      // set the active menu-option
      // if (opt) {
      //   $("#main-menu-opts li.opt-" + opt).addClass("active");
      // } else {
      //   $("#main-menu-opts li.opt-home").addClass("active");
      // }
    }

  });

});