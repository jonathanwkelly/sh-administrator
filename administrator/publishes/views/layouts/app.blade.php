@inject('config', 'scaffold.config')
@inject('module', 'scaffold.module')
@inject('template', 'scaffold.template')
@inject('breadcrumbs', 'scaffold.breadcrumbs')
@inject('navigation', 'scaffold.navigation')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>{{ strip_tags($config->get('title')) }}
    @if ($module && ($title = $module->title()))
        &raquo; {{ $title }}
    @endif
    </title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ asset($config->get('assets_path') . '/bootstrap/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- date picker -->
    <link rel="stylesheet" href="{{ asset($config->get('assets_path') . '/plugins/datepicker/datepicker3.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet"
          href="{{ asset($config->get('assets_path') . '/plugins/daterangepicker/daterangepicker-bs3.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset($config->get('assets_path') . '/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    <link rel="stylesheet" href="{{ asset($config->get('assets_path') . '/css/skins/skin-black-light.css') }}">

    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset($config->get('assets_path') . '/plugins/iCheck/square/blue.css') }}">

    <link rel="stylesheet" href="{{ asset($config->get('assets_path') . '/css/custom.css') }}">

    <script>
        window.DAM = window.DAM || {};
        window.DAM.BASE_URL = "{{ \Config::get('services.third-light.base-url') }}";
    </script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    @yield('scaffold.headjs')
</head>
<body class="hold-transition skin-black-light sidebar-mini">
<div class="wrapper">

    <!-- header logo: style can be found in header.less -->
    <header class="main-header">
        {{--
        <a href="{{ route('scaffold.dashboard') }}" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini">
                <img src="{{ asset($config->get('assets_path') . '/img/logo-sm.gif') }}" alt="">
            </span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg">
                <img src="{{ asset($config->get('assets_path') . '/img/logo.gif') }}" alt="">
            </span>
        </a>
        --}}
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                {{--@include('administrator::partials.badges')--}}
                @include($template->menu('tools'))
            </div>
        </nav>
    </header>

    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            @include($template->menu('sidebar'))
            <hr>
            <ul class="sidebar-menu">
                <li><a href="https://shopify.adgstore.com/resources/sales-dashboard"><i class="fa fa-table"></i> <span>Artwork Sales</span></a></li>
                <li><a href="https://shopify.adgstore.com/resources/tutorials"><i class="fa fa-television"></i> <span>Tutorials</span></a></li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Right side column. Contains the navbar and content of the page -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        @if ($module)
            <section class="content-header">
                @yield('scaffold.create')

                <div style="float:none; clear: left;">
                    @include($template->partials('breadcrumbs'))
                </div>

            </section>
            @endif

            @include($template->partials('messages'))

                    <!-- Main content -->
            <section class="content">

                <div class="box">
                    @yield('scaffold.filter')

                    <div class="box-body">
                        @yield('scaffold.content')
                    </div>

                    <div class="box-footer">
                        @yield('scaffold.content-footer')
                    </div>
                </div>
                <!-- /.box -->
            </section><!-- /.content -->

    </div>
    <!-- /.content-wrapper -->

    <!--
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.0
        </div>
        <strong>
            Copyright &copy; {{ date('Y')-1 }} - {{ date('Y') }}
            <a href="http://terranet.md">Terranet.md</a>.
        </strong> All rights reserved.
    </footer>
    -->
</div>
<!-- ./wrapper -->

<!-- jQuery 2.1.4 -->
<script src="{{ asset($config->get('assets_path') . '/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ asset($config->get('assets_path') . '/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- SlimScroll -->
{{--<script src="{{ asset($config->get('assets_path') . '/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>--}}
{{--<!-- FastClick -->--}}
{{--<script src="{{ asset($config->get('assets_path') . '/plugins/fastclick/fastclick.min.js') }}"></script>--}}
<!-- date-range-picker -->
<script src="{{ asset($config->get('assets_path') . '/plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset($config->get('assets_path') . '/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- date-picker -->
<script src="{{ asset($config->get('assets_path') . '/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
@if ('en' !== config('app.locale'))
<script src="{{ asset($config->get('assets_path') . '/plugins/datepicker/locales/bootstrap-datepicker.'.config("app.locale").'.js') }}"></script>
@endif

<!-- AdminLTE App -->
<script src="{{ asset($config->get('assets_path') . '/js/app.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
{{--<script src="{{ asset($config->get('assets_path') . '/js/demo.js') }}"></script>--}}

@include('vendor.administrator.scripts.product-images')

@yield('scaffold.js')
</body>
</html>
