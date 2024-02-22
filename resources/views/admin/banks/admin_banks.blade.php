@extends('layouts.admin')


@section('page')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">

            <div class="card bd-danger">
                <div class="card-icon bg-danger text-center">
                    <i class="fa fa-calendar-plus-o"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{number_format($collection_summary->get('month'),2)}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">This Month's Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="card bd-primary">
                <div class="card-icon bg-primary text-center">
                    <i class="fa fa-pie-chart"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{number_format($collection_summary->get('year'),2)}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">This Year's Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="card bd-warning">
                <div class="card-icon bg-warning text-center">
                    <i class="fa fa-line-chart"></i>
                </div>

                <div class="card-block">
                    <div class="h5">{{number_format($collection_summary->get('alltime'),2)}}</div>
                    <div class="text-muted text-uppercase font-weight-bold font-xs">All-Time Revenue</div>
                </div>
            </div>
        </div>
    </div>

<div class="panel with-nav-tabs panel-default">
  <div class="panel-heading panel-n-tab-heading hidden-xs">
    <div class="panel-title pull-left">
        <span class="caption-subject bold uppercase"> Bank Accounts </span>
    </div>
    <div class="pull-right">
        @permission('create-banks')
        <a class="btn btn-primary" href="{{ route('admin.banks.new') }}"><i class="fa fa-plus"></i> <span class="hidden-xs">New</span></a>
        @endpermission
    </div>
    <div class="clearfix"></div>
  </div>

              <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
                    <div class="panel-title pull-left">
                        <span class="caption-subject bold uppercase"> Bank Accounts </span>
                    </div>
                    <div class="pull-right">
                        @permission('create-banks')
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modal_bank_add"><i class="fa fa-plus"></i> <span class="hidden-xs">New</span></button>
                        @push('modals')
                        <form class="modal fade" id="modal_bank_add" action="{{route('admin.banks.index')}}"  method="post" role="basic" aria-hidden="true">
                            {!! csrf_field() !!}
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Add Bank Account</h4>
                                    </div>
                                    <div class="modal-body">
                                        @include('forms.merchant.bank_form')
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endpush
                        @endpermission
                    </div>
                    <div class="clearfix"></div>
              </div>
              <div class="panel-body table-settings">
                <div class="table-responsive">
                  <table class="table table-striped table-hover dt-responsive" width="100%">
                      <thead>
                      <tr>
                          <th>Account Name</th>
                          <th>Account No</th>
                          <th>Bank</th>
                          <th>Collected</th>
                          <th>Balance</th>
                          <th>Status</th>
                          <th>Action</th>
                      </tr>
                      </thead>
                      <tbody>
                      @foreach($banks as $bank)
                          <tr>
                              <td> <a href="{{route('admin.banks.view',[$bank->id])}}">
                                      {{$bank->name}}</a>
                                  @if ($bank->is_default)<span class="label label-success">Default</span> @endif
                              </td>
                              <td>{{ $bank->account_type =='bank'?$bank->masked_account_no: $bank->account_no}}</td>
                              <td>{{$bank->bank ? : 'Paybill'}}</td>
                              <td>{{ number_format($bank->account->credit) }}</td>
                              <td>{{ number_format($bank->account->balance) }}</td>
                              <td>{!! $bank->status_label !!}</td>
                              <td>
                                  <div class="btn-group">
                                      <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                          Action <span class="caret"></span>
                                      </button>
                                      <ul class="dropdown-menu dropdown-menu-right">
                                          @permission(['update-banks'])
                                          @if(!$bank->is_default)
                                              <li>
                                                  <a href="#sc_{{$bank->id}}" data-toggle="modal"> Make Default</a>
                                              </li>

                                              @push('modals')
                                              <div class="modal fade" id="sc_{{$bank->id}}" tabindex="-1" role="dialog">
                                                  <form class="modal-dialog form-otp" role="document" method="post" action="{{route('admin.banks.default',[$bank->id])}}">
                                                      {!! csrf_field() !!}
                                                      <div class="modal-content">
                                                          <div class="modal-header">
                                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                              <h4 class="modal-title">Change Default Bank</h4>
                                                          </div>
                                                          <div class="modal-body">
                                                              <p>Are you sure you want to set this bank account as the default?</p>
                                                          </div>
                                                          <div class="modal-footer">
                                                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                              <button type="submit" class="btn btn-primary">Yes</button>
                                                          </div>
                                                      </div><!-- /.modal-content -->
                                                  </form><!-- /.modal-dialog -->
                                              </div><!-- /.modal -->
                                              @endpush
                                          @endif
                                          @endpermission
                                      </ul>
                                  </div>
                              </td>
                          </tr>
                      @endforeach
                      </tbody>
                  </table>
                  {!! $banks->render() !!}

                </div>


              </div>

              </div>
@stop
