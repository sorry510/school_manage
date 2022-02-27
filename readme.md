## Laravel 5.5.* 教务系统
===============

### 安装

```
composer install
cp .env.example .env
```

### 安装 admin 数据表

```
php artisan admin:install
```

### socket-io

```
php artisan socketio start
php artisan socketio start -d # linux 下守护
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