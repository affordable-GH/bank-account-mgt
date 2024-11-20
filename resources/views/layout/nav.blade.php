<header class="bg-body-tertiary fixed-top">
    <ul class="nav" style="z-index: 1092;">
        <div class="container">
            <ul class="navbar-nav flex-row p-2 justify-content-between">
                <li class="nav-item d-flex align-items-center">
                    <button type="button" class="btn btn-sm px-1 py-1 me-2 nav-toggler-button">
                        <i class="bx bx-menu-alt-left fa-2x"></i>
                    </button>
                    <a class="nav-link" href="{{ route('l.list') }}"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <h3 class="h3 flex my-auto align-items-center fs-4 fw-bold text-uppercase">
                        {{ $account_location->name }} 
                    </h3>
                </li>
                <li class="nav-item">
                    <a id="signout" href="#" class="nav-link" title="click to logout">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Sign out</span>
                    </a>
                </li>
                <script>
                    $(document).ready(function() {
                        $('a#signout').click(function() {
                            $.ajax({
                                url: "{{ route('logout') }}",
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function() {
                                    window.location.replace("{{ route('login') }}");
                                }
                            });
                            return false; // to prevent the default link behavior from occurring
                        });
                    });
                </script>
            </ul>
        </div>
    </ul>
</header>
