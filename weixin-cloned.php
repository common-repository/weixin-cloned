<?php
/*
Plugin Name: 微信分身
Author: 水脉烟香
Author URI: https://wptao.com/smyx
Plugin URI: https://wptao.com/weixin-cloned.html
Description: 为防止微信封杀网站主域名A，在微信APP打开时自动跳到B域名，调用A域名的内容，在微信公众号自动返回B域名，在微信群，请自己用B域名分享！B域名被封了，改成C域名，以此类推！在非微信APP打开时，B域名自动跳回A域名。<strong>建议在建站初期就使用该功能，避免主域名被封。</strong>
Version: 1.0
*/

add_action('admin_menu', 'weixin_cloned_add_page');
function weixin_cloned_add_page() {
    if (function_exists('add_options_page')) {
        add_options_page('微信分身', '微信分身', 'manage_options', 'weixin-cloned', 'weixin_cloned_do_page');
    }
} 
add_action('plugin_action_links_' . plugin_basename(__FILE__), 'weixin_cloned_plugin_actions');
function weixin_cloned_plugin_actions($links) {
    $new_links = array();
    $new_links[] = '<a href="options-general.php?page=weixin-cloned">' . __('Settings') . '</a>';
    return array_merge($new_links, $links);
}
// 设置 Setting
function weixin_cloned_do_page() {
	if (isset($_POST['update_options'])) {
		update_option("weixin_cloned", $_POST['options']);
	}
	$options = get_option('weixin_cloned');
	echo '<div class="error"><p><strong>本插件为付费插件，此处仅作为后台展示，不能使用功能，如果您有需求，请【<a href="https://wptao.com/weixin-cloned.html" target="_blank">点击这里</a>】购买插件，买后卸载本插件，<a href="https://wptao.com/download" target="_blank">重新下载</a>安装后使用。</strong></p></div>';
?>
<div class="wrap">
  <h2>微信分身 <code><a target="_blank" href="https://wptao.com/weixin-cloned.html">官网</a></code></h2>
  <form method="post" action="">
	<?php wp_nonce_field('update-options');?>
	<h3>基本设置</h3>
	<table class="form-table">
		<tr>
          <td width="200" valign="top"><strong>功能描述<strong></td>
		  <td>为防止微信封杀网站域名A，在微信APP打开时自动跳到B域名，调用A域名的内容，在微信公众号自动返回B域名，在微信群，请自己用B域名分享！B域名被封了，改成C域名，以此类推！在非微信APP打开时，B域名自动跳到A域名。</td>
		</tr>
		<tr>
          <td valign="top"><label for="wx_domain">微信中使用的新域名</label></td>
		  <td><input type="text" id="wx_domain" name="options[wx_domain]" size="40" value="<?php echo $options['wx_domain'];?>" />
		  <br />格式: <code>www.xx.com</code>，不要加其他多余字符, 如果不使用请留空。保存后在PC端可以<?php echo ($options['wx_domain']) ? '<a target="_blank" href="'.home_url('?signature=1').'">预览效果</a>' : '预览效果'?>。<code>域名请先解析到当前网站目录下。</code></td>
		</tr>
		<tr>
          <td valign="top">新域名使用https</td>
		  <td><label><input name="options[https]" type="checkbox" value="1"<?php checked($options['https']);?>>开启</label>
		  <br />请确保微信中使用的新域名已经安装了SSL证书</td>
		</tr>
<?php
if (defined('WP_CACHE') && WP_CACHE) {
	$wx_dir = ltrim(set_url_scheme(plugins_url('weixin-cloned'), 'relative'), '/') . '/wx-cache.php';
	if (defined('WX_CACHE')) {
		if (WX_CACHE != ABSPATH . $wx_dir) {
			$wx_error = '您添加的代码不对，请删除之前的配置代码，并重新添加!';
		} elseif (is_main_site()) {
			$wx_error = '恭喜，已添加成功！';
		} 
	} else {
		$wx_error = '检测到您还没有添加配置代码。';
	} 
	if ($wx_error) { ?>
		<tr>
          <td valign="top">配置代码</td>
		  <td><p>检测到您用了缓存插件，将以下内容加入到<code><?php echo trailingslashit( str_replace( '\\', '/', ABSPATH ) );?></code>的<code>wp-config.php</code>文件，</p>
		  <p>加在<code>require_once(ABSPATH . 'wp-settings.php');</code>这行上方：</p>
		  <p style="color:red"><?php echo $wx_error;?></p>
		  <p><textarea class="code" readonly="readonly" cols="100" rows="7">
<?php if ($options['wx_domain']) {?>
if (defined('WP_CACHE') && WP_CACHE) {
	define('WX_CACHE_DIR', ABSPATH . 'wp-content');
	define('WX_CACHE', WX_CACHE_DIR . '<?php echo str_replace('wp-content/', '/', $wx_dir);?>');
<?php if (!is_multisite()) {?>
	$wx_domains = array('<?php echo parse_url(home_url(), PHP_URL_HOST);?>' => '<?php echo $options['wx_domain'];?>');
<?php }?>
	@include_once(WX_CACHE);
} 
<?php } else {
	echo '★☆★☆★请先填写【微信中使用的域名】,并保持【主题设置】★☆★☆★';
}?></textarea></p></td>
		</tr>
<?php }} ?>
	</table>
	<p class="submit">
	  <input type="submit" name="update_options" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
  </form>
</div>
<?php
} 