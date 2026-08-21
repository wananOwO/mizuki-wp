jQuery(function($){
	$(".erphpdown-select-postid").each(function(){
		var that = $(this);
        setTimeout(function(){
            that.after("<div class='erphpdown-select-posts' style='display:inline-block;position:relative;'><input type='text' id='erphpdown-select-postid' placeholder='辅助搜索文章关键字' /><a href='javascript:;' class='button erphpdown-select-postid-do'>搜索</a> <a href='javascript:;' class='erphpdown-select-posts-clear'>清空</a><br><div class='erphpdown-select-postss' style='position:absolute;left:0;top:calc(100% + 5px);background: #fff;border-radius: 3px;padding: 5px;max-height: 200px;overflow-y: scroll;font-size:12px;width:100%;'></div><style>.erphpdown-select-postss:empty{display:none}</style></div>");
        },1000);
		
	});

	$("body").on("click", ".erphpdown-select-posts-clear", function(){
		$("#erphpdown-select-postid").val('');
		$(".erphpdown-select-postss").html('');
	});

	$("body").on("click", ".erphpdown-select-postid-do", function(){
    	var post_search = $("#erphpdown-select-postid").val();
        if(post_search){

            $.post(ajaxurl, {
                "action": "epd_admin_search_post",
                "post_search": post_search
            }, function(result) {
                console.log(result);
                if(result.posts.length){
                	for (var i = 0; i < result.posts.length; i++) {
                		$(".erphpdown-select-postss").append('<div class="item">'+result.posts[i].title+'（ID：'+result.posts[i].id+'）'+'</div>');
                	}
                }
                
            }, 'json'); 
        }
        return false;
    });
});