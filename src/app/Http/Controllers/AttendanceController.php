<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{
    protected $attendanceService;
    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function search(Request $request)
    {
        //押下ボタン判定
        if ($request->has("prev_date")) {

            $search_date = Carbon::parse($request['search_date'])->subDay();
        } elseif ($request->has("next_date")) {

            $search_date = Carbon::parse($request['search_date'])->addDay();
        } elseif ($request->has("page")) {

            $search_date = Session::get('search_date');

            $search_date = Carbon::parse($search_date);
        } else {

            $search_date = Carbon::today();
        }
        Session::put('search_date', $search_date);
        $search_date = $search_date->toDateString('Y-m-d');

        $sumdata = $this->attendanceService->getAttendanceSummaryFromDate($search_date);
        return view(
            'search',
            compact('sumdata', 'sumdata'),
            compact('search_date', 'search_date')
        );
    }

    public function index(Request $request)
    {

        $readonly_status = [
            'attendance_start' => false,
            'attendance_end' => true,
            'break_start' => true,
            'break_end' => true,
        ];
        $today = Carbon::today();

        $user_id = Auth::user()['id'];
        $todayDate = Carbon::today()->toDateString();
        $attendances = Attendance::with('breaktime')->where('user_id', $user_id)->whereDate('work_date', $todayDate)->whereNull('end_time');

        if ($attendances->exists()) {

            $break_data = $attendances->first()['breaktime'];
            if (isset($break_data['end_time'])) {
                $readonly_status = [
                    'attendance_start' => true,
                    'attendance_end' => true,
                    'break_start' => true,
                    'break_end' => false,
                ];
            }

            $readonly_status = [
                'attendance_start' => true,
                'attendance_end' => false,
                'break_start' => false,
                'break_end' => true,
            ];
        }
        return view('attendance', compact('readonly_status'));
    }

    //押下ボタン判定
    public function attendanceLogic(Request $request)
    {
        if ($request->has('start')) {
            return $this->attendanceStart($request);
        } elseif ($request->has('end')) {
            return $this->attendanceEnd($request);
        } elseif ($request->has('break-start')) {
            return $this->breakStart($request);
        } elseif ($request->has('break-end')) {
            return $this->breakEnd($request);
        }
    }

    protected function attendanceStart(Request $request)
    {

        $user = $request->only(['user_id']);
        $data = [
            'user_id' => $request->input('user_id'),
            'work_date' => Carbon::now()->format('Y-m-d'),
            'start_time' => Carbon::now()->format('H:i:s'),
        ];


        Attendance::create($data);


        $readonly_status = [
            'attendance_start' => true,
            'attendance_end' => false,
            'break_start' => false,
            'break_end' => true,
        ];
        return view('attendance', compact('readonly_status'));
    }



    protected function attendanceEnd(Request $request)
    {

        $user = $request->only(['user_id']);

        $attendances = Attendance::where('user_id', $user)->get();
        if (!isset($attendances)) {
            return redirect()->back()->with('error');
        }
        $nowdate = Carbon::now();
        $before_date_endtime = Carbon::createFromTime(23, 59, 59);
        foreach ($attendances as $attendance) {

            //前日以降は23:59
            if ($nowdate->isSameDay($attendance->work_date)) {
                $time = [
                    'end_time' => $nowdate->format('H:i:s'),
                ];
            } else {
                $time = [
                    'end_time' => $before_date_endtime
                ];
            }
            $attendance->update($time);
        }

        $readonly_status = [
            'attendance_start' => false,
            'attendance_end' => true,
            'break_start' => true,
            'break_end' => true,
        ];
        return view('attendance', compact('readonly_status'));
    }


    protected function breakStart(Request $request)
    {

        $todayDate = Carbon::today()->toDateString();
        $user = $request->only(['user_id']);
        $attendance = Attendance::where('user_id', $user)->whereDate('work_date', $todayDate)->whereNull('end_time')->get();
        $data = [
            'user_id' => $request->input('user_id'),
            'attendance_id' =>  $attendance->first()->id,
            'start_time' => Carbon::now()->format('H:i:s'),
        ];
        BreakTime::create($data);

        $readonly_status = [
            'attendance_start' => true,
            'attendance_end' => true,
            'break_start' => true,
            'break_end' => false,
        ];
        return view('attendance', compact('readonly_status'));
    }

    protected function breakEnd(Request $request)
    {
        $todayDate = Carbon::today()->toDateString();
        $user_id = $request->only(['user_id']);
        $breaks = BreakTime::with(['attendance' => function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        }])->whereNull('end_time')->get();


        $nowdate = Carbon::now();
        $before_date_endtime = Carbon::createFromTime(23, 59, 59);
        foreach ($breaks as $break) {
            //前日以降は23:59
            $data_work_date = $break['attendance']->work_date;
            if ($nowdate->isSameDay($data_work_date)) {
                $time = [
                    'end_time' => $nowdate->format('H:i:s'),
                ];
            } else {
                $time = [
                    'end_time' => $before_date_endtime

                ];
            }
            $break->update($time);
        }

        $readonly_status = [
            'attendance_start' => true,
            'attendance_end' => false,
            'break_start' => false,
            'break_end' => true,
        ];
        return view('attendance', compact('readonly_status'));
    }
}
