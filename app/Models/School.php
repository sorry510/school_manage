<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Base
{
    use SoftDeletes;

    protected $table = 'school';

    public function schoolTeachers()
    {
        return $this->hasMany(SchoolTeacher::class, 'school_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'school_id');
    }
}
