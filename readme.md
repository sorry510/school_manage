## Laravel 5.5.* 教务系统
===============

### 安装

```
composer install
cp .env.example .env
```

### 部署配置

#### 主项目 Procfile
```
web: vendor/bin/heroku-php-apache2 public/
```

#### SocketIO Procfile

```
web: php artisan socketio start
```

### 安装 admin 数据表

```
php artisan admin:install
```

### laravel-Passport

```
php artisan passport:install
php artisan passport:keys
```

### socket-io

```
php artisan socketio start
php artisan socketio start --d # linux 下守护
```

### 第三方登录使用了 postMessage 传输信息
>需要注意 origin 使用的协议(http/https)，访问时必须保持一致

```
APP_URL=http://localhost:8000
```

### 开发扩展
#### 安装ide-help

[laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper)

- PHPDoc generation for Laravel Facades

```
php artisan ide-helper:generate
```

- PHPDocs for models

```
php artisan ide-helper:models
```

- PhpStorm Meta file

```
- php artisan ide-helper:meta
```

### swagger 文档

#### 生成 json 文件

```
php artisan l5-swagger:generate
```

#### 访问接口文档

```
{{host}}/api/documentation
```