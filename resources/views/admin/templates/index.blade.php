@extends('layouts.admin_settings')


@section('page')

<div class="panel with-nav-tabs panel-default">
  <div class="panel-heading panel-n-tab-heading hidden-xs">
    <h4 class="panel-title pull-left">Notification Templates</h4>
    <div class="pull-right">
        @permission('create-templates')
            <a href="{{route('admin.settings.templates.create')}}" class="btn btn-primary"> <i class="fa fa-plus"></i> Create New </a>
        @endpermission
    </div>
    <div class="clearfix"></div>
  </div>
                <div class="panel-heading">
                      @include('admin.settings._settings_menu')
              </div>
              <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
                <h4 class="panel-title pull-left">Notification Templates</h4>
                <div class="pull-right">
                    @permission('create-templates')
                        <a href="{{route('admin.settings.templates.create')}}" class="btn btn-primary"> <i class="fa fa-plus"></i> <span class="hidden-xs">Create New</span> </a>
                    @endpermission
                </div>
                <div class="clearfix"></div>
              </div>
              <div class="panel-body table-settings">
                <div class="table-responsive">
                  <table class="table table-striped table-hover table-advance dt-responsive dataTables">
                      <thead>
                      <tr>
                          <th></th>
                          <th>Name</th>
                          <th>Event</th>
                          <th>Date Created</th>
                          <th>Action(s)</th>
                      </tr>

                      </thead>
                      <tbody>

                      @foreach($templates as $template)
                          <tr>
                              <td>{{$template->id}}</td>
                              <td><a  href="{{route('admin.settings.templates.edit',[$template->id])}}">{{$template->name}}</a></td>
                              <td>{{ $template->event_name }}</td>
                              <td>{{$template->created_at->format('d, M Y')}}</td>
                              <td>
                                  <div class="btn-group">
                                      <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                          Select <span class="caret"></span>
                                      </button>
                                      <ul class="dropdown-menu dropdown-menu-right">
                                          <li>
                                              <a  href="{{route('admin.settings.templates.edit',[$template->id])}}">View</a>
                                          </li>
                                      </ul>
                                  </div>
                              </td>
                          </tr>
                      @endforeach
                      </tbody>
                  </table>
                  {!! $templates->render() !!}
                </div>


              </div>

              </div>





@endsection
