<?php

namespace App\Models;

use App\Events\TaskCompleted;
use App\Events\TaskCreated;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'name', 'application_id','user_id','completed_at', 'stage', 'notes'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param Application $application
     * @param $name
     * @param User $user
     * @param $stage
     * @return Application|null
     */
    public static function create_task(Application $application,$name, User $user, $stage)
    {
        $task = null;
        \DB::transaction(function () use (&$task, $name, &$application, $user, $stage){
            $task = self::create([
                'name' => $name,
                'user_id' => $user->id,
                'application_id' => $application->id,
                'stage' => $stage
            ]);

            event(new TaskCreated($task, $application, $user));
        });

        return $application;
    }

    public static function complete_task(self $task, $notes = '')
    {
        $task->completed_at = date("Y-m-d H:i:s");
        $task->notes  =  $notes;
        $task->save();

        event(new TaskCompleted($task));

        return $task;
    }
}
