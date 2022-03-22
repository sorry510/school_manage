<?php

namespace App\Providers;

use App\Models\AdminMessage;
use App\Models\Student;
use App\Models\Teacher;
use App\Observers\AdminMessageObserver;
use App\Observers\StudentObserver;
use App\Observers\TeacherObserver;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!defined('APP_URL')) {
            define('APP_URL', config('app.url'));
        }
        Teacher::observe(TeacherObserver::class);
        Student::observe(StudentObserver::class);
        AdminMessage::observe(AdminMessageObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Collection::macro('join', function ($glue, $finalGlue = '') {
            if ($finalGlue === '') {
                return $this->implode($glue);
            }

            $count = $this->count();

            if ($count === 0) {
                return '';
            }

            if ($count === 1) {
                return $this->last();
            }

            $collection = new static($this->items);

            $finalItem = $collection->pop();

            return $collection->implode($glue) . $finalGlue . $finalItem;
        });
    }
}
