## Laravel 5.5.* 教务系统
===============

### 安装
>ps: `window` 下因为涉及到 `ext-pcntl` 扩展，composer 无法安装成功

```
git clone https://github.com/sorry510/school_manage
cd school_manage
composer install
cp .env.example .env
```

### 部署配置

#### 缓存

```
# 配置缓存
php artisan route:cache
php artisan config:cache
php artisan view:cache

# 清除缓存
php artisan config:clear
php artisan route:clear
php artisan view:clear
```
#### .env 文件

- APP_ENV=production
> 环境设置为 production


- APP_DEBUG=false
> 取消调试模式(接口错误不再显示具体错误信息)

#### heroku 部署测试预览地址
#### 后台管理地址

```
http://dry-garden-13043.herokuapp.com/admin
user: admin
password: admin
```
#### 前台网站地址

```
http://dry-garden-13043.herokuapp.com/front.html
教师: 491559675@qq.com 123456
学生: zhangs 123456
```

#### 接口文档

```
http://dry-garden-13043.herokuapp.com/api/documentation
```

### heroku 部署配置
#### 主项目 Procfile
```
web: vendor/bin/heroku-php-apache2 public/
```

#### SocketIO Procfile

```
web: php artisan socketio start
```

### admin 设置

```
php artisan admin:install
```

### laravel-Passport 设置

```
php artisan passport:install # 生成2条数据
php artisan passport:keys # 生成秘钥
```

### 开启 socket-io 命令

```
php artisan socketio start
php artisan socketio start --d # linux 下守护
```

### 注意事项
#### 第三方登录使用了 postMessage 传输信息
>需要注意 origin 使用的协议(http/https)，访问时必须保持一致

```
APP_URL=http://localhost:8000
```

#### `.env` 配置

```
APP_URL = 填写部署的地址
TEACHER_AUTO_ACTIVE = true # if false, will send active mail
DB_CONNECTION = pgsql_heroku # 如果使用 heroku 的 postgres 数据库
```

#### 前端页面 public/static
> 是 webpack 打包后的文件，已经默认绑定了 websocket 中的 connect，搜索 `herokuapp` 来修改地址
> 前端项目地址：https://github.com/sorry510/school_manage_front

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

#### php-cs 格式化代码
>根据`.php-cs-fixer.dist.php` 配置进行格式化文件，更多配置参考[PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

```
composer global require friendsofphp/php-cs-fixer
php-cs-fixer fix .
```