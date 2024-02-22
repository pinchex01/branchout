@if(in_array($application->status,['pending','reviewed']))
 @if($application->status =='pending' && user()->can('review-applications') )
     @if(!$application->picked)

     @else
         <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#app_corrections_{{$application->id}}" aria-expanded="false" aria-controls="collapseExample">
             <i class="fa fa-ban"></i> Decline
         </button>
         <button class="btn btn-success" type="button" data-toggle="modal" data-target="#app_review_{{$application->id}}" aria-expanded="false" aria-controls="collapseExample">
             <i class="fa fa-check"></i> Approve
         </button>

         @push('modals')
         <div class="modal fade" id="app_corrections_{{$application->id}}" tabindex="-1"
              role="dialog">
             <form class="modal-dialog" role="document" method="post"
                   action="{{route('admin.tasks.reject',[$application->id])}}">
                 {!! csrf_field() !!}
                 <div class="modal-content">
                     <div class="modal-header">
                         <button type="button" class="close"
                                 data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">&times;</span></button>
                         <h4 class="modal-title">Decline</h4>
                     </div>
                     <div class="modal-body">
                         <div class="form-group">
                             <label>Reason for declining</label>
                             <textarea name="comment" class="form-control" required rows="4" cols="10"></textarea>
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button class="btn btn-default" type="button" data-dismiss="modal">Cancel</button>
                         <button type="submit" class="btn btn-primary" name="status" value="corrections">
                             Yes
                         </button>
                     </div>
                 </div><!-- /.modal-content -->
             </form><!-- /.modal-dialog -->
         </div><!-- /.modal -->

         <div class="modal fade" id="app_review_{{$application->id}}" tabindex="-1"
              role="dialog">
             <form class="modal-dialog" role="document" method="post"
                   action="{{route('admin.tasks.review',[$application->id])}}">
                 {!! csrf_field() !!}
                 <div class="modal-content">
                     <div class="modal-header">
                         <button type="button" class="close"
                                 data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">&times;</span></button>
                         <h4 class="modal-title">Approve</h4>
                     </div>
                     <div class="modal-body">
                         <p>Are you sure?</p>
                     </div>
                     <div class="modal-footer">
                         <button class="btn btn-default" type="button" data-dismiss="modal">Cancel</button>
                         <button type="submit" class="btn btn-primary" name="status" value="reviewed">
                             Accept
                         </button>
                     </div>
                 </div><!-- /.modal-content -->
             </form><!-- /.modal-dialog -->
         </div><!-- /.modal -->
         @endpush
     @endif
 @elseif($application->status == 'reviewed' && user()->can('approve-applications'))

     @if(!$application->picked)
         
     @else

         <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#app_rejection_{{$application->id}}" aria-expanded="false" aria-controls="collapseExample">
             <i class="fa fa-ban"></i> Reject
         </button>
         <button class="btn btn-success" type="button" data-toggle="modal" data-target="#app_approval_{{$application->id}}" aria-expanded="false" aria-controls="collapseExample">
             <i class="fa fa-check"></i> Approve
         </button>

         @push('modals')
         <div class="modal fade" id="app_rejection_{{$application->id}}" tabindex="-1"
              role="dialog">
             <form class="modal-dialog" role="document" method="post"
                   action="{{route('admin.tasks.reject',[$application->id])}}">
                 {!! csrf_field() !!}
                 <div class="modal-content">
                     <div class="modal-header">
                         <button type="button" class="close"
                                 data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">&times;</span></button>
                         <h4 class="modal-title">Reject</h4>
                     </div>
                     <div class="modal-body">
                         <div class="form-group">
                             <label>Reason for declining</label>
                             <textarea name="comment" required ></textarea>
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button class="btn btn-default" type="button" data-dismiss="modal">Cancel</button>
                         <button type="submit" class="btn btn-primary" name="status" value="rejected">
                             Yes
                         </button>
                     </div>
                 </div><!-- /.modal-content -->
             </form><!-- /.modal-dialog -->
         </div><!-- /.modal -->

         <div class="modal fade" id="app_approval_{{$application->id}}" tabindex="-1"
              role="dialog">
             <form class="modal-dialog" role="document" method="post"
                   action="{{route('admin.tasks.approve',[$application->id])}}">
                 {!! csrf_field() !!}
                 <div class="modal-content">
                     <div class="modal-header">
                         <button type="button" class="close"
                                 data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">&times;</span></button>
                         <h4 class="modal-title">Approve</h4>
                     </div>
                     <div class="modal-body">
                         <p>Are you sure you?</p>
                     </div>
                     <div class="modal-footer">
                         <button class="btn btn-default" type="button" data-dismiss="modal">Cancel</button>
                         <button type="submit" class="btn btn-primary" name="status" value="approved">
                             Yes
                         </button>
                     </div>
                 </div><!-- /.modal-content -->
             </form><!-- /.modal-dialog -->
         </div><!-- /.modal -->
         @endpush
     @endif
 @endif

@endif

