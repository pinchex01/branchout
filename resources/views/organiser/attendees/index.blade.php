@extends('layouts.organiser')

<?php $vue = true ?>
<?php $maps = true ?>
<?php $event_menu = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Attendees
            <div class="pull-right">
              <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#cs-filter"
                      aria-expanded="false" aria-controls="collapseExample">
                  <i class="fa fa-sliders"></i> Filter
              </button>
                <button class="btn btn-primary"> <i class="fa fa-user-plus"></i> Add Guest </button>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="collapse {{ request()->has('filters')? 'in': '' }}" id="cs-filter">
                <form class="well">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Ticket No</label>
                            <div class="col-sm-10">
                                {!! Form::text('filters[ticket_no]', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Ticket No',
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Order No</label>
                            <div class="col-sm-10">
                                {!! Form::text('filters[order_no]', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Order No',
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Ticket Type</label>
                            <div class="col-sm-10">
                                {!! Form::select('filters[ticket_type]',['' => "All"] + $ticket_types , null, [
                                'class' => 'form-control select2',
                                'label'=>"Ticket Type"
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer filter-actions">
                        <button type="submit" class="btn btn-danger">
                            Filter
                        </button>
                        <div class="btn-group">
                          <button type="submit" class="btn btn-warning" name="export" value="pdf"> Export </button>
                          <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a href="#">PDF</a></li>
                            <li><a href="#">Excel</a></li>
                          </ul>
                        </div>
                        <a href="{{route('organiser.orders.index', [$organiser->slug, $event->id])}}" class="btn btn-default">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-advance dt-responsive">
                    <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th>Order #</th>
                        <th>Name</th>
                        <th>Ticket</th>
                        <th>Check-in Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($attendees as $attendee)
                        <tr>
                            <td>{{ $attendee->ref_no }}</td>
                            <td>{{ $attendee->order->ref_no }}</td>
                            <td>{{$attendee->full_name}}</td>
                            <td>{{ $attendee->ticket }}</td>
                            <td>{{$attendee->check_in_date? : 'N/A'}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <p class="text-center">
                                    No attendees have been added yet. <br>
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {!! $attendees->render() !!}
            </div>
        </div>
    </div>
@endsection
