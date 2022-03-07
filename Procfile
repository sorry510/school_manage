release: php artisan migrate
release: php artisan passport:keys --force
release: php artisan config:cache
release: php artisan route:cache
web: vendor/bin/heroku-php-apache2 public/