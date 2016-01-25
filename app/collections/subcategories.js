/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    Defaults = require('app/defaults'),
    Category = require('app/models/subcategory');

  return Backbone.Collection.extend({
    model: Category,
    url: Defaults.ROUTE + 'subcategories/read',
    parse: function (response) {
    	this.response = response;
    	return response.data;
    }
  });

});