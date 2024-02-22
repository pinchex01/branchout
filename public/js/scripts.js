var $pageLoader = $('#page-loader');



$('.link-confirm').click(function (event) {
    event.preventDefault();
    var r = confirm($(this).data('caption'));
    if (r == true) {
        window.location = $(this).attr('href');
    }

});

$('.form-confirm').submit(function (event) {
    if (!confirm($(this).data('caption'))) {
        event.preventDefault();
    }
});


if ($().editable) {
    $('.xeditable').editable();
    $('.editable-field').editable();
}

if ($().select2) {
    $('.select2').select2({
        placeholder: "Select",
        allowClear: true,
        width: '100%',
        height: '34px'
    });
}


$('.rfilter').click(function () {
    var link = $(this).data('url');
    var name = $(this).data('name');
    var source = $(this).data('source');
    var val = $(source).val();
    window.location.href = link + "?" + name + "=" + val;
});


$('#cmd_edit').click(function (e) {
    e.preventDefault();
    $('.disable').prop('disabled', false);
    $(this).addClass('hide');
    $('#cmd_save').removeClass('hide');
});


if ($().datetimepicker) {
    $('.datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD'
    });
}

/*
 Initialize daterange picker
 */
if ($().daterangepicker) {
    $('.daterange').daterangepicker({
        locale: {
            format: 'YYYY/MM/DD'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
}

var DrawMap = function(elem_id) {
    var elem =  $(elem_id);
    var lat =  elem.data('lat');
    var lng =  elem.data('lon');

    map = new GMaps({
        div: elem.attr('id'),
        lat: lat,
        lng: lng,
        idle: function(e){
            if ( $( ".place-card" ).length === 0 ) {
                var card = $(elem.data('card'));
                if (card.length > 0){
                    $(".gm-style").append(card.html());
                }
            }
        },
        mapTypeControl: false,
        zoomControl: true,
        zoomControlOptions: {
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        },
    });

    map.addMarker({
        lat: lat,
        lng: lng,
        infoWindow: {
            content: '<p> '+ elem.data('title') +'</p>'
        },
        title: 'Courtyard by Marriott',
    });
};

$(function () {
    $('.btn-pay-invoice').click(function(e){
        e.preventDefault();
        var form_id  = $(this).data('id');
        var form  = $('#frm-invoice-'+ form_id);

        if (form.length > 0){
            form.submit();
        }

        return;
    });

    $('.multiselect').multiSelect();

    $('.btn-print').click(function (e) {
        e.preventDefault();
        var printArea  = $(this).data('source');
        $(printArea).print();
    })
});

$('.modal-map').on('shown.bs.modal', function (e) {
    alert('modal shown');
    $('#map-component').locationpicker('autosize');
});

$('.modal-credit-card').on('shown.bs.modal', function (e) {
    var elem_id  = '#' +$(this).attr('id');
    var wrapper  = elem_id + ' .card-wrapper';
    var card = new Card({
        // a selector or DOM element for the form where users will
        // be entering their information
        form:  elem_id, // *required*
        // a selector or DOM element for the container
        // where you want the card to appear
        container: wrapper, // *required*

        formSelectors: {
            numberInput: elem_id+'  input[name="card[number]"]', // optional — default input[name="number"]
            expiryInput: elem_id+' input[name="card[expiry]"]', // optional — default input[name="expiry"]
            cvcInput: elem_id+' input[name="card[cvc]"]', // optional — default input[name="cvc"]
            nameInput: elem_id+' input[name="card[name]"]'
        },

        width: '100%', // optional — default 350px
        formatting: true, // optional - default true

        // Strings for translation - optional
        messages: {
            validDate: 'valid\ndate', // optional - default 'valid\nthru'
            monthYear: 'mm/yyyy', // optional - default 'month/year'
        },

        // Default placeholders for rendered fields - optional
        placeholders: {
            number: '•••• •••• •••• ••••',
            expiry: '••/••',
            cvc: '•••',
            name: 'Full Name',
        },

        // if true, will log helpful messages for setting up Card
        debug: false // optional - default false
    });
});

$('.uss-btn').click(function (e) {
    var $form  = $(this).closest('form');
    var query_url  = $(this).data('url');
    var id_number = $form.find('.input-id-number').val();
    var first_name = $form.find('.input-user-first-name').val();
    var container  = $form.find('.user-service-search');
    var search_loader = container.find('.search-loader');
    var card_holder = container.find('.user-card-holder');
    var submit_btn  = $form.find('.submit-btn');
    var $loading = $('#page-loader');

    if (id_number.length < 1){
        alert("Please enter a valid ID Number")
        return;
    }


    if (first_name.length < 1){
        alert("Please enter a valid First Name")
        return;
    }

    var house_id = $('#txt_house_id').val();
    if (!house_id)
        house_id = 0;

    $loading.fadeIn();

    $.get(query_url, {number: id_number, first_name: first_name, ref: house_id}, function (result) {
        if (result.status == 'fail') {
            card_holder.html('<div class="note note-danger">' + result.message + '</div>');
            submit_btn.addClass('hide');
            $loading.fadeOut();
            return;
        }
        card_holder.html(result);
        $loading.fadeOut();
        submit_btn.removeClass('hide');

    });
});

var getPhoneNumber = function(elem) {
    var p_code = $(elem).find('#phone_code').val();
    var p_number  = $(elem).find('#phone_number').val();

    if (p_code.length === 0 ){
        alert("Please enter calling code")
        p_code = false;
    }

    if (p_number.length === 0 ){
        alert("Please enter phone number")
        p_number = false;
    }

    return {code: p_code,number: p_number};
}

window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove();
    });
}, 7000);

$('.btn-bfs').click(function (e) {
    e.preventDefault();
    var dt  = null;
    var container  = $(this).closest('.modal-bank-search');
    var bank = container.find('#bsf_name option:selected').text();
    var branch  = container.find('#bsf_branch').val();
    var table = container.find('table.table-bfs');

    var data = { filters: {name: bank, branch: branch}};
    $pageLoader.fadeIn()

    $.ajax({
        type: "post",
        url: '/api/search-banks',
        data: data,
        success: function (res) {
            if (res.status  == 'ok'){
                var items = res.data;
                var html = "";
                if (items.length){ //branches with matching filter found
                    html =  $.map(items,function(item){
                        return '<tr><td>' +item.name+'</td><td>' +item.branch+'</td><td>' +item.code+'</td></tr>';
                    })
                } else { //no bank branches foudn
                    html = '<tr><td colspan="3"> No records found</td>';
                }

                $pageLoader.fadeOut();
                //table.find('tbody').html(html);

                //do not reinitialize the table
                if ( $.fn.DataTable.isDataTable( '#'+table.attr('id') ) ) {
                    table.DataTable().destroy();
                }
                dt  = table.DataTable({
                    data: items,
                    "columns": [
                        { "data": "name" },
                        { "data": "branch" },
                        { "data": "code" },
                    ],
                    "columnDefs": [ {
                        "targets": 2,
                        "data": data,
                        "render": function ( value, type, full, meta ) {
                            return '<a href="#" onclick="bankSelected('+ full.id +',\''+full.name+'\',\''+full.branch+'\')">Select</a>';
                        }
                    } ],
                    "bLengthChange": false
                });

            }else{
                alert("error occurred");
            }
        }
    });


});

var bankSelected = function(ref,name,branch) {
    $('#bank_id').val(ref);
    $('#bank_name').val(name);
    $('#bank_branch').val(branch);
    $('.modal-bank-search').modal('hide');
};

function getUserFromMeta() {
    return $('meta[name="client-x"]').attr('content');
}

$('.form-otpp').submit(function(e){
    var $loader = $('#page-loader');
    $loader.fadeIn();
    var $form  = this;
    var fingerprint = null;
    var status = false;
    //first create otp
    $.ajax({
        type: "POST",
        url: '/api/create-otp',
        data: { token: getUserFromMeta()},
        success: function (response) {

            if (response.status == "ok") {
                fingerprint = response.data.fingerprint;
                $loader.fadeOut();
                swal({
                        title: "One-Time-Password",
                        text: "Enter the code sent to your phone",
                        type: "input",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        animation: "slide-from-top",
                        inputPlaceholder: "One-Time-Password",
                        confirmButtonText: "Confirm"
                    },
                    function(inputValue){
                        if (inputValue === false) return false;

                        if (inputValue === "") {
                            swal.showInputError("You need to write something!");
                            return false
                        }

                        $loader.fadeIn();
                        $.ajax({
                            type: 'POST',
                            url: '/api/verify-otp',
                            data: { fingerprint: fingerprint, otp: inputValue},
                            success: function (res) {

                                if (res.status == 'ok'){
                                    status  = true;
                                    $loader.fadeOut();
                                    $($form).unbind('submit').submit();
                                }else{
                                    swal.showInputError("Invalid code!");
                                    return false
                                }
                            }
                        });
                    }
                );
            } else {
                alert(response.message);
            }
        }
    });



    e.preventDefault();
});

function range(start, end, step) {
    var range = [];
    var typeofStart = typeof start;
    var typeofEnd = typeof end;

    if (step === 0) {
        throw TypeError("Step cannot be zero.");
    }

    if (typeofStart == "undefined" || typeofEnd == "undefined") {
        throw TypeError("Must pass start and end arguments.");
    } else if (typeofStart != typeofEnd) {
        throw TypeError("Start and end arguments must be of same type.");
    }

    typeof step == "undefined" && (step = 1);

    if (end < start) {
        step = -step;
    }

    if (typeofStart == "number") {

        while (step > 0 ? end >= start : end <= start) {
            range.push(start);
            start += step;
        }

    } else if (typeofStart == "string") {

        if (start.length != 1 || end.length != 1) {
            throw TypeError("Only strings with one character are supported.");
        }

        start = start.charCodeAt(0);
        end = end.charCodeAt(0);

        while (step > 0 ? end >= start : end <= start) {
            range.push(String.fromCharCode(start));
            start += step;
        }

    } else {
        throw TypeError("Only string and number types are supported");
    }

    return range;

}


$('input.phone').intlTelInput({
    nationalMode: true,
    initialCountry: "auto",
    geoIpLookup: function(callback) {
        $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
            var countryCode = (resp && resp.country) ? resp.country : "";
            callback(countryCode);
        });
    },
    separateDialCode: false,
    utilsScript: "/assets/js/utils.js"
});

$('input.phone').keydown(function (e) {
    var group  = $(this).closest('.form-group');
    var input_field  = group.find('input.full-phone');
    input_field.val($(this).intlTelInput('getNumber'));
});

$('form').submit(function () {
    $('#full-phone').val($('#phone').intlTelInput('getNumber'));
})

//helper function to sort array elements by name
function SortByName(a, b){
    var aName = a.name.toLowerCase();
    var bName = b.name.toLowerCase();
    return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
}


function add_ajax_crsf() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
}

//button loading state
$('.btn-loading').click(function (e) {
    var $btn = $(this);
    $btn.button('loading');
})
/**
 * Get data from array of objects
 * @param ar
 * @param key
 * @returns {Array}
 */
function get_data (ar, key) {
    data = [];
    ar.forEach(function (i) {
        data.push(i[key])
    })
    return data;
}


