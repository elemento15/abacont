/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    Defaults = require('app/defaults'),
    Account = require('app/models/account');

  return Backbone.Collection.extend({
    model: Account,
    url: Defaults.ROUTE + 'accounts/read',
    parse: function (response) {
    	this.response = response;
    	return response.data;
    }
  });

});