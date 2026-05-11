<?php

namespace App\Simple;

use App\Models\Simple\SuperLog;
use Illuminate\Support\Facades\Auth;

trait HasJobLogs
{
    //
    public static function bootHasJobLogs()
    {
        static::created(function ($model) {
            $model->writeJobLog('created');
        });
        static::updated(function ($model) {
            $model->writeJobLog('updated');
        });
        static::deleted(function ($model) {
            $model->writeJobLog('deleted');
        });
    }

    protected function writeJobLog(string $action)
    {
        if ($this instanceof SuperLog) {
            return;
        }
        $before = null;
        $after = null;
        switch ($action) {
            case 'created':
                $after = $this->getAttributes();
                break;
            case 'updated':
                // dd($this->getChanges());
                $changed = $this->getChanges();
                $before = array_intersect_key($this->getOriginal(), $changed);
                $after = $changed;
                break;
            case 'deleted':
                $before = $this->getOriginal();
                break;
        }

        SuperLog::create([
            'table_name' => $this->getTable(),
            'record_id' => $this->getKey(),
            'action' => $action,
            'before' => $before,
            'after' => $after,
            'user_id' => Auth::id(), // bisa null kalau tidak ada user
            'logged_at' => now(),
        ]);
    }
}
