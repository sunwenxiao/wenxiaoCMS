//鼠标进入时间
function get_son_topbar($father_id){
	 $.ajax({
         type: "POST",
         url: "?m=main&a=get_son_topbar",
         data: "father_id=" + $father_id,
         success: function (data) {
        	 $("#son_topbar_"+$father_id).html(data);
         }

     })
	
}