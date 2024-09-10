<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <h4 class="power-zone"> <span>P</span> <span>O</span> <span>W</span> <span>E</span> <span>R</span> <span>
                </span> <span>Z</span> <span>O</span> <span>N</span> <span>E</span> </h4>
        </div>


        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item has-treeview">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Client -->
                <li
                    class="nav-item has-treeview {{ request()->routeIs('admin.regularCustomer.*') || request()->routeIs('admin.irregularCustomer.*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->routeIs('admin.regularCustomer.*') || request()->routeIs('admin.irregularCustomer.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Client
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.regularCustomer.index') }}"
                                class="nav-link {{ request()->routeIs('admin.regularCustomer.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Regular Client</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.irregularCustomer.index') }}"
                                class="nav-link {{ request()->routeIs('admin.irregularCustomer.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Irregular Client</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Supplier -->
                <li class="nav-item">
                    <a href="{{ route('admin.supplier.index') }}"
                        class="nav-link {{ request()->routeIs('admin.supplier.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Supplier</p>
                    </a>
                </li>

                <!-- Products -->
                <li
                    class="nav-item has-treeview {{ request()->routeIs('admin.category.*') || request()->routeIs('admin.product.*') || request()->routeIs('admin.nonInventory.*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->routeIs('admin.category.*') || request()->routeIs('admin.product.*') || request()->routeIs('admin.nonInventory.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>
                            Products
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.category.index') }}"
                                class="nav-link {{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Category List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.product.index') }}"
                                class="nav-link {{ request()->routeIs('admin.product.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Product List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.nonInventory.index') }}"
                                class="nav-link {{ request()->routeIs('admin.nonInventory.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Non Inventory List</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <!-- Billing -->
                <li
                    class="nav-item has-treeview {{ request()->routeIs('admin.bill.*') || request()->routeIs('admin.monthlyBill.*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->routeIs('admin.bill.*') || request()->routeIs('admin.monthlyBill.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>
                            Billing
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.bill.create') }}"
                                class="nav-link {{ request()->routeIs('admin.bill.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Bill</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.bill.index') }}"
                                class="nav-link {{ request()->routeIs('admin.bill.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Bill List
                                </p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.monthlyBill.index') }}"
                                class="nav-link {{ request()->routeIs('admin.monthlyBill.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Monthly Bill</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview {{ request()->routeIs('admin.quotation.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.quotation.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Quotation
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.quotation.create') }}"
                                class="nav-link {{ request()->routeIs('admin.quotation.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Quotation From</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.quotation.index') }}"
                                class="nav-link {{ request()->routeIs('admin.quotation.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Quotation List
                                </p>
                            </a>
                        </li>
                    </ul>

                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<style>
    .power-zone {
        font-family: 'Arial', sans-serif;
        font-weight: bold;
        text-shadow: 6px 8px 8px rgba(243, 222, 222, 0.2);
    }

    .power-zone span {
        color: #71ce1b;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        animation: stylish-text 3s infinite;
    }

    @keyframes stylish-text {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }
</style>
