@extends('layout.index')
@section('content')
    @use(Carbon\Carbon)
    <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary my-3 shadow-0 position-sticky d-"
        style="top: 60px;z-index: 50;">
        <div class="container-fluid d-flex justify-content-between">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item text-uppercase">
                        <a href="#">{{ $account_location->name }}</a>
                    </li>
                    <li class="breadcrumb-item text-uppercase">
                        <a href="#">{{ $account->bank_name }}</a>
                    </li>
                    <li class="breadcrumb-item text-uppercase active" aria-current="page">
                        <a href="#">{{ $account->name }}</a>
                    </li>
                </ol>
            </nav>
            <div class="d-flex">
                {{-- @if ($account->entries->isNotEmpty()) --}}
                <button title="Reconcile Entries" class="btn btn-success reconcile-entries mx-1" disabled>
                    reconcile <i class="fas fa-check ms-1"></i>
                </button>
                {{-- @endif --}}
            </div>
        </div>
    </nav>
@section('name')
    {{ $account->bank_name . ': ' . $account->account_number }}
@endsection
<div class="card shadow-1-soft">
    <div class="card-body">
        <!-- Tabs navs -->
        <ul class="nav nav-tabs n mb-3" id="ex1" role="tablist">
            <li class="nav-item" role="presentation">
                <a data-mdb-tab-init class="nav-link" id="ex3-tab-3" href="#ex3-tabs-3" role="tab"
                    aria-controls="ex3-tabs-3" aria-selected="false">account</a>
            </li>
            <li class="nav-item" role="presentation">
                <a data-mdb-tab-init class="nav-link active" id="ex3-tab-1" href="#ex3-tabs-1" role="tab"
                    aria-controls="ex3-tabs-1" aria-selected="true">bank entries</a>
            </li>
            <li class="nav-item" role="presentation">
                <a data-mdb-tab-init class="nav-link" id="ex3-tab-2" href="#ex3-tabs-2" role="tab"
                    aria-controls="ex3-tabs-2" aria-selected="false">reports</a>
            </li>
        </ul>
        <!-- Tabs navs -->

        <!-- Tabs content -->
        <div class="tab-content" id="ex2-content">
            <div class="tab-pane fade show active" id="ex3-tabs-1" role="tabpanel" aria-labelledby="ex3-tab-1">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="table-action-btns">
                        <button id="excelButton" class="btn text-white me-1" data-mdb-ripple-init
                            style="background-color: #438162;" title="Export table to excel" type="button">
                            <i class="fas fa-print me-1"></i>
                            Excel
                        </button>
                        <button id="pdfButton" class="btn text-white mx-1" data-mdb-ripple-init
                            style="background-color: #ee4a60;" title="Save table as PDF" type="button">
                            <i class="fas fa-file-pdf me-1"></i>
                            PDF
                        </button>
                        <button id="printButton" class="btn text-white ms-1" data-mdb-ripple-init
                            style="background-color: #1179ce;" title="Click to print table" type="button">
                            <i class="fas fa-print me-1"></i>
                            print
                        </button>
                    </div>
                    @if ($account->accountStatus->status === 'open')
                        <a role="button" class="btn btn-info"
                            href="{{ route('entries.create', [$account_location->id, 'account' => $account->id]) }}">
                            create entry
                        </a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table align-middle text-uppercase" id="table-account-entries">
                        <thead class="text-uppercase">
                            <tr>
                                <th scope="col" title="REFERENCE NUMBER">ref. number</th>
                                <th scope="col">description</th>
                                <th scope="col" title="PAYMENT DATE">pay. date</th>
                                <th scope="col" title="VALUE DATE">value date</th>
                                <th scope="col">debit</th>
                                <th scope="col">credit</th>
                                <th scope="col" title="ACCOUNT BALANCE">balance</th>
                                <th scope="col">actions</th>
                                <th>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($account->entries as $entry)
                                <tr
                                    class="border-bottom table-{{ $entry->is_reconciled ? 'secondary' : '' }} border-{{ $entry->entryType->type === 'debit' ? 'danger' : 'success' }}">
                                    <td>{{ $entry->reference_number }}</td>
                                    <td>{{ $entry->description }}</td>
                                    <td class="text-nowrap">{{ Carbon::parse($entry->created_at)->format('d/m/Y') }}
                                    </td>
                                    <td class="text-nowrap">{{ Carbon::parse($entry->value_date)->format('d/m/Y') }}
                                    </td>
                                    <td
                                        class="fw-bold text-{{ $entry->entryType->type === 'debit' ? 'danger' : 'success' }}">
                                        @if ($entry->entryType->type === 'debit')
                                            {{ '-' . number_format($entry->amount, 2, '.', ',') }}
                                        @endif
                                    </td>
                                    <td
                                        class="fw-bold text-{{ $entry->entryType->type === 'debit' ? 'danger' : 'success' }}">
                                        @if ($entry->entryType->type === 'credit')
                                            {{ '+' . $entry->is_transfer ? number_format($entry->amount, 2, '.', ',') : '' }}
                                        @endif
                                    </td>
                                    <td class="fw-bold">
                                        {{ $entry->is_reconciled ? number_format($entry->amount, 2, '.', ',') : 0 }}
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a title="Edit" class="btn text-warning p-1 mx-1"
                                                href="{{ route('entries.edit', [$account_location->id, $entry->id]) }}">
                                                <i class="fas fa-pen-to-square"></i>
                                            </a>
                                            @if (!$entry->is_reconciled)
                                                <button title="Delete" data-id="{{ $entry->id }}"
                                                    data-url="{{ route('entries.destroy', [$account_location->id, $entry->id]) }}"
                                                    class="btn text-danger p-1 mx-1 delete-entry" href="#">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="m-0">
                                        <div class="form-check-inline m-0">
                                            <input class="form-check-input check-account-entry" autocomplete="off"
                                                {{ $entry->is_reconciled ? 'disabled' : '' }}
                                                value="{{ $entry->is_reconciled ? '' : $entry->id }}"
                                                type="checkbox" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-start">Totals:</th>
                                <th id="total-debit"></th>
                                <th id="total-credit"></th>
                                <th id="total-balance"></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="ex3-tabs-2" role="tabpanel" aria-labelledby="ex3-tab-2">
                Tab 2 content
            </div>
            {{-- <div class="tab-pane fade" id="ex3-tabs-3" role="tabpanel" aria-labelledby="ex3-tab-3">
                Tab 3 content
            </div> --}}
        </div>
        <!-- Tabs content -->
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        const ACCOUNT_WITH_ENTRIES_TABLE = new DataTable('#table-account-entries', {
            // responsive: true,
            order: [
                [0, 'desc']
            ],
            columnDefs: [{
                    targets: [6],
                    orderable: false
                },
                {
                    targets: [6],
                    orderable: false
                }
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
                var total = api.column(5).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0.00);
                var formatter = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'GHS',
                });
                var debitTotal = api.column(4, {
                    page: 'current'
                }).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0.00);
                var creditTotal = api.column(5, {
                    page: 'current'
                }).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0.00);
                var balanceTotal = api.column(6, {
                    page: 'current'
                }).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0.00);
                $(api.column(4).footer()).addClass('text-danger fw-semibold d-inline-block').html(
                    formatter.format(debitTotal));
                $(api.column(5).footer()).addClass('text-success fw-semibold').html(formatter
                    .format(creditTotal));
                $(api.column(6).footer()).addClass('fw-semibold').html(formatter.format(
                    balanceTotal));
            },
            buttons: [{
                    extend: 'excel',
                    title: '{{ strtoupper($account_location->name . ' - ' . $account->bank_name) }} Entries   ',
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
                    title: '{{ strtoupper($account_location->name . ' - ' . $account->bank_name) }} Entries   ',
                    filename: 'entries.pdf',
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
                    title: '<span class="text-uppercase text-center"> {{ $account_location->name . ' - ' . $account->bank_name }} Entries </span>',
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
            ACCOUNT_WITH_ENTRIES_TABLE.button(1).trigger();
        });
        $('#excelButton').on('click', function() {
            ACCOUNT_WITH_ENTRIES_TABLE.buttons(0).trigger();
        });
        $('#printButton').on('click', function() {
            ACCOUNT_WITH_ENTRIES_TABLE.button(2).trigger();
        });
        $('button.delete-entry').click(function(e) {
            e.preventDefault();
            var id = $(this).data('id'),
                url = $(this).data('url');

            if (confirm('Delete this entry?')) {
                $.ajax({
                    url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        window.open(response.url, '_self');
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });
        updateButtonState();

        function updateButtonState() {
            const checkboxes = $(
                'table#table-account-entries input[type="checkbox"].check-account-entry:checked');
            const reconcileButton = $('button.reconcile-entries'); // Replace with your button selector
            const deleteButton = $('button.delete-entries'); // Replace with your button selector

            if (checkboxes.length > 0) {
                reconcileButton.removeAttr('disabled');
                deleteButton.removeAttr('disabled');
            } else {
                reconcileButton.attr('disabled', 'disabled');
                deleteButton.attr('disabled', 'disabled');
            }
            // if (checkboxes.length > 1) {
            //     deleteButton.removeAttr('disabled');
            // } else {
            //     deleteButton.attr('disabled', 'disabled');
            // }
        }
        var entries = []; // Declare array outside function scope
        // Listen for checkbox changes
        $('table#table-account-entries input[type="checkbox"].check-account-entry').change(function() {
            const checkboxId = $(this).val();
            const isChecked = $(this).is(':checked');
            if (isChecked) {
                entries.push(checkboxId);
            } else {
                entries = entries.filter(id => id !== checkboxId);
            }
            updateButtonState();
            console.log(entries);

        });
        $('button.reconcile-entries').click(function() {
            // Reconcile entries
            if (!confirm('Confirm Reconcile Entries')) {
                return false;
            }
            $.ajax({
                url: "{{ route('entries.reconcile', $account_location->id) }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    entries
                },
                success: function(response) {
                    window.open(response.url, '_self');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>
@endsection
