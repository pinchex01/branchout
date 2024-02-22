@extends('admin.applications.layouts.tasks')

@section('task_title',"Tasks Inbox")
@push('application_actions')
<button class="btn btn-default" type="button" data-toggle="collapse" data-target="#cs-filter" aria-expanded="false" aria-controls="collapseExample">
    <i class="fa fa-sliders"></i> Filter
</button>
@endpush

@section('applications')
    <div class="collapse {{ request()->has('filters')? 'in': '' }}" id="cs-filter">
        <form class="well">
            <div class="form-horizontal">
                <<div class="form-group">
                    <label class="col-sm-2 control-label">Type</label>
                    <div class="col-sm-10">
                        {!! Form::select('filters[type]',['' => "All"] + \App\Models\Application::$types , null, [
                        'class' => 'form-control',
                        'label'=>"Status"
                        ]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-10">
                        {!! Form::select('filters[status]',[
                        '' => "All"] + \App\Models\Application::$statuses , null, [
                        'class' => 'form-control',
                        'label'=>"Status"
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer filter-actions">
                <button type="submit" class="btn btn-danger ">
                    Filter
                </button>
                <a href="{{route('admin.tasks.index')}}" class="btn btn-default">
                    <i class="fa fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>
    <table class="table table-striped  table-hover dt-responsive">
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
                    <a href="{{ route('admin.tasks.view',[$application->id]) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> view </a>
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
@stop

