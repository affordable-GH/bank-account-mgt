<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/jquery/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/app/loader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/boxicons/boxicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/datatables.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2-bootstrap-5-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/perfect-scroll/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/animate-css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/alert/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/mdb/css/mdb.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/app/main.css') }}" />
    <link rel="stylesheet" href="">
    <script src="{{ asset('assets/jquery/external/jquery.js') }}"></script>
    <script src="{{ asset('assets/jquery/jquery.inputmask.min.js') }}"></script>
    <title>ACCOUNTS MANAGER | {{ strtoupper($page_title ?? 'HOME') }}</title>
</head>
<style>
    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1096;
        overflow: hidden !important;
    }

    .loader-overlay .loader-text {
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24pt;
        text-align: center;
        margin-top: 20px;
        animation: blink 2s infinite;
        animation-timing-function: ease-in-out;
        color: #fff;
        position: absolute;
        bottom: 35%;
    }

    @keyframes blink {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    @media print {
        table tr th {
            text-transform: uppercase !important;
        }
    }

    table tr td:first-child,
    table tr th:first-child {
        text-align: left !important;
        margin: auto !important;
    }

    .sidebar {
        background-color: #000655;
        padding: 20px;
        position: fixed;
        color: #fff;
        top: 0;
        left: 0;
        bottom: 0;
        width: 100%;
        width: 320px;
        height: 100%;
        z-index: 1090;
        transition: all 0.3s ease-in-out;
        /* transform: translateX(-200px); */
    }

    .nav-menu {
        width: 100%;
        /* background-color: #299D91; */
        text-transform: capitalize;
    }

    .nav-menu-link {
        color: #fff;
        text-decoration: none;
        padding: 12px;
        display: flex;
        cursor: pointer;
        align-items: center;
        margin: 3px auto;
    }

    .nav-menu-link>span>i {
        margin-left: 10px;
        margin-right: 10px;
        font-size: 18pt;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);

    }

    .nav-menu-link:is(:hover, :focus),
    .nav-menu-link.active {
        color: #fff;

        background-color: #299D91;
    }

    .main {
        padding: 12px;
        margin-left: 320px;
    }

    @media (max-width: 768px) {

        .sidebar {
            left: -320px;
        }

        .sidebar.show {
            left: 0;
            z-index: 1090;
        }

        .main {
            margin-left: 0 !important;
        }

        .nav-toggler-button {
            display: inline-flex !important;
        }
    }

    .nav-toggler-button {
        display: none;
    }

    .backdrop {
        position: fixed;
        display: none;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* z-index: 999; */
        opacity: 1;
        transition: opacity 0.3s ease-in-out;
    }

    .backdrop.show {
        opacity: 1;
        transition: opacity 0.3s ease-in-out;
        z-index: 990;
        display: block;
        /* pointer-events: none; */
    }

    INPUT:-webkit-autofill,
    SELECT:-webkit-autofill,
    TEXTAREA:-webkit-autofill,
    INPUT:-webkit-autofill:hover,
    INPUT:-webkit-autofill:focus,
    INPUT:-webkit-autofill:active,
    SELECT:-webkit-autofill:hover,
    SELECT:-webkit-autofill:focus,
    SELECT:-webkit-autofill:active,
    TEXTAREA:-webkit-autofill:hover,
    TEXTAREA:-webkit-autofill:focus,
    TEXTAREA:-webkit-autofill:active {
        animation-name: onautofillstart;
        -webkit-background-clip: text;
        background-color: transparent !important;
        color: #000 !important;
        box-shadow: none !important;
        /* -webkit-text-fill-color: #000000; */
        -webkit-text-fill-color: #000 !important;
        /* -webkit-box-shadow: 0 0 20px 20px #fff inset !important; */
    }
</style>
@php
    // $account_location =
@endphp

<body>
    <div class="loader-overlay">
        <div class="loadingio-spinner-dual-ring-nq4q5u6dq7r">
            <div class="ldio-x2uulkbinbj">
                <div></div>
                <div>
                    <div></div>
                </div>
            </div>
        </div>
        <div class="loader-text text-white fs-2">Loading...</div>
    </div>
    @include('layout.nav')
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-2 sidebar">
                @include('layout.sidebar')
            </div>
            <div class="col-md-10 main">
                <div class="container mt-auto">
                    <div class="d-flex mt-3 align-items-center justify-content-between">
                        <div class="align-items-center">
                            <button class="btn btn-primary clone-btn me-2"
                                data-url="{{ route('l.clone', $account_location->id) }}" title="clone accounts">clone
                                <i class="fa-regular fa-clone ms-1"></i>
                            </button>
                            <button class="btn text-white new-account me-2" title="Create new location"
                                style="background-color: #ac2bac;">new location</button>
                            <button class="btn btn-warning modify-account">modify</button>
                        </div>
                    </div>
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <div class="backdrop"></div>
    <script src="{{ asset('assets/mdb/js/mdb.umd.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/perfect-scroll/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/app/color-modes.js') }}"></script>
    <script src="{{ asset('assets/alert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/jquery/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/app/main.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.loader-overlay').hide();
            (function() {
                const NavTogglerButton = document.querySelector('.nav-toggler-button');
                const Sidebar = document.querySelector('.sidebar');
                const BackDrop = document.querySelector('.backdrop');
                NavTogglerButton.addEventListener('click', function() {
                    // alert('Backdrop clicked')
                    Sidebar.classList.toggle('show');
                    BackDrop.classList.toggle('show');
                });
                BackDrop.addEventListener('click', function() {
                    Sidebar.classList.remove('show');
                    BackDrop.classList.remove('show');
                });
            })();
            $('button.modify-account').click(function() {
                const locationId = "{{ $account_location->id }}";
                const updateUrl = "{{ route('l.update', $account_location->id) }}";

                Swal.fire({
                    title: "Modify Account Location Name",
                    input: "text",
                    inputLabel: "Enter Location Name",
                    inputValue: "{{ $account_location->name }}",
                    showCancelButton: true,
                    confirmButtonText: "Update",
                    cancelButtonText: "Cancel",
                    inputValidator: (value) => {
                        if (!value) {
                            return "Input field cannot be empty!";
                        }
                    },
                    preConfirm: (locationName) => {
                        $.ajax({
                                url: updateUrl,
                                method: 'PUT',
                                data: {
                                    name: locationName,
                                    _token: '{{ csrf_token() }}',
                                },
                            })
                            .done((response) => {
                                if (response.success) {
                                    window.open(response.url, '_self');
                                    // showSuccessAlert.fire({
                                    //     icon: 'success',
                                    //     text: 'Account location updated successfully!',
                                    //     padding: '15px',
                                    //     width: 'auto'
                                    // });
                                    // setTimeout(() => {
                                    //     location.reload();
                                    // }, 1500);
                                }
                            })
                            .fail((error) => {
                                if (error.status === 422) {
                                    const errors = error.responseJSON.errors;
                                    // return console.log(errors);

                                    let errorMessages = '';
                                    for (let key in errors) {
                                        errorMessages += errors[key][0];
                                    }
                                    showSuccessAlert.fire({
                                        icon: 'error',
                                        text: errorMessages,
                                        padding: '15px',
                                        width: 'auto'
                                    });
                                } else {
                                    showSuccessAlert.fire({
                                        icon: 'error',
                                        text: 'Failed to update account location. Please try again later.',
                                        padding: '15px',
                                        width: 'auto'
                                    });
                                }
                            });
                    }
                });
            });
            $('button.new-account').click(function() {
                const url = "{{ route('l.new') }}";
                Swal.fire({
                    title: "Add New Location",
                    input: "text",
                    inputLabel: "Enter Location Name",
                    inputValue: "",
                    showCancelButton: true,
                    confirmButtonText: "Create",
                    cancelButtonText: "Cancel",
                    inputValidator: (value) => {
                        if (!value) {
                            return "field cannot be empty!";
                        }
                    },
                    preConfirm: (locationName) => {
                        $.ajax({
                                url,
                                method: 'POST',
                                data: {
                                    name: locationName,
                                    _token: '{{ csrf_token() }}',
                                },
                            })
                            .done((response) => {
                                if (response.success) {
                                    window.open(response.url, '_self');
                                }
                            })
                            .fail((error) => {
                                if (error.status === 422) {
                                    const errors = error.responseJSON.errors;
                                    // return console.log(errors);

                                    let errorMessages = '';
                                    for (let key in errors) {
                                        errorMessages += errors[key][0];
                                    }
                                    showSuccessAlert.fire({
                                        icon: 'error',
                                        text: errorMessages,
                                        padding: '15px',
                                        width: 'auto'
                                    });
                                } else {
                                    showSuccessAlert.fire({
                                        icon: 'error',
                                        text: 'Failed to update account location. Please try again later.',
                                        padding: '15px',
                                        width: 'auto'
                                    });
                                }
                            });
                    }
                });
            });
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
                            window.location.replace(response.url);
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
            $('.currencyInput').inputmask({
                alias: 'currency',
                prefix: '',
                rightAlign: false,
                digits: 2,
                digitsOptional: false,
                groupSeparator: ',',
                autoGroup: true,
                removeMaskOnSubmit: true,
                unmaskAsNumber: true
            });

            $('.currencyInput').keypress(function(e) {
                var charCode = (e.which) ? e.which : event.keyCode;
                if ((charCode < 48 || charCode > 57) && charCode !== 46) {
                    return false;
                }
            });

            $('input[type="date"]').datepicker({
                dateFormat: 'yy-mm-dd', // Adjust the date format as needed 
                changeMonth: true, // Allows changing the month 
                changeYear: true, // Allows changing the year 
                showButtonPanel: true // Adds buttons to clear or close the datepicker 
            });
        });
        const showSuccessAlert = Swal.mixin({
            position: 'top-right',
            toast: true,
            timer: 6500,
            showCloseButton: true,
            showConfirmButton: false,
            timerProgressBar: false,
            onOpen: () => {
                setInterval(() => {
                    Swal.close()
                }, 6500);
            },
            showClass: {
                popup: `
                    animate__animated
                    animate__fadeInDown
                    animate__faster
                    `
            },
        });
    </script>
    @yield('script')
    @if (session('success'))
        <script type="text/javascript">
            showSuccessAlert.fire({
                icon: 'success',
                text: '{{ session('success') }}',
                padding: '15px',
                width: 'auto'
            });
        </script>
    @endif
    @if (session('error'))
        <script type="text/javascript">
            showSuccessAlert.fire({
                icon: 'error',
                text: '{{ session('error') }}',
                padding: '15px',
                width: 'auto'
            });
        </script>
    @endif
    @if (session('warning'))
        <script type="text/javascript">
            showSuccessAlert.fire({
                icon: 'warning',
                text: '{{ session('warning') }}',
                padding: '15px',
                width: 'auto'
            });
        </script>
    @endif
    @if (session('info'))
        <script type="text/javascript">
            showSuccessAlert.fire({
                icon: 'info',
                text: '{{ session('info') }}',
                padding: '15px',
                width: 'auto'
            });
        </script>
    @endif
</body>

</html>
