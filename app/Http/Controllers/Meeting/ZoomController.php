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
        $all_meetings = Zoom::getAllMeeting(300);

        $meetings = ZoomModel::with('user')->orderBy('created_at', 'desc')->limit(20)->get();

        return Inertia::render('Meeting/Dashboard', [
            'meetings' => $meetings,
            'all_meetings' => $all_meetings,
            'upcoming_meetings' => $upcoming_meetings,
            'previous_meetings' => $previous_meetings,
            'live_meetings' => $live_meetings,
        ]);
    }

    public function index(Request $request, ?string $id = null)
    {
        $paginated = $request->input('paginated', 10);
        $currentPage = $request->input('page', $request->input('currentPage', 1));
        $filters = $request->input('filters', []);

        $query = ZoomModel::where('user_id', Auth::user()->id);

        if ($request->has('filters')) {
            if (!empty($filters['topic'])) {
                $query->where('topic', 'like', '%' . $filters['topic'] . '%');
            }
            if (!empty($filters['start_date'])) {
                $query->where('start_date', 'like', '%' . $filters['start_date'] . '%');
            }
            if (!empty($filters['end_date'])) {
                $searchEnd = $filters['end_date'];
                $query->where(function ($q) use ($searchEnd) {
                    $q->where('end_date', 'like', '%' . $searchEnd . '%')
                      ->orWhere(function ($q2) use ($searchEnd) {
                          $q2->whereNull('end_date')
                             ->where('start_date', 'like', '%' . $searchEnd . '%');
                      });
                });
            }
            if (!empty($filters['time'])) {
                $searchTime = $filters['time'];
                $query->where(function ($q) use ($searchTime) {
                    $q->where('time', 'like', '%' . $searchTime . '%')
                      ->orWhere('period', 'like', '%' . $searchTime . '%');
                });
            }
            if (!empty($filters['duration'])) {
                $query->where('duration', 'like', '%' . $filters['duration'] . '%');
            }
            if (!empty($filters['jumlah_peserta'])) {
                $query->where('jumlah_peserta', 'like', '%' . $filters['jumlah_peserta'] . '%');
            }
            if (!empty($filters['bidang'])) {
                $query->where('bidang', 'like', '%' . $filters['bidang'] . '%');
            }
            if (!empty($filters['co_host'])) {
                $query->where('co_host', 'like', '%' . $filters['co_host'] . '%');
            }
        }

        $query->orderBy('start_date', 'desc')
              ->orderBy('time', 'desc')
              ->orderBy('id', 'desc');

        $user_meetings = $query->paginate($paginated, ['*'], 'page', $currentPage)->withQueryString();

        if ($id !== null) {
            $localMeeting = ZoomModel::where('meeting_id', $id)->first();
            $meeting = Zoom::getMeeting($id);
            $meeting['local'] = $localMeeting;
            if ($request->wantsJson() && !$request->header('X-Inertia')) {
                return response()->json([
                    'meeting' => $meeting,
                ]);
            }
            // dd($meeting);
            // $invitation = Zoom::getMeetingInvitation($id);
        }

        return Inertia::render('Meeting/Index', [
            'user_meetings' => $user_meetings,
            'filters' => $filters,
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
        $conflictError = $this->validateMaxConcurrentMeetings($request);
        if ($conflictError) {
            return Redirect::route('meeting.index')->with('error', $conflictError);
        }
        $type = 2;
        $start_date = $request->datepicker['startDate'];
        $end_date = null;
        if ($request->input('datepicker.endDate')) {
            $end_date = $request->datepicker['endDate'];
            $type = 8;
        }

        if ($request->input('datepicker.endDate')) {
            $start_date = new \DateTime($start_date . ' ' . $request->time . ' ' . $request->period, new \DateTimeZone('Asia/Singapore'));
            $end_date = new \DateTime($end_date . ' ' . $request->time . ' ' . $request->period, new \DateTimeZone('Asia/Singapore'));

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
                        ($start_time <= $meeting_start_time && $end_time >= $meeting_end_time)
                    ) {
                        $conflict_count++;
                    }
                }

                if (false && $conflict_count >= 2) {
                    return Redirect::route('meeting.index')->with('error', 'Gagal membuat meeting karena sudah ada 2 zoom meeting di waktu yang sama');
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
                    ($start_time <= $meeting_start_time && $end_time >= $meeting_end_time)
                ) {
                    $conflict_count++;
                }
            }

            if (false && $conflict_count >= 2) {
                return Redirect::route('meeting.index')->with('error', 'Gagal membuat meeting karena sudah ada 2 zoom meeting di waktu yang sama');
            }
        }

        $start_date = $request->datepicker['startDate'];
        $recurrence = 0;
        if ($end_date !== null) {
            $end_date = $request->datepicker['endDate'];
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
            $meeting->meeting_id = (string) $meetings['data']['id'];
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
            return Redirect::route('meeting.index')->with('success', 'Meeting telah dibuat');
        } else {
            return Redirect::route('meeting.index')->with('error', 'Gagal membuat meeting');
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
        if ($request->isMethod('patch')) {
            $meeting_id = $request->input('meeting_id');
            if (!$meeting_id) return Redirect::route('meeting.index')->with('error', 'Gagal update meeting');
            $meetings = ['status' => false];
            $conflictError = $this->validateMaxConcurrentMeetings($request, (string) $meeting_id);
            if ($conflictError) {
                return Redirect::back()->with('error', $conflictError);
            }
            $conflictError = $this->validateMaxConcurrentMeetings($request);
        if ($conflictError) {
            return Redirect::route('meeting.index')->with('error', $conflictError);
        }
        $type = 2;
            $start_date = $request->datepicker['startDate'];
            $end_date = null;
            if ($request->input('datepicker.endDate')) {
                $end_date = $request->datepicker['endDate'];
                $type = 8;
            }

            if ($request->input('datepicker.endDate')) {
                $start_date = new \DateTime($start_date . ' ' . $request->time . ' ' . $request->period, new \DateTimeZone('Asia/Singapore'));
                $end_date = new \DateTime($end_date . ' ' . $request->time . ' ' . $request->period, new \DateTimeZone('Asia/Singapore'));

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
                            ($start_time <= $meeting_start_time && $end_time >= $meeting_end_time)
                        ) {
                            $conflict_count++;
                        }
                    }

                    if (false && $conflict_count >= 2) {
                        return Redirect::route('meeting.index')->with('error', 'Gagal membuat meeting karena sudah ada 2 zoom meeting di waktu yang sama');
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
                        ($start_time <= $meeting_start_time && $end_time >= $meeting_end_time)
                    ) {
                        $conflict_count++;
                    }
                }

                if (false && $conflict_count >= 2) {
                    return Redirect::route('meeting.index')->with('error', 'Gagal membuat meeting karena sudah ada 2 zoom meeting di waktu yang sama');
                }
            }

            $start_date = $request->datepicker['startDate'];
            $recurrence = 0;
            if ($end_date !== null) {
                $end_date = $request->datepicker['endDate'];
                $start = strtotime($start_date);
                $end = strtotime($end_date);
                $recurrence = floor(($end - $start) / (60 * 60 * 24)) + 1;
            }

            $start_time = date('Y-m-d\TH:i:s', strtotime($start_date . ' ' . $request->time . ' ' . $request->period));

            if ($type == 8) {
                $meetings = Zoom::updateMeeting($meeting_id, [
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
                $meetings = Zoom::updateMeeting($meeting_id, [
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

            $notFoundOnUpdate = isset($meetings['message']) && (str_contains($meetings['message'], '404') || str_contains($meetings['message'], '3001'));
            if ($meetings['status'] == true || $notFoundOnUpdate) {
                $meeting = ZoomModel::where('meeting_id', $meeting_id)->first();
                $meeting->topic = $request->topic;
                $meeting->jumlah_peserta = $request->participant;
                $meeting->bidang = $request->bidang;
                $meeting->co_host = $request->host;
                $meeting->start_date = $start_date;
                $meeting->end_date = $end_date;
                $meeting->time = $request->time;
                $meeting->period = $request->period;
                $meeting->duration = $request->duration;
                $meeting->save();
                return Redirect::route('meeting.index')->with('success', 'Meeting sudah diupdate');
            } else {
                return Redirect::back()->with('error', 'Gagal update meeting');
            }
        }
        $meeting = ZoomModel::where('meeting_id', $id)->first();
        $meeting_zoom = Zoom::getMeeting($id);
        $data_zoom = collect([]);
        if ($meeting_zoom['status'] == true) {
            $data_zoom = $meeting_zoom['data'];
        }
        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'meeting' => $meeting,
                'meeting_zoom' => $data_zoom,
            ]);
        }
        return Inertia::render('Meeting/Update', [
            'meeting' => $meeting,
            'meeting_zoom' => $data_zoom,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $meeting = ZoomModel::where('meeting_id', $id)->first();
        $delete = Zoom::deleteMeeting($id);
        $notFoundInZoom = isset($delete['message']) && (str_contains($delete['message'], '404') || str_contains($delete['message'], '3001'));
        if ($notFoundInZoom) {
            $delete['status'] = true;
        }
        if ($delete['status'] == true) {
            $meeting->delete();
            return Redirect::route('meeting.index')->with('success', 'Meeting berhasil dihapus');
        } else {
            return Redirect::route('meeting.index')->with('error', 'Gagal menghapus meeting');
        }
    }

    /**
     * Start Zoom meeting as Host for the owner of the meeting request.
     */
    public function start(string $id)
    {
        $localMeeting = ZoomModel::where('meeting_id', $id)->first();

        if (!$localMeeting || (int) $localMeeting->user_id !== (int) Auth::user()->id) {
            return Redirect::route('meeting.index')->with('error', 'Anda tidak memiliki akses untuk memulai meeting ini.');
        }

        $lastDate = $localMeeting->end_date ?: $localMeeting->start_date;
        $today = (new \DateTime('now', new \DateTimeZone('Asia/Singapore')))->format('Y-m-d');

        if ($lastDate < $today) {
            return Redirect::route('meeting.index')->with('error', 'Jadwal meeting sudah berlalu.');
        }

        $meeting = Zoom::getMeeting($id);

        if (!empty($meeting['status']) && !empty($meeting['data']['start_url'])) {
            return Redirect::away($meeting['data']['start_url']);
        }

        return Redirect::route('meeting.index')->with('error', 'Gagal memulai meeting atau tautan Zoom sudah kedaluwarsa.');
        if (false) {
            return Redirect::route('meeting.index')->with('error', 'Gagal memulai meeting.');
        }
    }

    /**
     * Validasi agar tidak ada lebih dari 2 Zoom meeting aktif di waktu yang bersamaan.
     * Mengembalikan pesan error (string) jika bentrok >= 2 meeting, atau null jika aman.
     */
    private function validateMaxConcurrentMeetings(Request $request, ?string $excludeMeetingId = null): ?string
    {
        $tz = new \DateTimeZone('Asia/Singapore');
        $utcTz = new \DateTimeZone('UTC');

        $startDateStr = $request->datepicker['startDate'] ?? null;
        $endDateStr = $request->input('datepicker.endDate') ?: $startDateStr;
        if (!$startDateStr) {
            return 'Tanggal mulai harus diisi.';
        }

        $startDay = new \DateTime($startDateStr . ' ' . $request->time . ' ' . $request->period, $tz);
        $endDay = new \DateTime($endDateStr . ' ' . $request->time . ' ' . $request->period, $tz);
        $durationMinutes = (int) ($request->duration * 60);

        // Kumpulkan semua interval jadwal yang sudah ada (dari Zoom API + Database lokal, dideduplikasi)
        $existingIntervals = [];
        $seenKeys = [];

        $upcoming = Zoom::getUpcomingMeeting();
        if (!empty($upcoming['status']) && !empty($upcoming['data']['meetings'])) {
            foreach ($upcoming['data']['meetings'] as $m) {
                $mId = (string) ($m['id'] ?? '');
                if ($excludeMeetingId && $mId === (string) $excludeMeetingId) {
                    continue;
                }
                if (empty($m['start_time'])) {
                    continue;
                }
                $mStart = new \DateTime($m['start_time'], $utcTz);
                $mStart->setTimezone($tz);
                $mDur = (int) ($m['duration'] ?? 60);
                $mEnd = (clone $mStart)->modify("+{$mDur} minutes");

                $key = $mId . '_' . $mStart->format('Y-m-d_H:i');
                $seenKeys[$key] = true;

                $existingIntervals[] = [
                    'id' => $mId,
                    'topic' => $m['topic'] ?? 'Meeting',
                    'start' => $mStart->getTimestamp(),
                    'end' => $mEnd->getTimestamp(),
                    'time_label' => $mStart->format('H:i') . ' - ' . $mEnd->format('H:i'),
                ];
            }
        }

        // Tambahkan juga dari database lokal (untuk jaga-jaga jika >30 upcoming atau recurring)
        $dbMeetings = ZoomModel::where(function ($q) use ($startDateStr, $endDateStr) {
            $q->whereBetween('start_date', [$startDateStr, $endDateStr])
              ->orWhereBetween('end_date', [$startDateStr, $endDateStr])
              ->orWhere(function ($q2) use ($startDateStr, $endDateStr) {
                  $q2->where('start_date', '<=', $startDateStr)
                     ->where('end_date', '>=', $endDateStr);
              });
        })->get();

        foreach ($dbMeetings as $dbM) {
            $mId = (string) $dbM->meeting_id;
            if ($excludeMeetingId && $mId === (string) $excludeMeetingId) {
                continue;
            }
            $dbStartStr = $dbM->start_date;
            $dbEndStr = $dbM->end_date ?: $dbM->start_date;
            $dStart = new \DateTime($dbStartStr . ' ' . $dbM->time . ' ' . $dbM->period, $tz);
            $dEnd = new \DateTime($dbEndStr . ' ' . $dbM->time . ' ' . $dbM->period, $tz);
            $dDur = (int) (((float) $dbM->duration) * 60);

            for ($cur = clone $dStart; $cur <= $dEnd; $cur->modify('+1 day')) {
                $key = $mId . '_' . $cur->format('Y-m-d_H:i');
                if (isset($seenKeys[$key])) {
                    continue;
                }
                $seenKeys[$key] = true;
                $cEnd = (clone $cur)->modify("+{$dDur} minutes");
                $existingIntervals[] = [
                    'id' => $mId,
                    'topic' => $dbM->topic,
                    'start' => $cur->getTimestamp(),
                    'end' => $cEnd->getTimestamp(),
                    'time_label' => $cur->format('H:i') . ' - ' . $cEnd->format('H:i'),
                ];
            }
        }

        // Periksa setiap hari dari jadwal yang diajukan
        for ($date = clone $startDay; $date <= $endDay; $date->modify('+1 day')) {
            $reqStart = $date->getTimestamp();
            $reqEnd = (clone $date)->modify("+{$durationMinutes} minutes")->getTimestamp();

            // Ambil semua meeting yang beririsan dengan [reqStart, reqEnd]
            $overlapping = [];
            foreach ($existingIntervals as $interval) {
                $intStart = max($reqStart, $interval['start']);
                $intEnd = min($reqEnd, $interval['end']);
                if ($intStart < $intEnd) {
                    $overlapping[] = [
                        'intStart' => $intStart,
                        'intEnd' => $intEnd,
                        'topic' => $interval['topic'],
                        'time_label' => $interval['time_label'],
                    ];
                }
            }

            // Cek apakah ada >= 2 meeting yang berjalan secara BERSAMAAN (irisan saling bertumpuk)
            $count = count($overlapping);
            if ($count >= 2) {
                for ($i = 0; $i < $count; $i++) {
                    for ($j = $i + 1; $j < $count; $j++) {
                        $simStart = max($overlapping[$i]['intStart'], $overlapping[$j]['intStart']);
                        $simEnd = min($overlapping[$i]['intEnd'], $overlapping[$j]['intEnd']);
                        if ($simStart < $simEnd) {
                            $tgl = $date->format('d-m-Y');
                            $jamBentrok = date('H:i', $simStart) . ' - ' . date('H:i', $simEnd);
                            return "Gagal menyimpan jadwal: Pada tanggal {$tgl} pukul {$jamBentrok} WITA sudah ada 2 Zoom meeting aktif secara bersamaan (\"{$overlapping[$i]['topic']}\" & \"{$overlapping[$j]['topic']}\").";
                        }
                    }
                }
            }
        }

        return null;
        if (false) {
            return Redirect::route('meeting.index')->with('error', 'Gagal memulai meeting.');
        }
    }
}
