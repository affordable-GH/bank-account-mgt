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
            <div class="d-flex align-items-center">
                <button id="excelButton" class="btn text-white me-1" data-mdb-ripple-init style="background-color: #438162;"
                    title="Export table to excel" type="button">
                    <i class="fas fa-print me-1"></i>
                    Excel
                </button>
                {{-- <button id="pdfButton" class="btn text-white mx-1" data-mdb-ripple-init style="background-color: #ee4a60;"
                    title="Save table as PDF" type="button">
                    <i class="fas fa-file-pdf me-1"></i>
                    PDF
                </button> --}}
                <button id="printButton" class="btn text-white ms-1" data-mdb-ripple-init style="background-color: #44abff;"
                    title="Click to print table" type="button">
                    <i class="fas fa-print me-1"></i>
                    print
                </button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle" id="table-transfer">
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
                                    {{-- <span class="currency"> --}}
                                        {{ number_format($transfer->amount, 2, '.', ',') }}
                                    {{-- </span> --}}
                                </td>
                                {{-- <td>{{ $transfer->reference_number }}</td> --}}
                                <td>{{ Carbon::parse($transfer->created_at)->format('Y-m-d') }}</td>
                                <td>
                                    {{-- <a title="Edit" class="btn p-1 text-warning"
                                        href="{{ route('transfers.edit', [$account_location->id, $transfer->id]) }}">
                                        <i class="fas fa-eye"></i>
                                    </a> --}}
                                    <button title="Delete transfer" data-id="{{ $transfer->id }}"
                                        data-url="{{ route('transfers.destroy', [$account_location->id, $transfer->id]) }}"
                                        class="btn text-danger p-1 mx-1 delete-transfer" href="#">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-start">Total:</th>
                            <th id="total-balance"></th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('button.delete-transfer').on('click', function() {
                if (!confirm('Confirm Delete Transfer')) {
                    return false;
                }
                const $loader = $('.loader-overlay');
                const $button = $(this);
                var id = $(this).data('id');
                var url = $(this).data('url');
                // $.ajax({
                //     url,
                //     type: 'DELETE',
                //     data: {
                //         _token: '{{ csrf_token() }}'
                //     },
                //     beforeSend: function() {
                //         $button.prop('disabled', true);
                //         $loader.show().find('.loader-text').text('Deleting...');
                //     },
                //     success: function() {
                //         $button.prop('disabled', false);
                //         $loader.hide();
                //         if (response.success) {
                //             window.open(response.url, '_self');
                //         } else {
                //             alert(`Failed to delete account: ${response.message}`);
                //         }
                //     },
                //     error: function() {
                //         $button.prop('disabled', false);
                //         $loader.hide();
                //         console.log('Failed to delete transfer');
                //     }
                // });
            });
            const TRANSFER_TABLE = new DataTable('#table-transfer', {
                // responsive: true,
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                        targets: [7],
                        orderable: false
                    },
                    // {
                    //     targets: [8],
                    //     orderable: false
                    // }
                ],
                pageLength: 50,
                // buttons: ['copy', 'excel', 'pdf', 'csv', 'print'],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api(),
                        data;
                    var intVal = function(i) {
                        if (typeof i === 'string') {
                            return i.replace(/[\$,-]/g, '') * 1.00;
                        } else if (typeof i === 'number') {
                            return i.toString().replace(/-/g, '') * 1.00;
                        }
                    };
                    // var total = api.column(5).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0.00);
                    var formatter = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'GHS',
                    });
                    // var debitTotal = api.column(3, {
                    //     page: 'current'
                    // }).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0.00);
                    // var creditTotal = api.column(4, {
                    //     page: 'current'
                    // }).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0.00);
                    var transferTotal = api.column(5, {
                        page: 'current'
                    }).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0.00);
                    // $(api.column(3).footer()).addClass('text-danger fw-semibold d-inline-block').html(
                    //     formatter.format(debitTotal));
                    // $(api.column(4).footer()).addClass('text-success fw-semibold').html(formatter
                    //     .format(creditTotal));
                    $(api.column(5).footer()).addClass('fw-semibold').html(formatter.format(
                        transferTotal));
                },
                buttons: [{
                        extend: 'excel',
                        title: '{{ strtoupper($account_location->name) }} Transfers',
                        filename: 'entries.excel',
                        text: '<i class="fas fa-print me-1"></i> excel',
                        className: 'btn text-white ms-1',
                        message: 'Printed on ' + new Date().toLocaleString(),
                        attr: {
                            "style": 'background-color: #438162;color: #fff',
                            "data-mdb-ripple-init": '',
                        },
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'pdf',
                        title: '{{ strtoupper($account_location->name) }} Transfers',
                        filename: 'entries.pdf',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                        },
                        text: '<i class="fas fa-print me-1"></i> pdf',
                        className: 'btn text-white ms-1',
                        message: 'Printed on ' + new Date().toLocaleString(),
                        attr: {
                            "style": 'background-color: #ee4a60;color: #fff',
                            "data-mdb-ripple-init": '',
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-1"></i> print',
                        className: 'btn text-white ms-1',
                        title: '<span class="text-uppercase text-center"> {{ $account_location->name }} Transfers </span>',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                        },
                        orientation: 'portrait',
                        message: 'Printed on ' + new Date().toLocaleString(),
                        attr: {
                            "style": 'background-color: #44abff;color: #fff',
                            "data-mdb-ripple-init": '',
                        }
                    }
                ],
                language: {
                    paginate: {
                        first: 'First',
                        previous: 'Prev',
                        next: 'Next',
                        last: 'Last',
                    }
                }
            });
            $('#pdfButton').on('click', function() {
                TRANSFER_TABLE.button(1).trigger();
            });
            $('#excelButton').on('click', function() {
                TRANSFER_TABLE.buttons(0).trigger();
            });
            $('#printButton').on('click', function() {
                TRANSFER_TABLE.button(2).trigger();
            });
        });
    </script>
@endsection
