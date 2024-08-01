<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Pagination\LengthAwarePaginator;

//データ取得用
class AttendanceService
{
    public function getAttendanceSummaryFromDate($search_date)
    {
        $attendances =
        Attendance::with(['user', 'breaktime'])
        ->whereDate('work_date', $search_date)->get();
        return $this->processAttendances($attendances);
    }

    public function getAttendanceSummaryFromUser($user_id)
    {
        $attendances = Attendance::with(['user', 'breaktime'])
            ->where('user_id', $user_id)->orderBy('work_date', 'asc')->get();
        return $this->processAttendances($attendances);
    }


    private function processAttendances($attendances)
    {
        $sumdata = [];

        foreach ($attendances as $attendance) {
            $user_name = $attendance->user->name;
            $total_work_time = $this->calculateWorkTime($attendance->start_time, $attendance->end_time);
            $total_break_time = $this->calculateBreakTime($attendance->breaktime);

            $sumdata[] = [
                'name' => $user_name,
                'work_date' => $attendance->work_date,
                'work_start' => $attendance->start_time,
                'work_end' => $attendance->end_time,
                'work_total_time' => $total_work_time,
                'break_total_time' => $total_break_time,
            ];
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $page = 5;
        $currentItems = array_slice($sumdata, ($currentPage - 1) * $page, $page);
        $sumdata = new LengthAwarePaginator($currentItems, count($sumdata), $page, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return $sumdata;
    }

    private function calculateWorkTime($start_time, $end_time)
    {
        if (isset($end_time)) {
            return gmdate('H:i:s', strtotime($end_time) - strtotime($start_time));
        }
        return null;
    }

    private function calculateBreakTime($breaks)
    {
        $total_break_time = 0;
        foreach ($breaks as $break) {
            if (isset($break->end_time)) {
                $total_break_time += (strtotime($break->end_time) - strtotime($break->start_time));
            }
        }
        return gmdate('H:i:s', $total_break_time);
    }
}
