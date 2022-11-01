<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
      <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">CopyVendor</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Hi {{ Auth::user()->name ?? 'Admin' }},</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link @if (Request::is('dashboard')) active @endif">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item @if (Request::is('blog') || Request::is('blog/*')) menu-open @endif">
            <a href="javascript: void(0);" class="nav-link @if (Request::is('blog/*')) active @endif">
              <i class="nav-icon fas fa-newspaper"></i>
              <p>Blogs <i class="right fas fa-angle-left"></i></p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('blog.index') }}" class="nav-link @if (Request::is('blog/index')) active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Blogs</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('blog.create') }}" class="nav-link @if (Request::is('blog/create')) active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Post Blog</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item @if (Request::is('category') || Request::is('category/*')) menu-open @endif">
            <a href="javascript: void(0);" class="nav-link @if (Request::is('category/*')) active @endif">
              <i class="nav-icon fas fa-th"></i>
              <p>Categories <i class="right fas fa-angle-left"></i></p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('category.index') }}" class="nav-link @if (Request::is('category')) active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Categories</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('category.create') }}" class="nav-link @if (Request::is('category/create')) active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Category</p>
                </a>
              </li>
            </ul>
          </li>

          {{-- LOGOUT :: Start --}}
          <li class="nav-item">
            <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="fas fa-sign-out-alt"></i>
              <p>Logout</p>
            </a>
          </li>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              {{ csrf_field() }}
          </form>
          {{-- LOGOUT :: End --}}

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>