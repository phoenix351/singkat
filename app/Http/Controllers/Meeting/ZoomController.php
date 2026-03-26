<?php

namespace App\Http\Controllers\Meeting;

use App\Http\Controllers\Controller;
use App\Models\Meeting\Zoom as ZoomModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Jubaer\Zoom\Facades\Zoom;

class ZoomController extends Controller
{
    //
    public function dashboard()
    {
        $upcoming_meetings = Zoom::getUpcomingMeeting();
        $previous_meetings = Zoom::getPreviousMeeting();
        $live_meetings = Zoom::getLiveMeeting();
        $all_meetings = Zoom::getAllMeeting();

        $meetings = ZoomModel::with('user')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Meeting/Dashboard', [
            'meetings' => $meetings,
            'all_meetings' => $all_meetings,
            'upcoming_meetings' => $upcoming_meetings,
            'previous_meetings' => $previous_meetings,
            'live_meetings' => $live_meetings,
        ]);
    }

    public function index(String $id = null)
    {
        $user_meetings = ZoomModel::where('user_id', Auth::user()->id)->get();

        if ($id !== null) {
            $meeting = Zoom::getMeeting($id);
            $invitation = Zoom::getMeetingInvitation($id);
            // dd($meeting, $invitation);
        }

        return Inertia::render('Zoom/Index', [
            'user_meetings' => $user_meetings,
            'meeting' => $meeting ?? null,
            'invitation' => $invitation ?? null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $type = 2;
        $start_date = $request->start_date['year'] . '-' . $request->start_date['month'] . '-' . $request->start_date['day'];
        $end_date = null;
        if ($request->end_date) {
            $end_date = $request->end_date['year'] . '-' . $request->end_date['month'] . '-' . $request->end_date['day'];
            $type = 8;
        }

        if ($request->end_date) {
            $start_date_string = $request->start_date['year'] . '-' . $request->start_date['month'] . '-' . $request->start_date['day'];
            $end_date_string = $request->end_date['year'] . '-' . $request->end_date['month'] . '-' . $request->end_date['day'];

            $start_date = new \DateTime($start_date_string . ' ' . $request->time . ' ' . $request->period, new \DateTimeZone('Asia/Singapore'));
            $end_date = new \DateTime($end_date_string . ' ' . $request->time . ' ' . $request->period, new \DateTimeZone('Asia/Singapore'));

            $start_date->setTimezone(new \DateTimeZone('UTC'));
            $end_date->setTimezone(new \DateTimeZone('UTC'));

            $upcoming_meetings = Zoom::getUpcomingMeeting();

            for ($date = clone $start_date; $date <= $end_date; $date->modify('+1 day')) {
                $start_time = $date->format('Y-m-d\TH:i:s');
                $end_time = (clone $date)->modify('+' . $request->duration * 60 . ' minutes')->format('Y-m-d\TH:i:s');

                $conflict_count = 0;

                foreach ($upcoming_meetings['data']['meetings'] as $meeting) {
                    $meeting_start_time = new \DateTime($meeting['start_time'], new \DateTimeZone('UTC'));
                    $meeting_start_time = $meeting_start_time->format('Y-m-d\TH:i:s');

                    $meeting_end_time = new \DateTime($meeting_start_time . ' + ' . $meeting['duration'] . ' minutes', new \DateTimeZone('UTC'));
                    $meeting_end_time = $meeting_end_time->format('Y-m-d\TH:i:s');

                    if (($start_time >= $meeting_start_time && $start_time < $meeting_end_time) ||
                        ($end_time > $meeting_start_time && $end_time <= $meeting_end_time) ||
                        ($start_time <= $meeting_start_time && $end_time >= $meeting_end_time)) {
                        $conflict_count++;
                    }
                }

                if ($conflict_count >= 2) {
                    return Redirect::route('zoom.index')->with('error', 'Gagal membuat meeting karena sudah ada 2 zoom meeting di waktu yang sama');
                }
            }
        } else {
            $start_time_dt = new \DateTime($start_date . ' ' . $request->time . ' ' . $request->period, new \DateTimeZone('Asia/Singapore'));
            $start_time_dt->setTimezone(new \DateTimeZone('UTC'));
            $start_time = $start_time_dt->format('Y-m-d\TH:i:s');

            $end_time_dt = new \DateTime($start_time . ' + ' . $request->duration * 60 . ' minutes', new \DateTimeZone('UTC'));
            $end_time = $end_time_dt->format('Y-m-d\TH:i:s');

            $upcoming_meetings = Zoom::getUpcomingMeeting();
            $conflict_count = 0;

            foreach ($upcoming_meetings['data']['meetings'] as $meeting) {
                $meeting_start_time = new \DateTime($meeting['start_time'], new \DateTimeZone('UTC'));
                $meeting_start_time = $meeting_start_time->format('Y-m-d\TH:i:s');

                $meeting_end_time = new \DateTime($meeting_start_time . ' + ' . $meeting['duration'] . ' minutes', new \DateTimeZone('UTC'));
                $meeting_end_time = $meeting_end_time->format('Y-m-d\TH:i:s');

                if (($start_time >= $meeting_start_time && $start_time < $meeting_end_time) ||
                    ($end_time > $meeting_start_time && $end_time <= $meeting_end_time) ||
                    ($start_time <= $meeting_start_time && $end_time >= $meeting_end_time)) {
                    $conflict_count++;
                }
            }

            if ($conflict_count >= 2) {
                return Redirect::route('zoom.index')->with('error', 'Gagal membuat meeting karena sudah ada 2 zoom meeting di waktu yang sama');
            }
        }

        $start_date = $request->start_date['year'] . '-' . $request->start_date['month'] . '-' . $request->start_date['day'];
        $recurrence = 0;
        if ($end_date !== null) {
            $end_date = $request->end_date['year'] . '-' . $request->end_date['month'] . '-' . $request->end_date['day'];
            $start = strtotime($start_date);
            $end = strtotime($end_date);
            $recurrence = floor(($end - $start) / (60 * 60 * 24)) + 1;
        }

        $start_time = date('Y-m-d\TH:i:s', strtotime($start_date . ' ' . $request->time . ' ' . $request->period));

        if ($type == 8) {
            $meetings = Zoom::createMeeting([
                "agenda" => $request->topic,
                "topic" => $request->topic,
                "type" => $type,
                "duration" => $request->duration * 60,
                "password" => $request->password,
                "timezone" => 'Asia/Singapore',
                "start_time" => $start_time,
                "recurrence" => [
                    'type' => 1,
                    'repeat_interval' => 1,
                    'end_times' => $recurrence,
                ],
                "settings" => [
                    'join_before_host' => true,
                    'host_video' => false,
                    'participant_video' => false,
                    'mute_upon_entry' => true,
                    'waiting_room' => true,
                    'audio' => 'both',
                    'auto_recording' => 'none',
                    'approval_type' => 2,
                ],
            ]);
        } else {
            $meetings = Zoom::createMeeting([
                "agenda" => $request->topic,
                "topic" => $request->topic,
                "type" => $type,
                "duration" => $request->duration * 60,
                "password" => $request->password,
                "timezone" => 'Asia/Singapore',
                "start_time" => $start_time,
                "settings" => [
                    'join_before_host' => true,
                    'host_video' => false,
                    'participant_video' => false,
                    'mute_upon_entry' => true,
                    'waiting_room' => true,
                    'audio' => 'both',
                    'auto_recording' => 'none',
                    'approval_type' => 2,
                ],
            ]);
        }

        if ($meetings['status'] == true) {
            $meeting = new ZoomModel();
            $meeting->user_id = Auth::user()->id;
            $meeting->meeting_id = $meetings['data']['id'];
            $meeting->topic = $meetings['data']['topic'];
            $meeting->jumlah_peserta = $request->participant;
            $meeting->bidang = $request->bidang;
            $meeting->co_host = $request->host;
            $meeting->start_date = $start_date;
            $meeting->end_date = $end_date;
            $meeting->time = $request->time;
            $meeting->period = $request->period;
            $meeting->duration = $request->duration;
            $meeting->save();
            return Redirect::route('zoom.index')->with('success', 'Meeting telah dibuat');
        } else {
            return Redirect::route('zoom.index')->with('error', 'Gagal membuat meeting');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $meeting = ZoomModel::where('meeting_id', $id)->first();
        $delete = Zoom::deleteMeeting($id);
        if ($delete['status'] == true) {
            $meeting->delete();
            return Redirect::route('zoom.index')->with('success', 'Meeting berhasil dihapus');
        } else {
            return Redirect::route('zoom.index')->with('error', 'Gagal menghapus meeting');
        }
    }
}
