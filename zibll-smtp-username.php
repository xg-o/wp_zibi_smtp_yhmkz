<?php
/**
 * Plugin Name:       子比主题 SMTP 用户名扩展
 * Plugin URI:        https://fgwl.qzz.io
 * Description:       为子比主题（Zibll）的 SMTP 发信功能补充 SMTP 用户名参数。适用于 Resend 等要求独立 SMTP 用户名的服务商（Resend 用户名为 resend），无需修改主题源码。
 * Version:           1.0.0
 * Author:            行简丨ima应用copilot模式默认模型
 * Author URI:        https://github.com/xg-o/wp_zibi_smtp_yhmkz
 * Text Domain:       zibll-smtp-username
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // 禁止直接访问
}

define( 'ZSU_OPTION', 'zsu_smtp_username' );

/* ========== 后台设置菜单 ========== */
add_action( 'admin_menu', 'zsu_admin_menu' );
function zsu_admin_menu() {
	add_options_page(
		'SMTP 用户名扩展',
		'SMTP 用户名扩展',
		'manage_options',
		'zsu-settings',
		'zsu_settings_page'
	);
}

/* ========== 注册设置项 ========== */
add_action( 'admin_init', 'zsu_register_settings' );
function zsu_register_settings() {
	register_setting( 'zsu_settings_group', ZSU_OPTION, array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => '',
	) );
}

/* ========== 设置页 ========== */
function zsu_settings_page() {
	?>
	<div class="wrap">
		<h1>子比主题 SMTP 用户名扩展</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'zsu_settings_group' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="zsu_smtp_username">SMTP 用户名</label>
					</th>
					<td>
						<input type="text" id="zsu_smtp_username"
							name="<?php echo esc_attr( ZSU_OPTION ); ?>"
							value="<?php echo esc_attr( get_option( ZSU_OPTION, '' ) ); ?>"
							class="regular-text" autocomplete="off" />
						<p class="description">
							填写 SMTP 认证用户名。使用 Resend 时填 <code>resend</code>；留空则不修改，保持子比主题原有行为。
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button( '保存设置' ); ?>
		</form>

		<hr />
		<h2>使用说明</h2>
		<ol>
			<li>在子比主题的 SMTP 设置中照常填写：服务器 <code>smtp.resend.com</code>、端口 <code>465</code>（SSL）、密码填 Resend API Key（<code>re_</code> 开头）。</li>
			<li>在本页「设置 → SMTP 用户名扩展」中填写 SMTP 用户名 <code>resend</code>，保存。</li>
			<li>网站发邮件时会自动使用此用户名进行 SMTP 认证，无需修改主题源码。</li>
		</ol>
		<p>提示：测试发信请在子比主题的 SMTP 设置页使用「测试邮件」功能，若提示发送成功即配置正确。</p>
	</div>
	<?php
}

/* ========== 核心逻辑：覆盖 SMTP 用户名 ========== */
add_action( 'phpmailer_init', 'zsu_override_smtp_username', 999 );
function zsu_override_smtp_username( $phpmailer ) {
	// 仅当站点确实使用 SMTP 发送时生效（兼容子比主题及其它 SMTP 插件）
	if ( ! is_object( $phpmailer ) || 'smtp' !== $phpmailer->Mailer ) {
		return;
	}

	$username = get_option( ZSU_OPTION, '' );
	if ( '' !== $username ) {
		$phpmailer->Username = $username;
		$phpmailer->SMTPAuth = true;
	}
}
