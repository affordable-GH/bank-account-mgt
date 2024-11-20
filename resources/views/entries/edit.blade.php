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
            <h5 class="card-title text-capitalize mb-5 fw-bold">edit entry</h5>
            <form id="edit-entry" action="{{ route('entries.update', [$account_location->id, $entry->id]) }}"
                method="POST">
                <fieldset class="row mb-3">
                    <label for="account" class="col-form-label text-uppercase col-sm-2 pt-0">account</label>
                    <div class="col-sm-10">
                        <select required class="form-select select2 @error('account') is-invalid @enderror" name="account"
                            id="account">
                            <option selected value="{{ $entry->account->id }}">{{ $entry->account->bank_name }}</option>
                            {{-- @forelse ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->bank_name }}</option>
                            @empty
                            @endforelse --}}
                        </select>
                    </div>
                </fieldset>
                <div class="row mb-3">
                    <label for="entry_type" class="col-sm-2 col-form-label text-uppercase">entry type</label>
                    <div class="col-sm-10">
                        <select required name="entry_type" class="form-select @error('entry_type') is-invalid @enderror"
                            id="entry_type">
                            @forelse ($entry_types as $entry_type)
                                <option {{ $entry_type->id === $entry->entry_type_id ? 'selected' : '' }}
                                    value="{{ $entry_type->id }}">{{ $entry_type->type }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="amount" class="col-sm-2 col-form-label text-uppercase">amount</label>
                    <div class="col-sm-10">
                        <input {{ $entry->is_reconciled === true ? 'readonly disabled' : '' }} required type="text"
                            step="0.01" class="currencyInput form-control @error('amount') is-invalid @enderror"
                            id="amount" name="amount" value="{{ $entry->amount }}" />
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="description" class="col-sm-2 col-form-label text-uppercase">description</label>
                    <div class="col-sm-10">
                        <textarea required name="description" class="form-control @error('description') is-invalid @enderror" id="description"
                            rows="3"></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="reference_number" class="col-form-label text-uppercase col-sm-2 pt-0">reference
                        number</label>
                    <div class="col-sm-10">
                        <div class="mb-3">
                            <input {{ $entry->is_reconciled === true ? 'readonly disabled' : '' }} type="number"
                                value="{{ $entry->reference_number ?? now()->format('Ymdhis') }}"
                                class="form-control @error('reference_number') is-invalid @enderror" name="reference_number"
                                id="reference_number" />
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <fieldset class="row mb-3">
                    <label for="date" class="col-form-label text-uppercase col-sm-2 pt-0">payment date</label>
                    <div class="col-sm-10">
                        <input required type="date" value="{{ Carbon::parse($entry->date)->format('Y-m-d') }}"
                            class="form-control @error('date') is-invalid @enderror" name="date" id="date"
                            placeholder="" />
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </fieldset>
                <fieldset class="row mb-3">
                    <label for="value-date" class="col-form-label text-uppercase col-sm-2 pt-0">value date</label>
                    <div class="col-sm-10">
                        <input required type="date" onchange="validateDates()"
                            value="{{ Carbon::parse($entry->value_date)->format('Y-m-d') ?? now()->format('Y-m-d') }}"
                            class="form-control @error('value-date') is-invalid @enderror" name="value-date" id="value-date"
                            placeholder="" />
                        @error('value-date')
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
        function validateDates() {
            var endDate = document.getElementById('date').value;
            document.getElementById('value-date').max = endDate;
        }
        const form = document.getElementById('edit-entry');
        form && form.addEventListener('submit', function(e) {
            e.submitter.classList.add('disabled');
            $('.loader-overlay').show().find('.loader-text').text('Updating...')
            return 1;
        });
        $('textarea[name="description"]').val('{{ $entry->description ?? 'payment' }}')
    </script>
@endsection
