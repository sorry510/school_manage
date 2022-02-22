<?php

namespace App\Http\Controllers\Teacher;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return $this->resJson(ErrorCode::SUCCESS, $user);
    }
}
