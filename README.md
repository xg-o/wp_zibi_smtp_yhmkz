# wp_zibi_smtp_yhmkz
用于wp论坛的子比主题smtp扩展。给子比主题的smtp服务增加了用户名参数。安装完插件后在设置 → SMTP 用户名扩展填写用户名即可在发邮件时增加用户名参数。你可以使用resend等等邮件服务了。
# 子比主题 SMTP 用户名扩展
为子比主题（Zibll）的 SMTP 发信功能补充 SMTP 用户名参数。
## 为什么需要
- 子比主题的 SMTP 配置只有：服务器、端口、加密、发件邮箱、密码，**没有 SMTP 用户名字段**
- Resend 的 SMTP 认证要求用户名固定为 `resend`，密码为 API Key（`re_` 开头）
- 子比主题默认会把发件邮箱当作 SMTP 用户名，导致 Resend 认证失败
## 安装
1. 将 `zibll-smtp-username.php` 上传到 `wp-content/plugins/zibll-smtp-username/`（或直接上传本 zip 压缩包）
2. 后台「插件」中启用「子比主题 SMTP 用户名扩展」
3. 进入「设置 → SMTP 用户名扩展」，填写 SMTP 用户名 `resend`，保存
## 使用
- 子比主题 SMTP 设置：服务器 `smtp.resend.com`、端口 `465`（SSL）、密码填 Resend API Key
- 插件内填写用户名 `resend`
- 站点发邮件时自动使用该用户名认证
## 原理
插件挂载 WordPress 核心钩子 `phpmailer_init`（优先级 999，晚于主题执行），检测到站点使用 SMTP 发送时覆盖 `$phpmailer->Username` 并强制开启 SMTP 认证。纯钩子实现，不修改、不依赖子比主题源码，卸载插件即完全还原。
## 版本
v1.0.0 (2026-08-14)
