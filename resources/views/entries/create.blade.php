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
                        <a title="go to entries" href="{{ route('entries.index', $account_location) }}">entries</a>
                    </li>
                    <li class="breadcrumb-item text-uppercase active" aria-current="page">
                        <a href="#">create</a>
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
            <h5 class="card-title text-capitalize mb-5 fw-bold">create new entry</h5>
            <form id="create-entry" action="{{ route('entries.store', $account_location->id) }}" method="POST">
                <fieldset class="row mb-3">
                    <label for="account" class="col-form-label text-uppercase col-md-2 pt-0">account</label>
                    <div class="col-md-10">
                        <select required class="form-select select2 @error('account') is-invalid @enderror" name="account"
                            id="account">
                            @forelse ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->bank_name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </fieldset>
                <div class="row mb-3">
                    <label for="entry_type" class="col-md-2 col-form-label text-uppercase">entry type</label>
                    <div class="col-md-10">
                        <select required name="entry_type" class="form-select @error('entry_type') is-invalid @enderror"
                            id="entry_type">
                            @forelse ($entry_types as $entry_type)
                                <option value="{{ $entry_type->id }}">{{ $entry_type->type }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="amount" class="col-md-2 col-form-label text-uppercase">amount</label>
                    <div class="col-md-10">
                        <input required type="text" step="0.01" onfocus="this.select()"
                            class="currencyInput form-control @error('amount') is-invalid @enderror" id="amount"
                            name="amount" value="{{ @old('amount') ?? '0.00' }}" />
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="description" class="col-md-2 col-form-label text-uppercase">description</label>
                    <div class="col-md-10">
                        <textarea required name="description" class="form-control @error('description') is-invalid @enderror" id="description"
                            rows="3"></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="reference_number" class="col-form-label text-uppercase col-md-2 pt-0">reference
                        number</label>
                    <div class="col-md-10">
                        <div class="mb-3">
                            <input type="number" value="{{ now()->format('Ymdhis') }}"
                                class="form-control @error('reference_number') is-invalid @enderror" name="reference_number"
                                id="reference_number" />
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <fieldset class="row mb-3">
                    <label for="date" class="col-form-label text-uppercase col-md-2 pt-0">payment date</label>
                    <div class="col-md-10">
                        <input required type="date" value="{{ now()->format('Y-m-d') }}"
                            class="form-control @error('date') is-invalid @enderror" name="date" id="date"
                            placeholder="" />
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </fieldset>
                <fieldset class="row mb-3">
                    <label for="value_date" class="col-form-label text-uppercase col-md-2 pt-0">value date</label>
                    <div class="col-md-10">
                        <input required type="date" value="{{ now()->format('Y-m-d') }}" onchange="validateDates()"
                            class="form-control @error('value_date') is-invalid @enderror" name="value_date" id="value_date"
                            placeholder="" />
                        @error('value_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </fieldset>
                @csrf
                <div class="d-flex justify-content-end mb-3">
                    <input value="save & exit" type="submit" name="exist" class="btn btn-info me-2"></input>
                    <button type="submit" class="btn btn-primary">save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function validateDates() {
            var endDate = document.getElementById('date').value;
            document.getElementById('value-date').max = endDate;
        }
        const form = document.getElementById('create-entry');
        form && form.addEventListener('submit', function(e) {
            e.submitter.classList.add('disabled');
            $('.loader-overlay').show().find('.loader-text').text('Processing...')
            return 1;
        });
        $('textarea[name="description"]').val('{{ @old('description') ?? 'payment' }}')
    </script>
@endsection
