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
                        <a href="#">accounts</a>
                    </li>
                    <li class="breadcrumb-item text-uppercase">
                        <a title="go to entries" href="{{ route('transfers.create', $account_location) }}">transfers</a>
                    </li>
                    <li class="breadcrumb-item text-uppercase active" aria-current="page">
                        <a href="#">edit</a>
                    </li>
                </ol>
            </nav>
            {{-- <div class="d-flex">
                <a class="btn btn-info" href="{{ route('entries.create', $account_location->name) }}">add entry</a>
            </div> --}}
        </div>
    </nav>
    <div class="card shadow-1-soft">
        <div class="card-body">
            <h5 class="card-title text-capitalize mb-5 fw-bold">edit transfer</h5>
            @session('error')
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endsession
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="list-unstyled mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form id="create-entry" action="{{ route('transfers.update', [$account_location->id, $transfer->id]) }}"
                method="POST">
                <fieldset class="row mb-3">
                    <label for="from_account" class="col-form-label text-uppercase col-sm-2 pt-0">from account</label>
                    <div class="col-sm-10">
                        <select required class="form-select select2 @error('from_account') is-invalid @enderror"
                            name="from_account" id="from_account">
                            @forelse ($accounts as $account)
                                <option {{ $transfer->from_account_id === $account->id ? 'selected' : '' }}
                                    value="{{ $account->id }}">{{ $account->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </fieldset>
                <fieldset class="row mb-3">
                    <label for="to_account" class="col-form-label text-uppercase col-sm-2 pt-0">to account</label>
                    <div class="col-sm-10">
                        <select required class="form-select select2 @error('to_account') is-invalid @enderror"
                            name="to_account" id="to_account">
                            @forelse ($accounts as $account)
                                <option {{ $transfer->to_account_id === $account->id ? 'selected' : '' }}
                                    value="{{ $account->id }}">{{ $account->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </fieldset>
                <div class="row mb-3">
                    <label for="amount" class="col-sm-2 col-form-label text-uppercase">amount</label>
                    <div class="col-sm-10">
                        <input required type="text" step="0.01" onfocus="this.select()"
                            class="currencyInput form-control @error('amount') is-invalid @enderror" id="amount"
                            name="amount" value="{{ $transfer->amount }}" />
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="notes" class="col-sm-2 col-form-label text-uppercase">description</label>
                    <div class="col-sm-10">
                        <textarea required name="notes" class="form-control @error('notes') is-invalid @enderror" id="notes"
                            rows="3"></textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <fieldset class="row mb-3">
                    <label for="date" class="col-form-label text-uppercase col-sm-2 pt-0">value date</label>
                    <div class="col-sm-10">
                        <input required type="date"
                            value="{{ Carbon::parse($transfer->created_at)->format('Y-m-d') ?? now()->format('Y-m-d') }}"
                            class="form-control @error('date') is-invalid @enderror" name="date" id="date"
                            placeholder="" />
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </fieldset>
                @method('PUT')
                @csrf
                <div class="d-flex justify-content-end mb-3">
                    <button type="submit" class="btn btn-primary">update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('textarea#notes').val('{{ $transfer->notes }}');
        const form = document.getElementById('create-entry');
        form && form.addEventListener('submit', function(e) {
            e.submitter.classList.add('disabled');
            $('.loader-overlay').show().find('.loader-text').text('Processing...')
            return 1;
        });
    </script>
@endsection
