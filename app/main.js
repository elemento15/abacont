require.config({
  baseUrl: 'libs',
  urlArgs: "v=1.6",
  
  // uncomment in development mode, avoid cached JS
  //urlArgs: "v=" + (new Date()).getTime(),

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
    },
    'datepicker': {
      deps: ['bootstrap'],
      exports: 'DatePicker'
    },
    'datepicker-es': {
      deps: ['datepicker'],
      exports: 'DatePickerEs'
    },
    'canvasjs': {
      exports: 'CanvasJS'
    }
  }
});

require([
  'jquery',
  'jquery.blockUI',
  'underscore',
  'backbone',
  'bootstrap',
  'datepicker',
  'datepicker-es',
  'app/functions',
  'app/router'
],
  function ($, BlockUI, _, Backbone, Bootstrap, DatePicker, DatePickerEs, Functions, Router) {
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

    // User data
    App.User = false;

    App.router = new Router();
    Backbone.history.start();
    Backbone.emulateHTTP = true;
    Backbone.emulateJSON = true;
  });