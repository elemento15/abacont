/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    Defaults = require('app/defaults'),
    Movement = require('app/models/movement');

  return Backbone.Collection.extend({
    model: Movement,
    url: Defaults.ROUTE + 'movements/read',
    parse: function (response) {
    	this.response = response;
    	return response.data;
    }
  });

});