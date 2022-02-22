<?php

namespace App\Http\Controllers\Student;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return $this->resJson(ErrorCode::SUCCESS, $user);
    }
}
