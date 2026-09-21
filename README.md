# goletter/hyperf-mail

基于 [Symfony Mailer](https://symfony.com/doc/current/mailer.html) 的 Hyperf 邮件组件，API 风格接近 Laravel Mailable。

支持 SMTP、Gmail、Mailgun、Postmark、AWS SES、Mandrill、阿里云 DM、Resend、`sendmail`、`log` 等驱动。

## 运行环境

- PHP >= 8.0
- Hyperf >= 3.1
- Swoole >= 4.5

## 安装

```bash
composer require goletter/hyperf-mail
```

发布配置：

```bash
php bin/hyperf.php vendor:publish goletter/hyperf-mail
```

配置写入 `config/autoload/mail.php`。改完 `.env` 后需重启 Hyperf 进程。

API 类驱动（Mailgun、Postmark 等）通常还需：

```bash
composer require hyperf/guzzle
```

## 快速开始

```env
MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Example"

MAIL_SMTP_HOST=smtp.example.com
MAIL_SMTP_PORT=587
MAIL_SMTP_ENCRYPTION=tls
MAIL_SMTP_USERNAME=you@example.com
MAIL_SMTP_PASSWORD=your-password
```

```bash
php bin/hyperf.php gen:mail OrderShipped
```

```php
use App\Mail\OrderShipped;
use Goletter\Mail\Mail;

Mail::to($user)->send(new OrderShipped($order));
```

---

## 驱动配置

默认驱动由 `MAIL_MAILER` / `mail.default` 决定。多数云服务使用 **DSN**；SMTP 可用离散配置或 DSN。

### SMTP（默认）

无需额外 Composer 依赖。

**离散配置（推荐）**

```env
MAIL_MAILER=smtp
MAIL_SMTP_HOST=smtp.hostinger.com
MAIL_SMTP_PORT=587
MAIL_SMTP_ENCRYPTION=tls
MAIL_SMTP_USERNAME=you@example.com
MAIL_SMTP_PASSWORD=your-password
```

| `MAIL_SMTP_ENCRYPTION` | 含义 | 常用端口 |
| --- | --- | --- |
| `tls` | STARTTLS（推荐） | `587` |
| `ssl` | 隐式 SSL/TLS | `465` |
| 留空 / `null` | 不强制加密 | `25` |

对应 `mail.mailers.smtp`：

```php
'smtp' => [
    'host' => env('MAIL_SMTP_HOST'),
    'port' => (int) env('MAIL_SMTP_PORT', 587),
    'encryption' => env('MAIL_SMTP_ENCRYPTION', 'tls'), // tls | ssl | null
    'username' => env('MAIL_SMTP_USERNAME'),
    'password' => env('MAIL_SMTP_PASSWORD'),
    'dsn' => env('MAIL_SMTP_DSN'), // 可选，设置后优先生效
],
```

**完整 DSN（可选）**

设置 `MAIL_SMTP_DSN` 后忽略 host/port 等离散项：

```env
MAIL_SMTP_DSN=smtp://you%40example.com:password@smtp.hostinger.com:587
# 或 ssl/465
MAIL_SMTP_DSN=smtps://you%40example.com:password@smtp.hostinger.com:465
```

> 用户名、密码中的 `@`、`:`、`/` 等请先 URL 编码（`@` → `%40`）。

### Gmail

```bash
composer require symfony/google-mailer
```

```env
MAIL_MAILER=gmail
MAIL_GMAIL_DSN=gmail+smtp://you%40gmail.com:your-app-password@default
```

需开启[两步验证](https://myaccount.google.com/security)，并使用[应用专用密码](https://myaccount.google.com/apppasswords)。Symfony 建议仅用于开发/测试。

### Mailgun

```bash
composer require symfony/mailgun-mailer hyperf/guzzle
```

```env
MAIL_MAILER=mailgun
MAIL_MAILGUN_DSN=mailgun+api://KEY:DOMAIN@default
# 或 mailgun+https://KEY:DOMAIN@default
# 或 mailgun+smtp://USERNAME:PASSWORD@default
```

### Postmark

```bash
composer require symfony/postmark-mailer hyperf/guzzle
```

```env
MAIL_MAILER=postmark
MAIL_POSTMARK_DSN=postmark+api://KEY@default
# 或 postmark+smtp://ID@default
```

### AWS SES

```bash
composer require symfony/amazon-mailer
```

```env
MAIL_MAILER=aws_ses
MAIL_AWS_SES_DSN=ses+api://ACCESS_KEY:SECRET_KEY@default
# 或 ses+https://ACCESS_KEY:SECRET_KEY@default
# 或 ses+smtp://USERNAME:PASSWORD@default
```

### Mandrill

```env
MAIL_MAILER=mandrill
MAIL_MANDRILL_DSN=mandrill+api://KEY@default
```

### 阿里云 DM

```bash
composer require alibabacloud/dm:^1.8
```

```env
MAIL_MAILER=aliyun_dm
MAIL_ALIYUN_DM_ACCESS_KEY_ID=
MAIL_ALIYUN_DM_ACCESS_SECRET=
MAIL_ALIYUN_DM_REGION_ID=
MAIL_ALIYUN_DM_CLICK_TRACE=0
```

```php
'aliyun_dm' => [
    'transport' => \Goletter\Mail\Transport\AliyunDmTransport::class,
    'options' => [
        'access_key_id' => env('MAIL_ALIYUN_DM_ACCESS_KEY_ID'),
        'access_secret' => env('MAIL_ALIYUN_DM_ACCESS_SECRET'),
        'region_id' => env('MAIL_ALIYUN_DM_REGION_ID'),
        'click_trace' => env('MAIL_ALIYUN_DM_CLICK_TRACE', '0'),
    ],
],
```

> 仅支持事务类邮件，不支持批量。

### Resend

```bash
composer require resend/resend-php:^0.22.0
```

```env
MAIL_MAILER=resend
MAIL_RESEND_ACCESS_KEY_ID=
```

```php
'resend' => [
    'transport' => \Goletter\Mail\Transport\ResendTransport::class,
    'options' => [
        'access_key_id' => env('MAIL_RESEND_ACCESS_KEY_ID'),
    ],
],
```

### sendmail / log

```php
'sendmail' => [
    'dsn' => 'sendmail://default',
],

'log' => [
    'transport' => \Goletter\Mail\Transport\LogTransport::class,
    'options' => [
        'name' => 'mail.local',
        'group' => 'default',
    ],
],
```

`log` 驱动不真正发信，只写入日志，适合本地开发。

### 全局发件人 / 统一收件人

```php
'from' => [
    'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
    'name' => env('MAIL_FROM_NAME', 'Example'),
],

// 可选：全局 reply-to
'reply_to' => [
    'address' => 'reply@example.com',
    'name' => 'Support',
],

// 可选：本地调试时，所有邮件改发到同一地址
'to' => [
    'address' => 'dev@example.com',
    'name' => 'Dev',
],
```

---

## 编写 Mailable

```bash
php bin/hyperf.php gen:mail OrderShipped
```

类默认生成在 `app/Mail`。在 `build()` 中配置主题、正文、发件人与附件。

### 完整示例

```php
<?php

declare(strict_types=1);

namespace App\Mail;

use Goletter\Mail\Mailable;

class OrderShipped extends Mailable
{
    public function __construct(
        public string $userName,
    ) {
    }

    public function build(): void
    {
        $this->subject('订单已发货')
            // HTML 与纯文本可同时设置，会组成 multipart 邮件
            ->htmlBody(sprintf(
                '<h1>Hello, %s</h1><p>您的订单已发货。</p>',
                htmlspecialchars($this->userName, ENT_QUOTES, 'UTF-8')
            ))
            ->textBody(sprintf('Hello, %s — 您的订单已发货。', $this->userName));
    }
}
```

发送：

```php
use App\Mail\OrderShipped;
use Goletter\Mail\Mail;

Mail::to('user@example.com')->send(new OrderShipped('Yong'));
```

### 发件人

```php
public function build(): void
{
    $this->from('example@example.com', 'App Name')
        ->subject('订单已发货');
}
```

未调用 `from()` 时使用 `mail.from` 全局配置。

### 正文内容

可用 **视图模板** 或 **直接写字符串**。HTML 与纯文本可只设其一，也可同时设置（推荐同时设置，兼容不支持 HTML 的客户端）。

**直接写正文**

```php
public function build(): void
{
    $this->subject('通知')
        ->htmlBody('<p>Hello</p>')
        ->textBody('Hello'); // 可选；与 htmlBody 并存时为 multipart，不是附件
}
```

**视图模板**

依赖 [`hyperf/view`](https://hyperf.wiki/3.1/#/zh-cn/view)（本包已引入）。以 Blade 为例：

```php
public function build(): void
{
    $this->subject('订单已发货')
        ->htmlView('emails.orders.shipped')
        ->textView('emails.orders.shipped_plain'); // 可选
}
```

**视图数据**

- `public` 属性会自动注入视图
- 或用 `with([...])` 手动传参（配合 `protected` / `private` 属性）

```php
public function __construct(public Order $order) {}

public function build(): void
{
    $this->htmlView('emails.orders.shipped')
        ->with([
            'orderName' => $this->order->name,
            'orderPrice' => $this->order->price,
        ]);
}
```

```blade
<div>Price: {{ $order->price }}</div>
```

### 指定 Mailer

未指定时使用 `mail.default`（即 `MAIL_MAILER`）。可用两种方式覆盖：

```php
// 发送时指定
Mail::mailer('postmark')->to($user)->send(new OrderShipped($order));

// 或在 Mailable 内指定
public function build(): void
{
    $this->mailer('smtp')
        ->subject('订单已发货')
        ->htmlView('emails.orders.shipped');
}
```

### 附件

```php
$this->htmlView('emails.orders.shipped')
    ->attach('/path/to/file', ['as' => 'name.pdf', 'mime' => 'application/pdf'])
    ->attachData($pdfBytes, 'name.pdf', ['mime' => 'application/pdf'])
    ->attachFromDefaultStorage('/path/to/file', 'name.pdf', ['mime' => 'application/pdf'])
    ->attachFromStorage('s3', '/path/to/file', 'name.zip', ['mime' => 'application/zip']);
```

文件系统附件依赖 [`hyperf/filesystem`](https://hyperf.wiki/3.1/#/zh-cn/filesystem)（本包已引入，需发布并配置）。

### 内联图片

模板中可直接使用 `$message`：

```blade
<img src="{{ $message->embed($pathToImage) }}">
<img src="{{ $message->embedData($data, $name) }}">
```

纯文本模板不要使用 `$message` 内联附件。

### 自定义 Symfony Email

```php
public function build(): void
{
    $this->htmlView('emails.orders.shipped');

    $this->withEmail(function (\Symfony\Component\Mime\Email $message) {
        $message->getHeaders()->addTextHeader('X-Custom', 'value');
    });
}
```

---

## 发送邮件

```php
use Goletter\Mail\Mail;

Mail::to($request->user())->send(new OrderShipped($order));

Mail::to($user)
    ->cc($moreUsers)
    ->bcc($evenMoreUsers)
    ->send(new OrderShipped($order));

// 指定 mailer（见上方「指定 Mailer」）
Mail::mailer('postmark')
    ->to($user)
    ->send(new OrderShipped($order));
```

`to` / `cc` / `bcc` 支持：邮箱字符串、字符串数组、`Goletter\Contract\HasMailAddress` 实例或其集合。

遍历多个收件人时，请为每人新建 Mailable（`to()` 会累加收件人）：

```php
foreach (['a@example.com', 'b@example.com'] as $recipient) {
    Mail::to($recipient)->send(new OrderShipped($order));
}
```

### 队列

依赖 [`hyperf/async-queue`](https://hyperf.wiki/3.1/#/zh-cn/async-queue)，需先配置队列。

```php
Mail::to($user)->queue(new OrderShipped($order));
Mail::to($user)->queue(new OrderShipped($order), 'emails'); // 指定队列

Mail::to($user)->later(new OrderShipped($order), 300); // 延迟秒数，单位与驱动一致
Mail::to($user)->later(new OrderShipped($order), 300, 'emails');
```

实现 `HyperfExt\Contract\ShouldQueue` 后，即使调用 `send()` 也会入队：

```php
use HyperfExt\Contract\ShouldQueue;
use Goletter\Mail\Mailable;

class OrderShipped extends Mailable implements ShouldQueue
{
    public string $queue = 'default';
}
```

### 渲染 / 预览

```php
$html = Mail::render(new InvoicePaid($invoice));
// 或
$html = (new InvoicePaid($invoice))->render();
```

路由或控制器中直接 `return new InvoicePaid($invoice);` 可在浏览器预览。

### 本地化

依赖 [`hyperf/translation`](https://hyperf.wiki/3.1/#/zh-cn/translation)。

```php
Mail::to($user)->locale('es')->send(new OrderShipped($order));
```

模型实现 `Goletter\Contract\HasLocalePreference` 并返回 `getPreferredLocale()` 后，向该模型发信会自动使用其语言，无需再调 `locale()`。

---

## 本地开发

1. **`log` 驱动**：`MAIL_MAILER=log`，邮件写入日志
2. **统一收件人**：配置 `mail.to`，所有外发改到同一地址
3. **[Mailtrap](https://mailtrap.io) 等**：用真实 SMTP 发到测试邮箱

---

## 事件

| 事件 | 时机 |
| --- | --- |
| `Goletter\Mail\Events\MailMessageSending` | 真正发送前 |
| `Goletter\Mail\Events\MailMessageSent` | 发送完成后 |

队列入队时不会触发；仅在实际发送时触发。

---

## 常见问题

- 改 `.env` 后必须重启进程
- 若曾发布过旧版 `mail.php`，请对照 `publish/mail.php` 补全字段，或重新 `vendor:publish`
- SMTP 用户名一般是完整邮箱，以服务商文档为准
- 包名与发布命令均为 `goletter/hyperf-mail`（不是 `goletter/mail`）
- `htmlBody()` + `textBody()`（或 `htmlView` + `textView`）会组成 multipart 邮件；纯文本不是附件，附件请用 `attach` / `attachData`
- 未调用 `mailer()` / `Mail::mailer()` 时使用 `MAIL_MAILER` 默认驱动，无需也不应提前访问 `$mailer` 属性
