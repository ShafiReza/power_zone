<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <!-- Add user panel content here if needed -->
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item has-treeview menu-open">
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Client -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-copy"></i>
                        <p>
                            Client
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.regularCustomer.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Regular Client</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.irregularCustomer.index') }}" class="nav-link {{ request()->routeIs('admin.irregularCustomer.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Irregular Client</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Supplier -->
                <li class="nav-item">
                    <a href="{{ route('admin.supplier.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>Supplier</p>
                    </a>
                </li>

                <!-- Products -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tree"></i>
                        <p>
                            Products
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.category.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Category List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.product.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Product List</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Billing -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tree"></i>
                        <p>
                            Billing
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.bill.create') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Bill</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.bill.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Bill List
                                </p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.bill.monthlyBill') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Monthly Bill</p>
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

