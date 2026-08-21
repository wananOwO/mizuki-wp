<?php
/*
mobantu.com
qq 82708210
*/
if ( !defined('ABSPATH') ) {exit;}

function erphpdown_metaboxs() {
	$erphp_down_default = get_option('erphp_down_default');
	$member_down_default = get_option('member_down_default');
	$down_price_default = get_option('down_price_default');
	$down_price_type_default = get_option('down_price_type_default');
	$down_days_default = get_option('down_days_default');

	$meta_boxes = array(
		array(
			"name"             => "erphp_down",
			"title"            => "收费模式",
			"desc"             => "可以通过在正文里添加短代码<code>[erphpdown]隐藏内容[/erphpdown]</code>来隐藏部分收费内容",
			"type"             => "downradio",
			"capability"       => "manage_options",
			'default' => $erphp_down_default?$erphp_down_default:'4',
		),
		array(
			"name"             => "member_down",
			"title"            => "VIP优惠",
			"desc"             => "专享指VIP免费、普通用户无法购买，专购指仅VIP可购买；<br>自定义折扣在 <a href='".admin_url()."admin.php?page=erphpdown/admin/erphp-vip-setting.php#erphp-discounts' target='_blank'>Erphpdown-VIP设置</a> 里的 自定义折扣设置 来设置固定的折扣；<br>可通过短代码<code>[vip]隐藏内容[/vip]</code>隐藏VIP专享内容，<code>[vip type=7]隐藏内容[/vip]</code>type的值默认可不填，若填7、8、9、10分别表示包月、包季、包年、终身。",
			"type"             => "vipradio",
			'options' => array(
				'1' => '无',
	            '3' => '免费',
	            '16' => '包季免费',
	            '6' => '包年免费',
	            '7' => '终身免费',
	            '4' => '专享',
	            '15' => '包季专享',
	            '8' => '包年专享',
	            '9' => '终身专享',
	            '2' => '5折',
	            '5' => '8折',
	            '13' => '5折|终身免费',
	            '14' => '8折|终身免费',
	            '20' => '自定义折扣|终身免费',
	            '10' => '专购',
	            '17' => '包季专购',
	            '18' => '包年专购',
	            '19' => '终身专购',
	            '11' => '专购|包年5折',
	            '12' => '专购|包年8折'
	        ),
	        'default' => $member_down_default?$member_down_default:'1',
			"capability"       => "manage_options"
		)
	);

	if(!get_option('erphp_metabox_mini')){
		$meta_boxes[] = array(
			"name"             => "down_price_type",
			"title"            => "价格类型",
			"desc"             => "多价格类型暂不支持免登录、查看模式",
			"type"             => "typeradio",
			'options' => array(
				'0' => '单价格',
	            '1' => '多价格'
	        ),
	        'default' => $down_price_type_default?$down_price_type_default:'0',
			"capability"       => "manage_options"
		);
	}

	$meta_boxes[] = array(
		"name"             => "down_price",
		"title"            => "收费价格",
		"desc"             => "除<b>VIP专享</b>外，其他必须大于0（<b>VIP专享购买</b>也必须大于0），否则视为全网免费资源；免登录模式时单位为元",
		"type"             => "erphpnumber",
		'default'          => $down_price_default?$down_price_default:'0',
		'required'         => '1',
		"capability"       => "manage_options"
	);

	$meta_boxes[] = array(
		"name"             => "down_url",
		"title"            => "下载地址",
		"desc"             => "",
		"type"             => "erphptextarea",
		"capability"       => "manage_options"
	);

	if(!get_option('erphp_metabox_mini')){
		$meta_boxes[] = array(
			"name"             => "down_urls",
			"title"            => "下载地址",
			"desc"             => "",
			"type"             => "erphptextareas",
			"capability"       => "manage_options"
		);

		$meta_boxes[] = array(
			"name"             => "down_url_free",
			"title"            => "免费下载地址",
			"desc"             => "与上面的收费下载地址可同时存在，用户不用登录就能免费下载的地址，不记录下载次数，无任何限制，可被爬虫采集，慎用；格式与上面一致",
			"type"             => "textarea",
			"capability"       => "manage_options"
		);
	}

	$meta_boxes[] = array(
		"name"             => "hidden_content",
		"title"            => "隐藏内容",
		"desc"             => "收费下载模式的隐藏内容，一般填提取码或者解压密码。格式化的格式为<code>提取码：xxxx，解压密码：oooo</code>或<code>提取码：oooo</code>，注意是中文冒号与逗号",
		"type"             => "text",
		"default"          => "",
		"capability"       => "manage_options"
	);

	if(plugin_check_activation()){
		$meta_boxes[] = array(
			"name"             => "down_activation",
			"title"            => "激活码发放",
			"desc"             => "需要配置好smtp发邮件功能，激活码会在用户购买后自动发送到用户邮箱",
			"type"             => "checkbox",
			"capability"       => "manage_options"
		);
	}

	if(!get_option('erphp_metabox_mini')){
		$meta_boxes[] = array(
			"name"             => "down_days",
			"title"            => "过期天数",
			"desc"             => "留空或0则表示一次购买永久下载，设置一个大于0的数字比如30，则表示购买30天后得重新购买",
			"type"             => "number",
			'default'          => $down_days_default?$down_days_default:'0',
			"required"         => "0",
			"capability"       => "manage_options"
		);

		$meta_boxes[] = array(
			"name"             => "down_box_hide",
			"title"            => "隐藏购买框",
			"desc"             => "隐藏默认添加到文章内容底部的购买框，你可以通过短代码<code>[box]</code>在文章任意地方显示购买框，仅适用于下载、免登录模式",
			"type"             => "checkbox",
			"capability"       => "manage_options"
		);
		
		$meta_boxes[] = array(
			"name"             => "down_only_pay",
			"title"            => "仅在线支付",
			"desc"             => "不可使用余额购买，请确保 1.erphpdown-基础设置 开启 直接支付购买，2.有配置可用的在线支付接口",
			"type"             => "checkbox",
			"capability"       => "manage_options"
		);

		$meta_boxes[] = array(
			"name"             => "down_repeat",
			"title"            => "重复购买",
			"desc"             => "仅限登录状态的【下载】模式，可重复购买多次",
			"type"             => "checkbox",
			"capability"       => "manage_options"
		);

		/*$meta_boxes[] = array(
			"name"             => "down_user_one",
			"title"            => "一人买一次",
			"desc"             => "收费模式选【发卡】时可使用，限制每人仅可购买一次，不要与上面的【重复购买】同时勾选",
			"type"             => "checkbox",
			"capability"       => "manage_options"
		);

		$meta_boxes[] = array(
			"name"             => "down_user_ones",
			"title"            => "不限次用户",
			"desc"             => "收费模式选【下载】时可使用，可以不限购买次数的用户名，多个用户名用英文逗号<code>,</code>隔开，例如：admin,test",
			"type"             => "textarea",
			"capability"       => "manage_options"
		);*/
	}

	return $meta_boxes;
}

function erphpdown_show_metabox() {
	global $post;
	$meta_boxes = erphpdown_metaboxs(); 
?>
	<style>
	.erphpdown-metabox-item{padding-left:100px;position:relative;margin:1em 0}
	.erphpdown-metabox-item-pricetype1{display:none}
	.erphpdown-metabox-item label.title{position:absolute;left:0;top:0;display:inline-block;font-weight:bold;width:100px;vertical-align:top}
	.down-urls{position: relative;}
	.down-urls .down-url{margin-bottom: 5px;border: 1px dashed #ccc;padding: 5px;border-radius: 5px;position: relative;cursor: move;}
	.down-urls .down-url input, .down-urls .down-url select, .down-urls .down-url .del-down-url {vertical-align: top;}
	</style>
<?php
	foreach ( $meta_boxes as $meta ) :
		$value = get_post_meta( $post->ID, $meta['name'], true );
		if ( $meta['type'] == 'text' )
			erphpdown_show_text( $meta, $value );
		elseif ( $meta['type'] == 'erphpnumber' )
			erphpdown_show_erphpnumber( $meta, $value );
		elseif ( $meta['type'] == 'number' )
			erphpdown_show_number( $meta, $value );
		elseif ( $meta['type'] == 'textarea' )
			erphpdown_show_textarea( $meta, $value );
		elseif ( $meta['type'] == 'erphptextarea' )
			erphpdown_show_erphptextarea( $meta, $value );
		elseif ( $meta['type'] == 'erphptextareas' )
			erphpdown_show_erphptextareas( $meta, $value );
		elseif ( $meta['type'] == 'checkbox' )
			erphpdown_show_checkbox( $meta, $value );
		elseif ( $meta['type'] == 'radio' )
			erphpdown_show_radio( $meta, $value );
		elseif ( $meta['type'] == 'downradio' )
			erphpdown_show_downradio( $meta, $value );
		elseif ($meta['type'] == 'vipradio')
			erphpdown_show_vipradio( $meta, $value );
		elseif ($meta['type'] == 'typeradio')
			erphpdown_show_typeradio( $meta, $value );
	endforeach;
?>
	<fieldset style="border:1px solid #ccc;padding:5px 8px;border-radius: 4px;display: none;"><legend>短代码使用指南</legend>- 收费隐藏短代码 <code>[erphpdown]部分隐藏内容[/erphpdown]</code>，多价格类型不建议使用短代码；<br>- VIP专属隐藏短代码 <code>[vip type=6]VIP内容[/vip]</code>（type选填，可为6、7、8、9、10，分别对应五种VIP）；<br>- 自定义位置的购买下载框短代码 <code>[box]</code>，仅对下载模式有效。</fieldset>
	<script>
		jQuery(function(){
			if(jQuery("input[name='erphp_down'].nologin").is(":checked")){
				jQuery("input[name='member_down'].login").parent().hide();
			}
			if(jQuery("input[name='erphp_down'].novip").is(":checked")){
				jQuery("input[name='member_down'].vip").parent().hide();
			}
			if(jQuery("input[name='erphp_down'].noprice").is(":checked")){
				jQuery("input[name='down_price_type'].pricetype1").parent().hide();
			}
		});

		jQuery("input[name='erphp_down']").click(function(){
			if(jQuery(this).hasClass("nologin")){
				jQuery("input[name='member_down'].login").parent().hide();
			}else{
				jQuery("input[name='member_down'].login").parent().show();
			}

			if(jQuery(this).hasClass("novip")){
				jQuery("input[name='member_down'].vip").parent().hide();
			}else{
				if(jQuery(this).hasClass("nologin")){
					jQuery("input[name='member_down'].login").parent().hide();
				}else{
					jQuery("input[name='member_down'].vip").parent().show();
				}
			}

			if(jQuery(this).hasClass("noprice")){
				jQuery("input[name='down_price_type'].pricetype1").parent().hide();
			}else{
				jQuery("input[name='down_price_type'].pricetype1").parent().show();
			}
		});

		jQuery(function(){
			if(jQuery("input[name='down_price_type'].pricetype1").is(":checked")){
				jQuery(".erphpdown-metabox-item-pricetype0").hide();
				jQuery(".erphpdown-metabox-item-pricetype1").show();
			}
		});
		jQuery("input[name='down_price_type']").click(function(){
			if(jQuery(this).hasClass("pricetype1")){
				jQuery(".erphpdown-metabox-item-pricetype0").hide();
				jQuery(".erphpdown-metabox-item-pricetype1").show();
			}else{
				jQuery(".erphpdown-metabox-item-pricetype1").hide();
				jQuery(".erphpdown-metabox-item-pricetype0").show();
			}
		});
	</script>
<?php 
}

function erphpdown_show_radio( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<?php
			$i=1;
            foreach ($options as $key => $option) {
            	if(!$value) $value=$default;
                echo '<span><input type="radio" name="'.$name.'" id="'.$name.$i.'" value="'. esc_attr( $key ) . '" '. checked( $value, $key, false) .'/><label for="'.$name.$i.'">' . esc_html( $option ) . '</label>&nbsp;&nbsp;</span>';
                $i ++;
            }
        ?>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_show_typeradio( $args = array(), $value = false ) {
	global $pagenow;
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<?php
			$i=1;
            foreach ($options as $key => $option) {
            	if($pagenow === 'post-new.php') $value=$default;
            	else{if($value == null) $value = 0;}

            	if($key == 1){$class="pricetype1";}else{$class="";}
                echo '<span><input type="radio" name="'.$name.'" id="'.$name.$i.'" value="'. esc_attr( $key ) . '" '. checked( $value, $key, false) .' class="'.$class.'"/><label for="'.$name.$i.'">' . esc_html( $option ) . '</label>&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                $i ++;
            }
        ?>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_show_vipradio( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<?php
			$i=1;
            foreach ($options as $key => $option) {
            	if(!$value) $value=$default;
            	if($key != 1 && $key != 3 && $key != 16 && $key != 6 && $key != 7){$class="login";}else{$class="";}
            	if($key != 1){$class .= " vip";}
                echo '<span><input type="radio" name="'.$name.'" id="'.$name.$i.'" value="'. esc_attr( $key ) . '" '. checked( $value, $key, false) .' class="'.$class.'"/><label for="'.$name.$i.'">' . esc_html( $option ) . '</label>&nbsp;&nbsp;</span>';
                $i ++;
            }
        ?>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_show_text( $args = array(), $value = false ) {
	global $pagenow;
	extract( $args ); if($pagenow == 'post-new.php'){ if(!$value) $value=$default; }?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_html( $value, 1 ); ?>" style="width: 100%;" />
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_show_erphpnumber( $args = array(), $value = false ) {
	extract( $args ); if($value == '') $value=$default; ?>
	<div class="erphpdown-metabox-item erphpdown-metabox-item-pricetype0">
		<label class="title"><?php echo $title; ?></label>
		<input type="number" min="0" step="0.01" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_html( $value, 1 ); ?>" style="width: 100px;" <?php if($required) echo 'required';?>/>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_show_number( $args = array(), $value = false ) {
	extract( $args ); if(!$value) $value=$default; ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<input type="number" min="0" step="1" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_html( $value, 1 ); ?>" style="width: 100px;" <?php if($required) echo 'required';?>/>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_show_textarea( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" cols="60" rows="4" tabindex="30" style="width: 100%;"><?php echo esc_html( $value, 1 ); ?></textarea>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>	
	</div>
	<?php
}

function erphpdown_show_erphptextarea( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item erphpdown-metabox-item-pricetype0">
		<label class="title"><?php echo $title; ?></label>
		<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" cols="60" rows="4" tabindex="30" style="width: 100%;"><?php echo esc_html( $value, 1 ); ?></textarea><a href="javascript:;" class="erphp-add-file button">上传媒体库文件</a> <a href="javascript:;" class="erphp-add-file2 button button-primary">上传本地文件</a> <span id="file-progress"></span>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description">
			收费查看模式不用填写，地址一行一个，可外链以及内链。地址格式可为以下任意一种：<a href="javascript:;" class="erphpshowtypes">点击显示格式</a><br>
			<fieldset class="erphpurltypes" style="display: none;border:1px solid #ccc;padding:5px 8px;border-radius: 4px;"><legend>下载地址格式</legend><ol><li>/wp-content/uploads/moban-tu.zip</li><li>https://pan.baidu.com/test</li><li>某某地址,https://pan.baidu.com/test,提取码：2587</li><li>某某地址,https://pan.baidu.com/test</li><li>链接: https://pan.baidu.com/s/test 提取码: xxxx</li></ol>模板兔提示：1是内链，可加密下载地址；3与4格式用英文半角逗号隔开（名称,下载地址,提取码或解压密码），不能有空格；5是<b>网页版百度网盘</b>默认分享格式（名称 下载地址 提取码名称 提取码），英文空格分割</fieldset>
		</p>	
		<script src="<?php echo ERPHPDOWN_URL;?>/static/jquery.form.js"></script>
		<script>
	        jQuery(function($) {
	            
                $(document).on('click', '.erphp-add-file', function(e) {
                    e.preventDefault();
                    var button = $(this);
                    var id = button.prev();
                    var original_send = wp.media.editor.send.attachment;
                    wp.media.editor.send.attachment = function(props, attachment) {
                        if($.trim(id.val()) != ''){
							id.val(id.val()+'\n'+attachment.url);
						}else{
							id.val(attachment.url);	
						}
						wp.media.editor.send.attachment = original_send; 
                    };
                    wp.media.editor.open(button);
                    return false;
                });
	            

	            $(".erphpshowtypes").click(function(){
	            	if($(this).hasClass('active')){
	            		$(".erphpurltypes").hide();
	            	}else{
	            		$(".erphpurltypes").show();
	            	}
	            	$(this).toggleClass("active");
	            });

	            $(".erphp-add-file2").click(function(){
                    $("body").append('<form style="display:none" id="erphpFileForm" action="<?php echo ERPHPDOWN_URL;?>/admin/action/file.php" enctype="multipart/form-data" method="post"><input type="file" id="erphpFile" name="erphpFile"></form>');
                    $("#erphpFile").trigger('click');
                    $("#erphpFile").change(function(){
                        $("#erphpFileForm").ajaxSubmit({
                            dataType:  'json',
                            beforeSend: function() {
                                
                            },
                            uploadProgress: function(event, position, total, percentComplete) {
                                $('#file-progress').text(percentComplete+'%');
                            },
                            success: function(data) {
                                $('#erphpFileForm').remove();
                                var olddata = $('#<?php echo $name;?>').val();
                                if($.trim(olddata)){
                                	$('#<?php echo $name;?>').val(olddata+'\n'+data.link);   
                                }else{
                                    $('#<?php echo $name;?>').val(data.link);   
                                }
                            },
                            error:function(xhr){
                                $('#erphpFileForm').remove();
                                alert('上传失败！'); 
                            }
                        });

                    });
                    return false;
                });
	            
	        });
	    </script>	
	</div>
	<?php
}

function erphpdown_show_erphptextareas( $args = array(), $value = false ) {
	extract( $args ); 
	?>
	<div class="erphpdown-metabox-item erphpdown-metabox-item-pricetype1">
		<label class="title"><?php echo $title; ?></label>
		<div class="down-urls erphpdown-urls">
			<?php 
				if($value){
					$cnt = count($value['index']);
            		if($cnt){
            			for($i=0; $i<$cnt;$i++){
            				echo '<div class="down-url">
								<input type="number" min="1" step="1" name="down_urls[index][]" value="'.$value['index'][$i].'" placeholder="序号" style="width:8%" required><input type="text" name="down_urls[name][]" value="'.$value['name'][$i].'" placeholder="名称" style="width: 15%" required><input type="number" step="0.01" name="down_urls[price][]" value="'.$value['price'][$i].'" placeholder="价格" style="width: 10%"><div style="display: inline-block;width: 50%;"><textarea name="down_urls[url][]" placeholder="地址" style="width: 100%" rows="2">'.$value['url'][$i].'</textarea><a href="javascript:;" class="erphp-add-url button">上传媒体库文件</a> <a href="javascript:;" class="erphp-add-url2 button button-primary">上传本地文件</a> <span></span></div><select name="down_urls[vip][]" style="display:inline-block;"><option value="0">VIP默认优惠</option><option value="1" '.($value['vip'][$i] == '1'?'selected':'').'>无</option><option value="3" '.($value['vip'][$i] == '3'?'selected':'').'>VIP免费</option><option value="16" '.($value['vip'][$i] == '16'?'selected':'').'>包季VIP免费</option><option value="6" '.($value['vip'][$i] == '6'?'selected':'').'>年费VIP免费</option><option value="7" '.($value['vip'][$i] == '7'?'selected':'').'>终身VIP免费</option><option value="4" '.($value['vip'][$i] == '4'?'selected':'').'>VIP专享</option><option value="15" '.($value['vip'][$i] == '15'?'selected':'').'>包季VIP专享</option><option value="8" '.($value['vip'][$i] == '8'?'selected':'').'>年费VIP专享</option><option value="9" '.($value['vip'][$i] == '9'?'selected':'').'>终身VIP专享</option><option value="2" '.($value['vip'][$i] == '2'?'selected':'').'>VIP 5折</option><option value="5" '.($value['vip'][$i] == '5'?'selected':'').'>VIP 8折</option><option value="13" '.($value['vip'][$i] == '13'?'selected':'').'>VIP 5折|终身免费</option><option value="14" '.($value['vip'][$i] == '14'?'selected':'').'>VIP 8折|终身免费</option><option value="20" '.($value['vip'][$i] == '20'?'selected':'').'>自定义折扣|终身免费</option><option value="10" '.($value['vip'][$i] == '10'?'selected':'').'>VIP专购</option><option value="17" '.($value['vip'][$i] == '17'?'selected':'').'>包季VIP专购</option><option value="18" '.($value['vip'][$i] == '18'?'selected':'').'>包年VIP专购</option><option value="19" '.($value['vip'][$i] == '19'?'selected':'').'>终身VIP专购</option><option value="11" '.($value['vip'][$i] == '11'?'selected':'').'>专购|年费5折</option><option value="12" '.($value['vip'][$i] == '12'?'selected':'').'>专购|年费8折</option></select> <a href="javascript:;" class="del-down-url">删</a>
							</div>';
            			}
            		}
				}
			?>
		</div>

		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<p class="description">
			<button class="button add-down-url" type="button">+ 添加地址</button> 序号确保每个唯一，地址一行一个，可外链以及内链。地址格式可为以下任意一种：<a href="javascript:;" class="erphpshowtypes2">点击显示格式</a><br>
			<fieldset class="erphpurltypes2" style="display: none;border:1px solid #ccc;padding:5px 8px;border-radius: 4px;"><legend>下载地址格式</legend><ol><li>/wp-content/uploads/moban-tu.zip</li><li>https://pan.baidu.com/test</li><li>某某地址,https://pan.baidu.com/test,提取码：2587</li><li>某某地址,https://pan.baidu.com/test</li><li>链接: https://pan.baidu.com/s/test 提取码: xxxx</li></ol>模板兔提示：1是内链，可加密下载地址；3与4格式用英文半角逗号隔开（名称,下载地址,提取码或解压密码），不能有空格；5是<b>网页版百度网盘</b>默认分享格式（名称 下载地址 提取码名称 提取码），英文空格分割</fieldset>
		</p>	
		<script src="<?php echo ERPHPDOWN_URL;?>/static/jquery.form.js"></script>
		<script>
	        jQuery(function($) {
	        	$( ".erphpdown-urls" ).sortable({ disabled: false });
	        	$(".add-down-url").click(function(){
		            $(".down-urls").append('<div class="down-url"><input type="number" min="1" step="1" name="down_urls[index][]" placeholder="序号" style="width:8%" required><input type="text" name="down_urls[name][]" placeholder="名称" style="width: 15%" required><input type="number" value="<?php echo get_option('down_prices_default')?get_option('down_prices_default'):'';?>" step="0.01" name="down_urls[price][]" placeholder="价格" style="width: 10%"><div style="display: inline-block;width: 50%;"><textarea name="down_urls[url][]" placeholder="地址" style="width: 100%" rows="2"></textarea><a href="javascript:;" class="erphp-add-url button">上传媒体库文件</a> <a href="javascript:;" class="erphp-add-url2 button button-primary">上传本地文件</a> <span></span></div><select name="down_urls[vip][]" style="display:inline-block;"><option value="0">VIP默认优惠</option><option value="1">无</option><option value="3">VIP免费</option><option value="16">包季VIP免费</option><option value="6">年费VIP免费</option><option value="7">终身VIP免费</option><option value="4">VIP专享</option><option value="15">包季VIP专享</option><option value="8">年费VIP专享</option><option value="9">终身VIP专享</option><option value="2">VIP 5折</option><option value="5">VIP 8折</option><option value="13">VIP 5折|终身免费</option><option value="14">VIP 8折|终身免费</option><option value="20">自定义折扣|终身免费</option><option value="10">VIP专购</option><option value="17">包季VIP专购</option><option value="18">包年VIP专购</option><option value="19">终身VIP专购</option><option value="11">专购|年费5折</option><option value="12">专购|年费8折</option></select> <a href="javascript:;" class="del-down-url">删</a></div>');
		            return false;
		        });

		        $(document).on("click",".del-down-url",function(){
		            $(this).parent().remove();
		        });
	            
                $(document).on('click', '.erphp-add-url', function(e) {
                    e.preventDefault();
                    var button = $(this);
                    var id = button.prev();
                    var original_send = wp.media.editor.send.attachment;
                    wp.media.editor.send.attachment = function(props, attachment) {
                        if($.trim(id.val()) != ''){
							id.val(id.val()+'\n'+attachment.url);
						}else{
							id.val(attachment.url);	
						}
						wp.media.editor.send.attachment = original_send; 
                    };
                    wp.media.editor.open(button);
                    return false;
                });

	            $(document).on("click", ".erphp-add-url2", function(){
	            	var button = $(this);
                    var id = button.prev().prev();
                    $("body").append('<form style="display:none" id="erphpFileForm" action="<?php echo ERPHPDOWN_URL;?>/admin/action/file.php" enctype="multipart/form-data" method="post"><input type="file" id="erphpFile" name="erphpFile"></form>');
                    $("#erphpFile").trigger('click');
                    $("#erphpFile").change(function(){
                        $("#erphpFileForm").ajaxSubmit({
                        	dataType:  'json',
                            uploadProgress: function(event, position, total, percentComplete) {
                                button.next().text(percentComplete+'%');
                            },
                            success: function(data) {
                                $('#erphpFileForm').remove();
                                if($.trim(id.val()) != ''){
                                	id.val(id.val()+'\n'+data.link);   
                                }else{
                                    id.val(data.link);   
                                }
                            },
                            error:function(xhr){
                                $('#erphpFileForm').remove();
                                alert('上传失败！'); 
                            }
                        });

                    });
                    return false;
                });

                $(".erphpshowtypes2").click(function(){
	            	if($(this).hasClass('active')){
	            		$(".erphpurltypes2").hide();
	            	}else{
	            		$(".erphpurltypes2").show();
	            	}
	            	$(this).toggleClass("active");
	            });
	            
	        });
	    </script>	
	</div>
	<?php
}

function erphpdown_show_checkbox( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="1"
		<?php if ( htmlentities( $value, 1 ) == '1' ) echo ' checked="checked"'; ?>
		style="width: auto;" />
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<p class="description"><?php echo $desc; ?></p>
	</div>
<?php }

function erphpdown_show_downradio( $args = array(), $value = false ) {
	extract( $args ); 
	global $post, $pagenow;
	$value1 = get_post_meta( $post->ID, 'start_down', true );
	$value2 = get_post_meta( $post->ID, 'start_see', true );
	$value3 = get_post_meta( $post->ID, 'start_see2', true );
	$value5 = get_post_meta( $post->ID, 'start_down2', true );
	?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>

		<?php if($pagenow === 'post-new.php'){?>
		<input type="radio" name="erphp_down" id="erphp_down4" <?php if($default == '4') echo 'checked'?> value="4" /><label for="erphp_down4">不启用</label> &nbsp;
		<input type="radio" name="erphp_down" id="erphp_down1" <?php if($default == '1') echo 'checked'?> value="1" /><label for="erphp_down1">下载</label> &nbsp;
		<input type="radio" name="erphp_down" id="erphp_down2" <?php if($default == '2') echo 'checked'?> value="2" class="noprice"/><label for="erphp_down2">查看</label> &nbsp;
		<input type="radio" name="erphp_down" id="erphp_down3" <?php if($default == '3') echo 'checked'?> value="3" class="noprice"/><label for="erphp_down3">部分查看</label> &nbsp;
		<?php if(!get_option('erphp_wppay_close')){?>
		<input type="radio" name="erphp_down" id="erphp_down5" <?php if($default == '5') echo 'checked'?> value="5" class="nologin noprice"/><label for="erphp_down5">免登录（旧）</label> &nbsp;
		<?php }?>
		<input type="radio" name="erphp_down" id="erphp_down6" <?php if($default == '6') echo 'checked'?> value="6" class="noprice novip"/><label for="erphp_down6">发卡</label> &nbsp;
		<?php }else{?>
		<input type="radio" name="erphp_down" id="erphp_down4" checked value="4" /><label for="erphp_down4">不启用</label> &nbsp;
		<input type="radio" name="erphp_down" id="erphp_down1" <?php if($value1 == 'yes') echo 'checked'?> value="1" /><label for="erphp_down1">下载</label> &nbsp;
		<input type="radio" name="erphp_down" id="erphp_down2" <?php if($value2 == 'yes') echo 'checked'?> value="2" class="noprice"/><label for="erphp_down2">查看</label> &nbsp;
		<input type="radio" name="erphp_down" id="erphp_down3" <?php if($value3 == 'yes') echo 'checked'?> value="3" class="noprice"/><label for="erphp_down3">部分查看</label> &nbsp;
		<?php if(!get_option('erphp_wppay_close')){?>
		<input type="radio" name="erphp_down" id="erphp_down5" <?php if($value5 == 'yes') echo 'checked'?> value="5" class="nologin noprice"/><label for="erphp_down5">免登录（旧）</label> &nbsp;
		<?php }?>
		<input type="radio" name="erphp_down" id="erphp_down6" <?php if($value == '6') echo 'checked'?> value="6" class="noprice novip"/><label for="erphp_down6">发卡</label> &nbsp;
		<?php }?>

		<input type="hidden" name="erphpdown" value="1">
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<p class="description"><?php echo $desc; ?></p>
		
	</div>
<?php }


add_action( 'admin_menu', 'erphpdown_create_metabox' );
add_action( 'save_post', 'erphpdown_save_metabox' );

function erphpdown_create_metabox() {
	$erphp_post_types = get_option('erphp_post_types');
	$args = array(
		'public'   => true,
	);
	$post_types = get_post_types($args);
	foreach ( $post_types  as $post_type ) {
		if($erphp_post_types){
			if(in_array($post_type,$erphp_post_types)) add_meta_box( 'erphpdown-postmeta-box','Erphpdown属性', 'erphpdown_show_metabox', $post_type, 'normal', 'high' );
		}
	}
	
}

function erphpdown_save_metabox( $post_id ) {

	if(!isset($_POST['erphpdown']))
		return;

	$meta_boxes = array_merge( erphpdown_metaboxs() );
	foreach ( $meta_boxes as $meta_box ) :
		if($meta_box['type'] == 'downradio'){

			if (!wp_verify_nonce( $_POST[$meta_box['name'] . '_input_name'], plugin_basename( __FILE__ ) ))
				return $post_id;

			if(isset($_POST['erphp_down'])){
				$data = stripslashes( $_POST['erphp_down'] );
				$data1 = '';$data2='';$data3='';$data5='';
				if($data == '1'){
					$data1 = 'yes';
					update_post_meta( $post_id, 'start_down', 'yes' );
					delete_post_meta( $post_id, 'start_down2');
					delete_post_meta( $post_id, 'start_see');
					delete_post_meta( $post_id, 'start_see2');
				}elseif($data == '2'){
					$data2 = 'yes';
					update_post_meta( $post_id, 'start_see', 'yes' );
					delete_post_meta( $post_id, 'start_down2');
					delete_post_meta( $post_id, 'start_down');
					delete_post_meta( $post_id, 'start_see2');
				}elseif($data == '3'){
					$data2 = 'yes';
					update_post_meta( $post_id, 'start_see2', 'yes' );
					delete_post_meta( $post_id, 'start_down2');
					delete_post_meta( $post_id, 'start_down');
					delete_post_meta( $post_id, 'start_see');
				}elseif($data == '5'){
					$data2 = 'yes';
					update_post_meta( $post_id, 'start_down2', 'yes' );
					delete_post_meta( $post_id, 'start_see');
					delete_post_meta( $post_id, 'start_down');
					delete_post_meta( $post_id, 'start_see2');
				}else{
					delete_post_meta( $post_id, 'start_down');
					delete_post_meta( $post_id, 'start_down2');
					delete_post_meta( $post_id, 'start_see');
					delete_post_meta( $post_id, 'start_see2');
				}
				update_post_meta( $post_id, $meta_box['name'], $data );
			}
		}else{
			if (!wp_verify_nonce( $_POST[$meta_box['name'] . '_input_name'], plugin_basename( __FILE__ ) ))
				return $post_id;

			if(isset($_POST[$meta_box['name']])){
	            update_post_meta($post_id, $meta_box['name'], $_POST[$meta_box['name']]);
	        }else{
	            delete_post_meta($post_id, $meta_box['name']);
	        }
		}


	endforeach;
}