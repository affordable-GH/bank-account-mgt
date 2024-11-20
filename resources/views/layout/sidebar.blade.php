{{-- <aside class="sidebar"> --}}
<ul class="nav mb-auto p-3">
    <li class="nav-menu">
        <a href="{{ route('account.create', $account_location->id) }}" class="nav-menu-link" data-mdb-ripple-init
            data-mdb-ripple-color="light">
            <span class="d-flex align-items-center">
                <i class="bx bx-grid-alt"></i>
            </span>
            New account
        </a>
    </li>
    <li class="nav-menu">
        <a href="{{ route('account.home', $account_location->id) }}" class="nav-menu-link " data-mdb-ripple-init
            data-mdb-ripple-color="light">
            <span class="d-flex align-items-center">
                <i class='bx bx-list-ul'></i>
            </span>
            bank list
        </a>
    </li>
    <li class="nav-menu">
        <a href="{{ route('entries.index', $account_location->id) }}" class="nav-menu-link " data-mdb-ripple-init
            data-mdb-ripple-color="light">
            <span class="d-flex align-items-center">
                <i class='bx bx-money'></i>
            </span>
            list entries
        </a>
    </li>
    <li class="nav-menu">
        <a href="{{ route('transfers.index', $account_location->id) }}" class="nav-menu-link " data-mdb-ripple-init
            data-mdb-ripple-color="light">
            <span class="d-flex align-items-center">
                <i class="fas fa-exchange-alt"></i>
            </span>
            transfers
        </a>
    </li>
    <li class="nav-menu">
        <a href="#" class="nav-menu-link " data-mdb-ripple-init data-mdb-ripple-color="light">
            <span class="d-flex align-items-center">
                <i class="bx bx-trash  "></i>
            </span>
            trash
        </a>
    </li>
</ul>
<div class="container position-absolute me-2" style="bottom: 60px;left: 0;">
    <span id="date-time"></span>
</div>
{{-- </aside> --}}
