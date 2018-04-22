/*global define*/
define(function (require) {

  "use strict";

  var Backbone = require('backbone'),
    HeaderView = require('app/views/header'),
    HomeView   = require('app/views/home'),
    AccountsView = require('app/views/accounts/index'),
    CategoriesView = require('app/views/categories/index'),
    SubCategoriesView = require('app/views/subcategories/index'),
    IncomesView = require('app/views/incomes/index'),
    ExpensesView = require('app/views/expenses/index'),
    MovAccountsView = require('app/views/movs_accounts/index'),
    RptMovementsView = require('app/views/rpt_movements/index'),
    RptMovsAccountsView = require('app/views/rpt_movs_accounts/index'),
    RptIncomesVsExpenses = require('app/views/rpt_incomes_expenses/index'),
    ProfileView = require('app/views/profile/edit'),
    ChartsView = require('app/views/charts/index');

  return Backbone.Router.extend({
    routes: {
      'cuentas'              : 'accounts',
      'clases'               : 'categories',
      'subclases'            : 'subcategories',
      'ingresos'             : 'incomes',
      'gastos'               : 'expenses',
      'mov_cuentas'          : 'mov_accounts',
      'rpt-movs'             : 'rpt_movements',
      'rpt-movs-accounts'    : 'rpt_movs_account',
      'rpt-incomes-expenses' : 'rpt_incomes_expenses',
      'graficas'             : 'charts',
      'profile'              : 'profile',
      '*default'             : 'home'
    },

    home: function () {
      this.showView(new HomeView([]));
    },
    accounts: function () {
      this.showView(new AccountsView([]), 'menu1', 'cuentas');
    },
    categories: function () {
      this.showView(new CategoriesView([]), 'menu1', 'clases');
    },
    subcategories: function () {
      this.showView(new SubCategoriesView([]), 'menu1','subclases');
    },
    incomes: function () {
      this.showView(new IncomesView([]), 'menu3', 'ingresos');
    },
    expenses: function () {
      this.showView(new ExpensesView([]), 'menu3', 'gastos');
    },
    mov_accounts: function () {
      this.showView(new MovAccountsView([]), 'menu3', 'mov_cuentas');
    },
    showView: function (view, opt, opt2) {
      $("body").html(new HeaderView([]).render().el);
      $("#main-container").html(view.render().el);
      this.setActiveOption(opt, opt2);
      if (view.onRender) {
        view.onRender();
      }
    },

    rpt_movements: function () {
      this.showView(new RptMovementsView([]), 'menu2','rpt-movs');
    },

    rpt_movs_account: function () {
      this.showView(new RptMovsAccountsView([]), 'menu2','rpt-movs-accounts');
    },

    rpt_incomes_expenses: function () {
      this.showView(new RptIncomesVsExpenses([]), 'menu2', 'rpt-incomes-expenses');
    },

    charts: function () {
      this.showView(new ChartsView([]), 'graficas');
    },

    profile: function () {
      this.showView(new ProfileView([]), 'profile');
    },

    setActiveOption: function (opt, opt2) {
      // set the active menu-option
      if (opt) {
        $('#navbar li a[href="#'+ opt +'"]').parent().addClass('active');
        if (opt) {
          $('#navbar li a[href="#'+ opt2 +'"]').parent().addClass('active');
        }
      } else {
        $('#navbar li a[href="#home"]').parent().addClass('active');
      }
    }

  });

});