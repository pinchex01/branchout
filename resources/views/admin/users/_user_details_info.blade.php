<div class="row">
    <div class="col-md-12">
        <div class="span3 well">
            <div align="center">
                <a href="#aboutModal" data-toggle="modal" data-target="#myModal"><img src="{{ $user->getAvatar() }}" name="aboutme" width="140" height="140" class="img-circle"></a>
                <h3>{{$user->full_name}}</h3>
                <span>
                    <i class="fa fa-registered"></i> {{$user->id_number}} &nbsp;&nbsp;
                    <i class="fa fa-calendar"></i> {{$user->dob}} &nbsp;&nbsp;
                    <i class="fa fa-phone"></i> {{$user->phone}} &nbsp;&nbsp;
                    <i class="fa fa-at"></i> {{$user->email}} &nbsp;&nbsp;
                </span>
            </div>
        </div>
    </div>
</div>