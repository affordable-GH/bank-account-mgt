@extends('layout.index')
@section('content')
    @use(Carbon\Carbon)
    <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary my-3 shadow-0 position-sticky"
        style="top: 60px;z-index: 50;">
        <div class="container-fluid d-flex justify-content-between">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item text-uppercase">
                        <a href="#">{{ $account_location->name }}</a>
                    </li>
                    <li class="breadcrumb-item text-uppercase">
                        <a href="{{ route('account.home', $account_location->id) }}">accounts</a>
                    </li>
                    <li class="breadcrumb-item text-uppercase active" aria-current="page">
                        <a href="#">create</a>
                    </li>
                </ol>
            </nav>
            {{-- <div class="d-flex">
                <a class="btn btn-info" href="#">add account</a>
            </div> --}}
        </div>
    </nav>
    @if ($account_types->isEmpty())
        <script>
            window.location.reload();
        </script>
    @endif
    <div class="card shadow-1-soft">
        <div class="card-body">
            <h5 class="card-title text-capitalize mb-4">create bank account</h5>
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="list-unstyled mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form id="create-account-form" action="{{ route('account.store', $account_location->id) }}" method="POST">
                <div class="row mb-3">
                    <label for="account_number" class="col-sm-2 col-form-label text-uppercase">account number</label>
                    <div class="col-sm-10">
                        <input autofocus required type="number" value="{{ @old('account_number') }}"
                            class="form-control @error('account_number') is-invalid @enderror" id="account_number"
                            name="account_number" />
                        @error('account_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="bank_namme" class="col-sm-2 col-form-label text-uppercase">bank name</label>
                    <div class="col-sm-10">
                        <input required type="bank_namme" class="form-control @error('bank_namme') is-invalid @enderror"
                            id="bank_namme" name="bank_name" value="{{ @old('bank_name') }}" />
                        @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                {{-- <div class="row mb-3">
                    <label for="name" class="col-sm-2 col-form-label text-uppercase">account name</label>
                    <div class="col-sm-10">
                        <input required type="name" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ @old('name') }}" />
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div> --}}
                <div class="row mb-3">
                    <label for="account_type" class="col-sm-2 col-form-label text-uppercase">account type</label>
                    <div class="col-auto">
                        <select required name="account_type" class="form-select @error('account_type') is-invalid @enderror"
                            id="account_type">
                            @forelse ($account_types as $account_type)
                                <option value="{{ $account_type->id }}">{{ $account_type->type }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
                {{-- <div class="row mb-3">
                    <label for="account_address" class="col-form-label text-uppercase col-sm-2 pt-0">account
                        address</label>
                    <div class="col-sm-10">
                        <input required type="account_address"
                            class="form-control @error('account_address') is-invalid @enderror" id="account_address"
                            name="account_address" value="{{ @old('account_address') }}" />
                        @error('account_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div> --}}
                <div class="row mb-3">
                    <label for="initial_amount" class="col-form-label text-uppercase col-sm-2 pt-0">initial
                        amount</label>
                    <div class="col-sm-10">
                        <input required type="text"  value="0" onfocus="this.select()" step="0.01"
                            class="currencyInput form-control @error('initial_amount') is-invalid @enderror"
                            id="initial_amount" name="initial_amount" />
                        @error('initial_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <fieldset class="row mb-3">
                    <label for="account_status" class="col-form-label text-uppercase col-sm-2 pt-0">status</label>
                    <div class="col-auto">
                        <div class="mb-3">
                            <select required class="form-select @error('account_status') is-invalid @enderror"
                                name="account_status" id="account_status">
                                @forelse ($account_statuses as $account_status)
                                    <option value="{{ $account_status->id }}">{{ $account_status->status }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                    </div>
                </fieldset>
                <fieldset class="row mb-3">
                    <label for="created_at" class="col-form-label text-uppercase col-sm-2 pt-0">date</label>
                    <div class="col-sm-10">
                        <input required type="date" value="{{ now()->format('Y-m-d') }}"
                            class="form-control @error('created_at') is-invalid @enderror" name="created_at" id="created_at"
                            placeholder="" />
                        @error('created_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </fieldset>
                <div class="row mb-3">
                    <label for="account_description" class="col-form-label text-uppercase col-sm-2 pt-0">notes and
                        attachments</label>
                    <div class="col-sm-10">
                        <textarea name="account_description" class="form-control @error('account_description') is-invalid @enderror"
                            id="account_description" rows="3"></textarea>
                        @error('account_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @csrf
                <div class="d-flex justify-content-end mb-3">
                    <input type="submit" name="exist" class="btn btn-info me-2" value="save & exit"></input>
                    <button type="submit" class="btn btn-primary">save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        const form = document.getElementById('create-account-form');
        form && form.addEventListener('submit', function(e) {
            e.submitter.classList.add('disabled');
            $('.loader-overlay').show().find('.loader-text').text('Updating...')
            return 1;
        });
    </script>
@endsection
