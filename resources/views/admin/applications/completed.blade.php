@extends('admin.applications.layouts.tasks')

@section('task_title',"Completed Tasks")

@section('applications')
    <div class="table-responsive">
        <table class="table table-striped  table-hover dt-responsive" width="100%">
            <thead>
            <tr>
                <th>#</th>
                <th style="width: 30%">Name</th>
                <th>Submitted By</th>
                <th>Date Submitted</th>
                <th>Status</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($applications as $application)
                <tr>
                    <td>{{$application->id}}</td>
                    <td>
                        <h4>{{$application->name }}</h4>
                        <p class="text-muted">{{$application->notes}}</p>
                    </td>
                    <td>{{$application->user}}</td>
                    <td>{{date('M d, Y', strtotime($application->created_at))}}</td>
                    <td>{!! $application->status_label !!}</td>
                    <td>
                        <a href="{{route('admin.tasks.view',[$application->id])}}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> view </a>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="7">No records found</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        {!! $applications->render() !!}
    </div>
@stop

