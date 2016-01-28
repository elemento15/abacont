/*global define*/
define(function (require) {
  "use strict";

  var Backbone = require('backbone'),
    Defaults = require('app/defaults'),
    MovAccount = require('app/models/mov_account');

  return Backbone.Collection.extend({
    model: MovAccount,
    url: Defaults.ROUTE + 'movs_accounts/read',
    parse: function (response) {
    	this.response = response;
    	return response.data;
    }
  });

});