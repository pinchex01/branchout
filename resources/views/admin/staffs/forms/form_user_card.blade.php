@if ($user)
    <div class="media well">
        <div class="media-left">
            <a href="#">
                <img class="media-object" src="{{property_exists($user,'avatar')? $user->avatar : asset('img/generic-avatar.png')}}" alt="" height="auto" width="50">
            </a>
        </div>
        <div class="media-body">
            <h4 class="media-heading">{{$user->full_name}}</h4>
            <strong>ID No.</strong>  {{$user->id_number}}<br>
            <input type="hidden" name="id_number" value="{{$user->id_number}}">
            @if ($user->registered)
                <strong>Email</strong>  {{$user->email}}<br>
                <strong>Phone No.</strong>  {{$user->phone}}<br>

                <input type="hidden" name="registered" value="1">
            @else
                <div class="form-group">
                    <label class="control-label">Email</label>
                    <input type="email" name="email" placeholder="Email Address" required class="form-control">
                </div>
                <div class="form-group {{$errors->has('phone.code') || $errors->has('phone.number') ? 'has-error': ''}}">
                    <label>Phone Number</label>
                    <input id="phone" type="tel" class="form-control phone" value="{{old('phone')}}" required>
                    {!! Form::input('hidden','phone',null,['class'=>'form-control full-phone','id'=>'full-phone','v-model'=>'phone.number']) !!}
                    {!! $errors->first('phone', '<span class="help-block">:message</span>') !!}
                </div>
                <input type="hidden" name="_user_sf_token" value="{{$token}}">
            @endif

            <input type="hidden" name="registered" value="{{$user->registered}}">
            <div class="form-group">
                {!! Form::label('roles', 'Roles', ['class' => ' control-label']) !!}
                <select name="role_ids[]" id="roles" class="form-control">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" > {{ $role->display_name }} </option>
                    @endforeach
                </select>


            </div>
            @if(!$user->registered)
                <hr>
            <div class="form-group">
                <label class="control-label">
                    <input type="checkbox" name="active" value="1">
                    Pre-active user account?
                </label>

            </div>
            @endif
        </div>
    </div>
@else
    <div class="note note-warning">
        <p>Invalid ID Number</p>
    </div>
@endif


<script>
    $(function(){
        $('input.phone').intlTelInput({
            nationalMode: true,
            initialCountry: "auto",
            geoIpLookup: function(callback) {
                $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "";
                    callback(countryCode);
                });
            },
            separateDialCode: true,
            utilsScript: "/assets/js/utils.js"
        });

        $('input.phone').keydown(function (e) {
            var group  = $(this).closest('.form-group');
            var input_field  = group.find('input.full-phone');
            input_field.val($(this).intlTelInput('getNumber'));
        });
    })
</script>

