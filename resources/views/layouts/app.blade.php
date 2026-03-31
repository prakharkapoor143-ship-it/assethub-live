<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'AssetHub')</title>
    <style>
        :root {
            --bg: #f4f6f9;
            --login-bg: #d8cfbe;
            --surface: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #0f6cbd;
            --border: #e5e7eb;
            --sidebar: #111827;
            --sidebar-text: #d1d5db;
            --sidebar-active: #1f2937;
            --danger: #b91c1c;
            --success: #15803d;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Segoe UI, Tahoma, sans-serif; color: var(--text); background: var(--bg); }
        .app { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .sidebar { background: var(--sidebar); color: var(--sidebar-text); padding: 18px 12px; }
        .brand { color: #fff; text-decoration: none; font-weight: 700; font-size: 20px; display: block; padding: 8px 10px; margin-bottom: 12px; }
        .menu a { display: block; color: var(--sidebar-text); text-decoration: none; padding: 10px 12px; border-radius: 8px; margin-bottom: 6px; }
        .menu a.active, .menu a:hover { background: var(--sidebar-active); color: #fff; }
        .content { padding: 24px; }
        .top { margin-bottom: 16px; }
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 16px; }
        .stats { display: grid; gap: 12px; grid-template-columns: repeat(3, minmax(0, 1fr)); margin-bottom: 16px; }
        .stat .label { color: var(--muted); font-size: 13px; }
        .stat .value { font-size: 28px; font-weight: 700; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid var(--border); }
        th { background: #f9fafb; color: var(--muted); font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        .row { display: flex; gap: 10px; align-items: center; }
        .top-actions { display: flex; gap: 8px; align-items: center; }
        .spacer { flex: 1; }
        .btn { display: inline-block; text-decoration: none; border: 1px solid var(--border); padding: 8px 12px; border-radius: 8px; color: var(--text); background: #fff; cursor: pointer; }
        .btn.primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn.danger { background: #fff; color: var(--danger); border-color: #fecaca; }
        .form-card { max-width: 780px; }
        .field { margin-bottom: 12px; }
        .filters { display: grid; gap: 10px; grid-template-columns: repeat(4, minmax(0, 1fr)); margin-bottom: 12px; }
        .filters .actions { display: flex; gap: 8px; align-items: end; }
        label { display: block; font-size: 13px; color: var(--muted); margin-bottom: 6px; }
        input, select, textarea { width: 100%; padding: 9px 10px; border: 1px solid var(--border); border-radius: 8px; font: inherit; }
        .errors { margin-bottom: 12px; padding: 10px; border-radius: 8px; border: 1px solid #fecaca; background: #fef2f2; color: #991b1b; }
        .success { margin-bottom: 12px; padding: 10px; border-radius: 8px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534; }
        .pill { font-size: 12px; padding: 3px 8px; border-radius: 999px; display: inline-block; }
        .pill.available { background: #dcfce7; color: #166534; }
        .pill.assigned { background: #dbeafe; color: #1d4ed8; }
        .pill.maintenance { background: #fef3c7; color: #92400e; }
        .pill.retired { background: #e5e7eb; color: #374151; }
        .pagination-wrap { margin-top: 12px; }
        .pagination-wrap nav { display: flex; justify-content: end; }
        .role-chip { font-size: 12px; color: #9ca3af; text-transform: uppercase; }
        body.login-screen {
            background:
                radial-gradient(circle at 15% 20%, rgba(255, 255, 255, 0.28), transparent 35%),
                radial-gradient(circle at 85% 80%, rgba(255, 255, 255, 0.2), transparent 30%),
                var(--login-bg);
        }
        body.login-screen .app { grid-template-columns: 1fr; }
        body.login-screen .content {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
        }
        body.login-screen .top { display: none; }
        body.login-screen .form-card {
            width: min(520px, 100%);
            min-height: 560px;
            border: 1px solid rgba(212, 200, 178, 0.9);
            border-radius: 18px;
            padding: 44px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 20px 48px rgba(72, 63, 43, 0.18);
            backdrop-filter: blur(3px);
        }
        .login-brand {
            font-size: 13px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #7a6f5b;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .login-title {
            margin: 0;
            font-size: 44px;
            line-height: 1;
            color: #1e2b3f;
        }
        .login-subtitle {
            margin: 12px 0 26px;
            font-size: 15px;
            color: #6d7280;
        }
        .login-form .field {
            margin-bottom: 16px;
        }
        .login-form label {
            font-size: 14px;
            font-weight: 600;
            color: #5a6372;
            margin-bottom: 8px;
        }
        .login-form input {
            height: 50px;
            border-radius: 10px;
            border: 1px solid #d3d9e2;
            background: #fafbfd;
            padding: 12px 14px;
            transition: border-color 160ms ease, box-shadow 160ms ease;
        }
        .login-form input:focus {
            outline: none;
            border-color: #2b74c8;
            box-shadow: 0 0 0 3px rgba(43, 116, 200, 0.16);
            background: #fff;
        }
        .login-submit {
            margin-top: 10px;
            min-width: 130px;
            min-height: 46px;
            border-radius: 11px;
            font-weight: 700;
        }
        @media (max-width: 960px) {
            .app { grid-template-columns: 1fr; }
            .stats { grid-template-columns: 1fr; }
            .filters { grid-template-columns: 1fr; }
            body.login-screen .form-card { min-height: auto; padding: 32px 22px; }
            .login-title { font-size: 36px; }
        }
    </style>
</head>
<body class="{{ request()->routeIs('login') ? 'login-screen' : '' }}">
<div class="app">
    @auth
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="brand">AssetHub</a>
        <nav class="menu">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('alerts.index') }}" class="{{ request()->routeIs('alerts.*') ? 'active' : '' }}">Alerts</a>
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">Reports</a>
            <a href="{{ route('timeline.index') }}" class="{{ request()->routeIs('timeline.*') ? 'active' : '' }}">Timeline</a>
            <a href="{{ route('assets.index') }}" class="{{ request()->routeIs('assets.*') ? 'active' : '' }}">Assets</a>
            <a href="{{ route('accessories.index') }}" class="{{ request()->routeIs('accessories.*') ? 'active' : '' }}">Accessories</a>
            <a href="{{ route('consumables.index') }}" class="{{ request()->routeIs('consumables.*') ? 'active' : '' }}">Consumables</a>
            <a href="{{ route('components.index') }}" class="{{ request()->routeIs('components.*') ? 'active' : '' }}">Components</a>
            <a href="{{ route('licenses.index') }}" class="{{ request()->routeIs('licenses.*') ? 'active' : '' }}">Licenses</a>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Categories</a>
            <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">Employees</a>
            <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">Suppliers</a>
            <a href="{{ route('companies.index') }}" class="{{ request()->routeIs('companies.*') ? 'active' : '' }}">Companies</a>
            <a href="{{ route('locations.index') }}" class="{{ request()->routeIs('locations.*') ? 'active' : '' }}">Locations</a>
            @can('admin-only')
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Users</a>
            @endcan
        </nav>
    </aside>
    @endauth

    <main class="content">
        <div class="top row">
            <div>
                <h1 style="margin:0; font-size: 24px;">@yield('heading', 'AssetHub')</h1>
                @auth
                <div class="role-chip">{{ auth()->user()->role }}</div>
                @endauth
            </div>
            <div class="spacer"></div>
            <div class="top-actions">@yield('top_actions')</div>
            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn" type="submit">Logout</button>
            </form>
            @endauth
        </div>

        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
