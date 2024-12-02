<nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> Menú </a> </li>
                </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto"> 
                    <!--begin::Notifications Dropdown Menu-->
                    <li class="nav-item dropdown"> <a class="nav-link" data-bs-toggle="dropdown" href="#"> <i class="bi bi-bell-fill"></i> <span class="navbar-badge badge text-bg-warning">1</span> </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end"> <span class="dropdown-item dropdown-header">1 Notificación</span>
                            <div class="dropdown-divider"></div> <a href="#" class="dropdown-item"> <i class="bi bi-file-earmark-fill me-2"></i> Nueva revisión pendiente
                                <span class="float-end text-secondary fs-7">2 días</span> </a>
                            <div class="dropdown-divider"></div> <a href="#" class="dropdown-item"> <i class="bi bi-file-earmark-fill me-2"></i> Nuevo reporte
                                <span class="float-end text-secondary fs-7">1 hora</span> </a>
                            <div class="dropdown-divider"></div> <a href="#" class="dropdown-item dropdown-footer">
                                Ver todas las notificaciones
                            </a>
                        </div>
                    </li> <!--end::Notifications Dropdown Menu-->
                </ul> <!--end::End Navbar Links-->
            </div> <!--end::Container-->
        </nav> <!--end::Header--> <!--begin::Sidebar-->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark"> <!--begin::Sidebar Brand-->
            <div class="sidebar-brand"> <!--begin::Brand Link--> <a href="/dev/panel/" class="brand-link"> <!--begin::Brand Text--> <span class="brand-text fw-light">Orion</span> <!--end::Brand Text--> </a> <!--end::Brand Link--> </div> <!--end::Sidebar Brand--> <!--begin::Sidebar Wrapper-->
            <div class="sidebar-wrapper">
                <nav class="mt-2"> <!--begin::Sidebar Menu-->
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                        <li class="nav-item"> <a href="/dev/panel/" class="nav-link"> <i class="nav-icon bi bi-house"></i>
                                <p>
                                    Inicio
                                </p>
                            </a>
                        </li>
                        <li class="nav-item"> <a href="/" class="nav-link"> <i class="nav-icon bi bi-arrow-return-left"></i>
                                <p>
                                    Volver a Orion
                                </p>
                            </a>
                        </li>
                        <li class="nav-header">JUEGOS</li>
                        <li class="nav-item"> <a href="/dev/panel/games" class="nav-link"> <i class="nav-icon bi bi-download"></i>
                                <p>Tus juegos</p>
                            </a> </li>
                            <li class="nav-header">PAGOS</li>
                        <li class="nav-item"> <a href="/dev/panel/payment" class="nav-link"> <i class="nav-icon bi bi-download"></i>
                                <p>Configuración de pago</p>
                            </a> </li>
                        <li class="nav-item"> <a href="/dev/panel/wallet" class="nav-link"> <i class="nav-icon bi bi-download"></i>
                                <p>Saldo</p>
                            </a> </li>
                    </ul> <!--end::Sidebar Menu-->
                </nav>
            </div> <!--end::Sidebar Wrapper-->
        </aside> <!--end::Sidebar--> <!--begin::App Main-->