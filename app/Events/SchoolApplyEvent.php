<?php

namespace App\Events;

use App\Models\SchoolApply;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SchoolApplyEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $schoolApply;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SchoolApply $schoolApply)
    {
        $this->schoolApply = $schoolApply;
    }
}
