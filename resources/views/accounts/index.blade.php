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
                        <a href="#">bank list</a>
                    </li>
                    <li class="breadcrumb-item text-uppercase active" aria-current="page">
                        <a href="#">accounts</a>
                    </li>
                </ol>
            </nav>
            <div class="d-flex">
                <button class="btn btn-primary clone-btn me-2" data-url="{{ route('l.clone', $account_location->id) }}"
                    title="clone accounts">clone
                    <i class="fa-regular fa-clone ms-1"></i>
                </button>
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
                <button id="pdfButton" class="btn text-white mx-1" data-mdb-ripple-init style="background-color: #ee4a60;"
                    title="Save table as PDF" type="button">
                    <i class="fas fa-file-pdf me-1"></i>
                    PDF
                </button>
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
                            <th scope="col">bank name</th>
                            <th scope="col">account number</th>
                            <th scope="col">account type</th>
                            <th scope="col">account status</th>
                            <th scope="col">balance(ghs)</th>
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
                                <td>{{ $account->bank_name }}</td>
                                <td>{{ $account->account_number }}</td>
                                <td>{{ $account->accountType->type }}</td>
                                <td>{{ $account->accountStatus->status }}</td>
                                <td class="text-{{ $account->balance >= 0 ? 'success' : 'danger' }} fw-bold">
                                    {{ number_format($account->balance, 2) }}
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
                            <tr>
                                <td colspan="8" class="text-center text-uppercase">
                                    <span class="text-muted">no accounts found</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-start">Total Balance:</th>
                            <th id="total-balance"></th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
    {{-- <script>
        var ids = [];
        $('input[type="checkbox"].check-account').on('change', function(e) {
            e.currentTarget.checked ? ids.push($(this).val()) : ids.pop($(this).val());
            console.log(ids);
        });
    </script> --}}
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            console.log($('button.clone-btn'));

            $('button.clone-btn').click(function() {
                if (!confirm('Confirm cloning accounts?')) {
                    return false;
                }
                const url = $(this).data('url');
                const $button = $(this);
                const $loader = $('.loader-overlay');

                $.ajax({
                    url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        $button.prop('disabled', true);
                        $loader.show().find('.loader-text').text('Cloning...');
                    },
                    success: function(response) {
                        if (response.success) {
                            window.open(response.url, '_blank');
                        } else {
                            alert(`Failed to clone model: ${response.message}`);
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
                        targets: [6],
                        orderable: false
                    },
                    {
                        targets: [7],
                        orderable: false
                    }
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
                    var total = api.column(5).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0.00);
                    var formatter = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'GHS',
                    });
                    var pageTotal = api.column(5, {
                        page: 'current'
                    }).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0.00);
                    $(api.column(5).footer()).html(formatter.format(pageTotal));
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
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'pdf',
                        title: '{{ strtoupper($account_location->name) }} Bank List',
                        filename: 'bank-list.pdf',
                        orientation: 'portrait',
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
                        title: '<span class="text-uppercase text-center"> {{ $account_location->name }} Bank List</span>',
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
