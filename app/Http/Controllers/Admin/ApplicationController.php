<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Task;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $this->can(['view-applications','approve-applications','review-applications'], function (){
            return \user()->hasRole('super-admin');
        });
        //todo: probably load applications depending on whether approver or review

        $applications  = Application::filter($request)
            ->latest('applications.created_at')
            ->paginate(20)
            ->appends($request->except('page'));

        return view('admin.applications.index',[
            'applications' => $applications
        ])->with('page_title', "Applications");
    }

    public function show(Application $application, Request $request)
    {
        $this->can(['view-applications','approve-applications','review-applications']);

        $application->load(['applicable','user','tasks','uploads']);

        $tasks = $application->tasks()->with(['user'])->latest('tasks.created_at')->get();

        return view('admin.applications.view',[
            'application' => $application,
            'tasks' => $tasks
        ])->with("page_title","{$application} - Application Details");
    }

    public function queue(Request $request)
    {
        $user = user();

        $applications = Application::filter($request)
            ->ofQueue($user)
            ->latest('applications.created_at')
            ->paginate(20)
            ->appends($request->except('page'));

        return view('admin.applications.queue',[
            "applications" =>  $applications
        ])->with("page_title", "Tasks Queue");
    }

    public function inbox(Request $request)
    {
        $user = user();

        $applications = Application::filter($request)
            ->myTasks($user)
            ->latest('applications.created_at')
            ->paginate(20)
            ->appends($request->except('page'));

        return view('admin.applications.inbox',[
            "applications" =>  $applications
        ])->with("page_title", "Tasks Inbox");
    }

    public function completed(Request $request)
    {
        $user = user();

        $applications = Application::filter($request)
            ->myTasks($user, true)
            ->latest('applications.created_at')
            ->paginate(20)
            ->appends($request->except('page'));

        return view('admin.applications.completed',[
            "applications" =>  $applications
        ])->with("page_title", "Tasks Inbox");
    }
    public function pick( Request $request)
    {
        $this->can(['review-applications','approve-applications']);

        //if application is already picked, pick another random application
        $task = $this->pick_random_application(user());

        if (!$task){
            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'danger', 'message' => "Ooops! Could not pick task, no other available tasks"]
                ]);
        }

        return redirect()->route('admin.tasks.view', [$task->id]);
    }

    public function review(Application $application, Request $request)
    {
        $this->_can('review-applications', function () use($application){
            return $application->assigned_id  ==  user()->id;
        });

        $this->validate($request,[
            'status' => 'in:reviewed,corrections'
        ],[
            'status.*' => "Invalid request"
        ]);

        $task = $application->current_task;
        if ($request->input('status') == 'reviewed'){
            Application::review($application, $task);
        }else{
            Application::send_to_corrections($application, $task);
        }

        return redirect()->route('admin.tasks.queue')
            ->with('alerts', [
                ['type' => 'info', 'message' => "Application has been {$request->get('status')}!"]
            ]);
    }

    public function approve(Application $application, Request $request)
    {
        $this->_can(['approve-applications','review-applications'], function () use($application){
            return $application->assigned_id  ==  user()->id;
        });

        $this->validate($request,[
            'status' => 'in:rejected,approved'
        ],[
            'status.*' => "Invalid request"
        ]);

        $task = $application->current_task;
        Application::approve($application, $task);

        return redirect()->route('admin.tasks.queue')
            ->with('alerts', [
                ['type' => 'info', 'message' => "Application {$application} has been updated successfully!"]
            ]);
    }

    public function reject(Application $application, Request $request)
    {
        $this->_can(['approve-applications','review-applications'], function () use($application){
            return $application->assigned_id  ==  user()->id;
        });

        $this->validate($request,[
            "comment" => "required"
        ]);

        $task = $application->current_task;
        Application::reject($application, $task, $request->input('comment'));

        return redirect()->route('admin.tasks.queue')
            ->with('alerts', [
                ['type' => 'info', 'message' => "Application {$application} has been updated successfully!"]
            ]);
    }

    protected function pick_random_application( User $user)
    {
        $application = null;
        \DB::transaction(function () use (&$application, $user){
            $application = Application::ofQueue($user)
                ->whereNull("assigned_id")
                ->latest('applications.created_at','ASC')
                ->first();

            if ($application){
                Application::pick($application, $user);
            }
        });

        return $application;
    }
}
