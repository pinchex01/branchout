<div class="panel panel-bank-details">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <p><strong>Account Name: </strong> {{$bank->name}}</p>
                <p><strong>Account No: </strong> {{$bank->account_no}}</p>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <p><strong>Bank: </strong> {{$bank->bank }}</p>
                <p><strong>Currency: </strong> {{$bank->currency}}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-4">

        <div class="card bd-success">
            <div class="card-icon bg-success text-center">
                <i class="fa fa-money"></i>
            </div>

            <div class="card-block">
                <div class="h5">KES. {{ number_format($summary->credit,2) }}</div>
                <div class="text-muted text-uppercase font-weight-bold font-xs">Total Credit</div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4">

        <div class="card bd-success">
            <div class="card-icon bg-danger text-center">
                <i class="fa fa-users "></i>
            </div>

            <div class="card-block">

                <div class="h5">{{ number_format($summary->debit,2) }}</div>
                <div class="text-muted text-uppercase font-weight-bold font-xs">Total Debit</div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4">
        <div class="card bd-success">
            <div class="card-icon bg-primary text-center">
                <i class="fa fa-money"></i>
            </div>

            <div class="card-block">
                <div class="h5">KES. {{ number_format($summary->balance,2) }}</div>
                <div class="text-muted text-uppercase font-weight-bold font-xs">Available Balance</div>
            </div>
        </div>
    </div>
</div>
