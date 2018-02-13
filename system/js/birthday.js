function birthday_init(bir){
	s = y = m = d = '';	
	if( bir!= undefined){
		b = bir.split('-');
	}else{
		b = new Array()	
	}
	for (i = (new Date().getFullYear()); i > (new Date().getFullYear()) - 90; i--) {
		s = i == b[0] ?  "selected='selected'":'';
		y += "<option value='" + i + "'" + s + ">" + i + "</option>";
	}
	
	for (i = 1; i < 13; i++) {
		s = i == b[1] ?  "selected='selected'":'';
		i < 10 ? (i = '0' + i) : (j = i + '0');
		m += "<option value='" + i + "'" + s + ">" + i + "</option>";
	}
	
	for (i = 1; i < 32; i++) {
		s = i == b[2]?  "selected='selected'":'';
		i < 10 ? (i = '0' + i) : (j = i + '0');
		d += "<option value='" + i + "'" + s + ">" + i + "</option>";
	}
	str  ='<input type="hidden" name="birthday" class="birthday" />'
	str +='<select name="_year" onchange="fill_birthday()">'  + y + '</select>年';
	str +='<select name="_month" onchange="fill_birthday()">' + m + '</select>月';
	str +='<select name="_date" onchange="fill_birthday()">'  + d + '</select>日';
	$('#birthday').html(str);
	fill_birthday();
}
function fill_birthday() {
	$(".birthday").val($("[name='_year']").val() + '-' + $("[name='_month']").val() + '-' + $("[name='_date']").val());
}