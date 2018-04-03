
Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
		c = isNaN(c = Math.abs(c)) ? 2 : c, 
		d = d == undefined ? "." : d, 
		t = t == undefined ? "," : t, 
		s = n < 0 ? "-" : "", 
		i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
		j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

String.prototype.formatMoney = function (c, d, t) {
	var n = this;
	return parseFloat(n).formatMoney(c, d, t);
}

String.prototype.getDayOfWeek = function () {
	var sp = this.split('-');
	var dt = new Date(parseInt(sp[0]), parseInt(sp[1]) - 1, parseInt(sp[2]));
	var day;
	
	switch (dt.getDay()) {
		case 0 : day = 'Domingo';   break;
		case 1 : day = 'Lunes';     break;
		case 2 : day = 'Martes';    break;
		case 3 : day = 'Miércoles'; break;
		case 4 : day = 'Jueves';    break;
		case 5 : day = 'Viernes';   break;
		case 6 : day = 'Sábado';    break;
	}
	return day;
}