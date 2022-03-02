<?php

namespace App\Events;

use App\Events\SchoolApplyEvent;
use App\Models\School;
use App\Models\SchoolApply;
use App\Models\SchoolTeacher;
use Illuminate\Support\Facades\DB;

class SchoolApplyListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SchoolApplyEvent  $event
     * @return void
     */
    public function handle(SchoolApplyEvent $event)
    {
        $schoolApply = $event->schoolApply;
        if ($schoolApply->status == SchoolApply::STATUS_SUCCESS) {
            // 创建学校和教师管理员
            DB::transaction(function () use ($schoolApply) {
                $school = School::create([
                    'name' => $schoolApply->name,
                ]);
                SchoolTeacher::create([
                    'teacher_id' => $schoolApply->teacher_id,
                    'teacher_type' => SchoolTeacher::TYPE_ADMIN,
                    'school_id' => $school->id,
                ]);
            });
        }
    }
}
