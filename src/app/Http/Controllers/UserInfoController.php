<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class UserInfoController extends Controller
{

    protected $attendanceService;
    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index()
    {
        $users = User::all();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $page = 5;
        $currentItems = $users->slice(($currentPage - 1) * $page, $page)->values();
        $users = new LengthAwarePaginator($currentItems, count($users), $page, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return view(
            'userlist',
            compact('users', 'users'),
        );
    }

    public function attendance(Request $request)
    {
        $user_id = $request['user_id'];
        $user_name = $request['user_name'];
        $sumdata = $this->attendanceService->getAttendanceSummaryFromUser($user_id);
        return view(
            'search-user',
            compact('sumdata', 'user_name', 'user_id')
        );
    }
}
