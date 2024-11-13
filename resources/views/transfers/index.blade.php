@extends('layout.index')
@section('content')
    @use(Carbon\Carbon)
    <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary my-3 shadow-0 position-sticky"
        style="top: 60px;z-index: 50;">
        <div class="container-fluid d-flex justify-content-between">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item text-uppercase">
                        <a href="#">{{ env('APP_NAME') }}</a>
                    </li>
                    <li class="breadcrumb-item text-uppercase">
                        <a href="#">accounts</a>
                    </li>
                    <li class="breadcrumb-item text-uppercase active" aria-current="page">
                        <a href="#">transfers</a>
                    </li>
                </ol>
            </nav>
            <div class="d-flex">
                <a class="btn btn-info" href="{{ route('transfers.create', $account_location->id) }}">create transfer</a>
            </div>
        </div>
    </nav>
    <div class="card shadow-1-soft">
        <div class="card-body">
            {{-- {{ $accounts }} --}}
            {{-- <h5 class="card-title text-capitalize mb-4">create bank account</h5> --}}
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="text-uppercase">
                        <tr>
                            <th>S/N </th>
                            <th scope="col">from account</th>
                            <th scope="col">to account</th>
                            <th scope="col" title="TRANSFER TYPE">trans. type</th>
                            <th scope="col">description</th>
                            <th scope="col" title="TRANSFER AMOUNT">amount</th>
                            {{-- <th scope="col">ref. number</th> --}}
                            <th scope="col">date</th>
                            <th scope="col">actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transfers as $transfer)
                            <tr class="border-bottom">
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>{{ $transfer->fromAccount->name . ($transfer->fromAccount->accountLocation->id == $account_location->id ? '' : ' (' . $transfer->fromAccount->accountLocation->name . ')') }}
                                </td>
                                <td>{{ $transfer->toAccount->name . ($transfer->toAccount->accountLocation->id == $account_location->id ? '' : ' (' . $transfer->fromAccount->accountLocation->name . ')') }}
                                </td>
                                <td>
                                    <span class="btn btn-secondary">
                                        {{ $transfer->transferType->type }}
                                    </span>
                                </td>
                                <td>{{ $transfer->notes }}</td>
                                <td>
                                    <span class="currency">
                                        {{ number_format($transfer->amount, 2, '.', ',') }}
                                    </span>
                                </td>
                                {{-- <td>{{ $transfer->reference_number }}</td> --}}
                                <td>{{ Carbon::parse($transfer->created_at)->format('Y-m-d') }}</td>
                                <td>
                                    {{-- <a title="Edit" class="btn p-1 text-warning"
                                        href="{{ route('transfers.edit', [$account_location->id, $transfer->id]) }}">
                                        <i class="fas fa-eye"></i>
                                    </a> --}}
                                    <button title="Delete" data-id="{{ $transfer->id }}"
                                        data-url="{{ route('transfers.destroy', [$account_location->id, $transfer->id]) }}"
                                        class="btn text-danger p-1 mx-1 delete-transfer" href="#">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>

                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <script>
        var ids = [];
        $('input[type="checkbox"].check-account').on('change', function(e) {
            e.currentTarget.checked ? ids.push($(this).val()) : ids.pop($(this).val());
            console.log(ids);
        });
    </script>
@endsection
