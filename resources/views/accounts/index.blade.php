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
                        <a href="#">bank list</a>
                    </li>
                    <li class="breadcrumb-item text-uppercase active" aria-current="page">
                        <a href="#">accounts</a>
                    </li>
                </ol>
            </nav>
            <div class="d-flex">
                <a class="btn btn-info" href="{{ route('account.create', $account_location->id) }}">new account</a>
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
                <table id="table-accounts" class="table align-middle text-uppercase">
                    <thead class="text-uppercase">
                        <tr class="border-bottom border-info">
                            <th>S/N</th>
                            <th scope="col">bank</th>
                            <th scope="col">account number</th>
                            <th scope="col">account status</th>
                            {{-- <th scope="col">location</th> --}}
                            <th scope="col">account type</th>
                            <th scope="col" title="ENTRIES TO RECONCILE">ETR</th>
                            <th scope="col">balance</th>
                            <th scope="col">date created</th>
                            <th scope="col">operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accounts as $account)
                            <tr
                                class="{{ $account->accountStatus->status == 'closed' ? 'table-danger text-danger border-danger' : '' }}">
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>{{ $account->account_address }}</td>
                                <td>{{ $account->account_number }}</td>
                                <td>{{ $account->accountStatus->status }}</td>
                                {{-- <td>{{ $account->accountLocation->name }}</td> --}}
                                <td>{{ $account->accountType->type }}</td>
                                <td>
                                    <a role="button"
                                        href="{{ $account->entries()->entriesToReconcile()->count() > 0 ? route('account.show', [$account_location->id, $account->id]) : '#' }}"
                                        title="GO TO ENTRIES"
                                        class="btn btn-secondary p-2 {{ $account->entries()->entriesToReconcile()->count() > 0 ? '' : 'disabled' }}">{{ $account->entries()->entriesToReconcile()->count() }}
                                    </a>
                                </td>
                                <td class="text-{{ $account->balance >= 0 ? 'success' : 'danger' }} fw-bold">
                                    {{ number_format($account->balance, 2, '.', ',') }}
                                </td>
                                <td>{{ Carbon::parse($account->created_at)->format('Y-m-d') }}</td>
                                <td>
                                    <a title="View" class="btn btn- p-1" title="View"
                                        href="{{ route('account.show', [$account_location->id, $account->id]) }}">
                                        <i class="fas text-info fa-eye"></i>
                                    </a>
                                    <a title="Edit" class="btn btn- p-1"
                                        href="{{ route('account.edit', [$account_location->id, $account->id]) }}">
                                        <i class="fas text-warning fa-pen-to-square"></i>
                                    </a>
                                    <button title="Delete Account" data-id="{{ $account->id }}"
                                        data-url="{{ route('account.destroy', [$account_location->id, $account->id]) }}"
                                        class="btn btn- p-1 mx-1 delete-account">
                                        <i class="fas text-danger fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-start">Total:</th>
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
            $('button.delete-account').click(function() {
                const id = $(this).data('id');
                const url = $(this).data('url');
                const $button = $(this);
                const $loader = $('.loader-overlay');
                if (!confirm('Confirm delete account')) {
                    return false;
                }
                $.ajax({
                    url,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        $button.prop('disabled', true);
                        $loader.show().find('.loader-text').text('Deleting...');
                    },
                    success: function(response) {
                        if (response.success) {
                            window.open(response.url, '_self');
                        } else {
                            alert(`Failed to delete account: ${response.message}`);
                        }
                        $button.prop('disabled', false);
                        $loader.hide();
                    },
                    error: function(xhr, status, error) {
                        alert(`An error occurred: ${xhr.responseText}`);
                        $button.prop('disabled', false);
                        $loader.hide();
                    }
                });
            });
            const ACCOUNTS_TABLE = new DataTable('#table-accounts', {
                responsive: true,
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                        targets: [8],
                        orderable: false
                    },
                    // {
                    //     targets: [7],
                    //     orderable: false
                    // }
                ],
                // dom: '<"row"<"col-md-4"l><"col-md-4"B><"col-md-4"f>>rt<"row"<"col-md-4"i><"col-md-4"p><"col-md-4"n>>',
                buttons: ['copy', 'excel', 'pdf', 'csv', 'print'],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api(),
                        data;
                    var intVal = function(i) {
                        if (typeof i === 'string') {
                            return i.replace(/[\$,]/g, '') * 1.00;
                        } else if (typeof i === 'number') {
                            return i;
                        }
                    };
                    var total = api.column(6).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0.00);
                    var formatter = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'GHS',
                    });
                    var pageTotal = api.column(6, {
                        page: 'current'
                    }).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0.00);
                    $(api.column(6).footer()).addClass('fw-semibold').html(formatter.format(pageTotal));
                },
                buttons: [{
                        extend: 'excel',
                        title: '{{ strtoupper($account_location->name) }} Bank List',
                        filename: 'bank-list.excel',
                        text: '<i class="fas fa-print me-1"></i> excel',
                        className: 'btn text-white ms-1',
                        message: 'Printed on ' + new Date().toLocaleString(),
                        attr: {
                            "style": 'background-color: #438162;color: #fff',
                            "data-mdb-ripple-init": '',
                        },
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    },
                    {
                        extend: 'pdf',
                        title: '{{ strtoupper($account_location->name) }} Bank List',
                        filename: 'bank-list.pdf',
                        orientation: 'portrait',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
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
                        title: '<span class="text-uppercase text-center"> {{ $account_location->name }} Bank List</span>',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
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
                ACCOUNTS_TABLE.button(1).trigger();
            });
            $('#excelButton').on('click', function() {
                ACCOUNTS_TABLE.buttons(0).trigger();
            });
            $('#printButton').on('click', function() {
                ACCOUNTS_TABLE.button(2).trigger();
            });
        });
    </script>
@endsection
