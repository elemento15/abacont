require.config({
  baseUrl: 'libs',
  urlArgs: "bust=" + (new Date()).getTime(),

  paths: {
    app:  '../app',
    tpl:  '../app/templates'
  },

  shim: {
    'app/functions': {
      exports: 'Functions'
    },
    'backbone': {
      deps: ['underscore', 'jquery'],
      exports: 'Backbone'
    },
    'underscore': {
      exports: '_'
    },
    'bootstrap': {
      deps: ['jquery'],
      exports: 'Bootstrap'
    }
  }
});

require([
  'jquery',
  'jquery.blockUI',
  'underscore',
  'backbone',
  'bootstrap',
  'app/functions',
  'app/router'
],
  function ($, BlockUI, _, Backbone, Bootstrap, Functions, Router) {
    App = {};

    // define functions for page blockers
    App.block = function (msg) {
      var text = msg || 'Espere un momento';
      $.blockUI({
        message: '<h5><img src="img/waiting24.gif" /> ' + text + '</h5>',
        css: {
          width: '20%'
        },
        overlayCSS: {
          opacity: 0.1
        }
      });
    };
    App.unblock = function () {
      $.unblockUI();
    };

    App.router = new Router();
    Backbone.history.start();
    Backbone.emulateHTTP = true;
    Backbone.emulateJSON = true;
  });