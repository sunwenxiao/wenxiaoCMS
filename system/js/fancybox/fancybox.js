$(function(){
	src=$("script[src$='fancybox.js']").attr('src');
	imgurl=src.replace('fancybox.js','');
	str = '<style>\
	body{ position:relative}\
	.fancy_box_mask { position:absolute; top:0px; left:0px; height:100%; width:100%; background-color:#777777; z-index:999999; display:none }\
	.fancy_box_container{ position:absolute; z-index:9999999; top:50%; left:50%; height:auto; \
	 width:auto; background-color:#fff; border:10px solid #fff; display:none }\
	.fancy_box_close_btn { position:absolute; z-index:99999999; height:30px; width:30px; right:-20px;\
	top:-20px; background-image:url('+imgurl+'close.png); cursor:pointer; cursor:hand }\
	.fancy_box_close_btn { *background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader\(src="'+imgurl+'close.png", sizingMethod="scale"); }\
	</style>\
	<div class="fancy_box_mask"></div>\
	<div class="fancy_box_container">\
	<div class="content"></div>\
	<div class="fancy_box_close_btn"></div>\
	</div>';
	$('body').append(str);
	$('.fancy_box_close_btn,.fancy_box_mask').click(function(){
		close_fancybox();
	})
})

function open_fancybox( imgurl ){
	img = "<img src='"+imgurl+"' onload='display_fancybox()' />";
	$('.fancy_box_container .content').html(img);
}

function display_fancybox(){
	h = $('.fancy_box_container').css('height');
	h = h.replace('px','');
	w = $('.fancy_box_container').css('width');
	w = w.replace('px','');
	$('.fancy_box_container').css('margin-left',-(w/2)+'px');
	pos=(document.documentElement.clientHeight-h)/2+$(document).scrollTop()-10+'px';
	$(".fancy_box_container").css('top',pos);
	mh=document.body.clientHeight>=document.documentElement.clientHeight?document.body.clientHeight:document.documentElement.clientHeight ;
	$('.fancy_box_mask').css('height',mh+'px');
	$('.fancy_box_mask').css('opacity','0.7').show();
	$('.fancy_box_container').show();
}

function close_fancybox(){
	$('.fancy_box_container').hide();
	$('.fancy_box_mask').hide();
}
