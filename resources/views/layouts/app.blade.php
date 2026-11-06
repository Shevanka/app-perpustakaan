<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Perpustakaan Digital Kampus')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            background: #eef2f7;
            color: #334155;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Neumorphic Navigation */
        nav {
            background: #eef2f7;
            padding: 12px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            max-width: 1100px;
            margin: 20px auto 10px;
            border-radius: 20px;
            box-shadow: 8px 8px 20px rgba(166, 180, 200, 0.45), -8px -8px 20px #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.85);
        }
        nav .brand {
            background: linear-gradient(135deg, #7ca1e6, #9bb8f0);
            color: #ffffff;
            font-weight: 800;
            font-size: 15px;
            padding: 8px 18px;
            border-radius: 14px;
            box-shadow: 4px 4px 10px rgba(124, 161, 230, 0.35), -2px -2px 6px #ffffff;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.3px;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 8px;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
            align-items: center;
        }
        nav ul li a {
            color: #475569;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 13.5px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-block;
        }
        nav ul li a:hover {
            color: #2563eb;
            background: #e5ebf4;
            box-shadow: 3px 3px 7px rgba(166, 180, 200, 0.35), -3px -3px 7px #ffffff;
        }
        nav ul li a.active {
            background: #e2e8f2;
            color: #2563eb;
            font-weight: 700;
            box-shadow: inset 3px 3px 6px rgba(166, 180, 200, 0.45), inset -3px -3px 6px #ffffff;
            border-bottom: none;
        }
        nav .navbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
        }
        nav .navbar-user .user-chip {
            background: #eef2f7;
            padding: 6px 14px;
            border-radius: 12px;
            box-shadow: inset 2px 2px 5px rgba(166, 180, 200, 0.3), inset -2px -2px 5px #ffffff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #334155;
            text-decoration: none;
        }
        nav .btn-logout {
            background: #fee2e2;
            border: 1px solid rgba(248, 113, 113, 0.25);
            color: #b91c1c;
            padding: 6px 14px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            font-family: inherit;
            box-shadow: 3px 3px 7px rgba(248, 113, 113, 0.2), -3px -3px 7px #ffffff;
            transition: all 0.2s ease;
        }
        nav .btn-logout:hover {
            background: #fecaca;
            box-shadow: 4px 4px 10px rgba(248, 113, 113, 0.3), -3px -3px 8px #ffffff;
        }
        nav .btn-logout:active {
            box-shadow: inset 2px 2px 4px rgba(185, 28, 28, 0.3), inset -2px -2px 4px #ffffff;
        }

        /* Main Container */
        main {
            max-width: 1100px;
            margin: 10px auto 40px;
            padding: 20px 24px;
        }

        /* Typography */
        h1 {
            color: #1e293b;
            font-size: 26px;
            font-weight: 800;
            margin-top: 0;
            margin-bottom: 16px;
            letter-spacing: -0.3px;
        }
        h2 {
            color: #334155;
            font-size: 19px;
            font-weight: 700;
            margin-top: 28px;
            margin-bottom: 12px;
        }
        p {
            color: #64748b;
            line-height: 1.6;
            margin-top: 0;
        }

        /* Neumorphic Tables */
        table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            margin-top: 18px;
            margin-bottom: 20px;
            background: #eef2f7;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 8px 8px 20px rgba(166, 180, 200, 0.35), -8px -8px 20px #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.9);
        }
        th, td {
            padding: 13px 16px;
            text-align: left;
            border: none;
            border-bottom: 1px solid rgba(166, 180, 200, 0.2);
            font-size: 14px;
        }
        th {
            background: linear-gradient(180deg, #e4ecf7, #dae4f2);
            color: #475569;
            font-weight: 700;
            font-size: 12.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tbody tr:last-child td {
            border-bottom: none;
        }
        tbody tr {
            transition: background 0.15s ease;
        }
        tbody tr:hover {
            background: rgba(235, 243, 253, 0.7);
        }

        /* Detail Table in Cards */
        .form-container table {
            margin-top: 0;
            box-shadow: inset 2px 2px 5px rgba(166, 180, 200, 0.25), inset -2px -2px 5px #ffffff;
            border-radius: 14px;
        }
        .form-container table th {
            background: linear-gradient(180deg, #e4ecf7, #dae4f2) !important;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
            border-right: 1px solid rgba(166, 180, 200, 0.25);
            width: 170px;
        }
        .form-container table td {
            background: #eef2f7;
            font-weight: 500;
        }

        /* Table Action Buttons & Links */
        table td a {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            background: #eef2f7;
            color: #475569;
            box-shadow: 3px 3px 6px rgba(166, 180, 200, 0.35), -3px -3px 6px #ffffff;
            transition: all 0.2s ease;
            margin: 2px 2px;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        table td a:hover {
            color: #2563eb;
            box-shadow: 2px 2px 4px rgba(166, 180, 200, 0.4), -2px -2px 4px #ffffff;
            transform: translateY(-1px);
        }
        table td a:active {
            box-shadow: inset 2px 2px 4px rgba(166, 180, 200, 0.4), inset -2px -2px 4px #ffffff;
            transform: translateY(0);
        }
        table td button {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            margin: 2px 2px;
            transition: all 0.2s ease;
            border: none;
        }
        table td form button[type="submit"] {
            background: #fee2e2;
            color: #b91c1c;
            box-shadow: 3px 3px 6px rgba(248, 113, 113, 0.25), -3px -3px 6px #ffffff;
        }
        table td form button[type="submit"]:hover {
            background: #fecaca;
            transform: translateY(-1px);
        }
        table td form button[type="submit"]:active {
            box-shadow: inset 2px 2px 4px rgba(185, 28, 28, 0.3), inset -2px -2px 4px #ffffff;
            transform: translateY(0);
        }

        /* Specific Kembalikan Action Button */
        form[action*="kembalikan"] button {
            background: #dcfce7 !important;
            color: #15803d !important;
            box-shadow: 3px 3px 6px rgba(34, 197, 94, 0.25), -3px -3px 6px #ffffff !important;
        }
        form[action*="kembalikan"] button:hover {
            background: #bbf7d0 !important;
        }
        form[action*="kembalikan"] button:active {
            box-shadow: inset 2px 2px 4px rgba(21, 128, 61, 0.3), inset -2px -2px 4px #ffffff !important;
        }

        /* Neumorphic Alert */
        .alert-success {
            background: #dcfce7;
            color: #15803d;
            padding: 13px 20px;
            border-radius: 16px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid rgba(134, 239, 172, 0.6);
            box-shadow: 6px 6px 16px rgba(166, 180, 200, 0.3), -6px -6px 16px #ffffff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Neumorphic Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            background: linear-gradient(135deg, #7ca1e6, #678ed4);
            color: #ffffff;
            text-decoration: none;
            border-radius: 14px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            box-shadow: 5px 5px 12px rgba(124, 161, 230, 0.4), -5px -5px 12px #ffffff;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn:hover {
            background: linear-gradient(135deg, #8cb0f2, #759be0);
            box-shadow: 6px 6px 16px rgba(124, 161, 230, 0.45), -6px -6px 16px #ffffff;
            transform: translateY(-1px);
            color: #ffffff;
        }
        .btn:active {
            box-shadow: inset 3px 3px 6px rgba(80, 110, 170, 0.45), inset -3px -3px 6px rgba(255, 255, 255, 0.6);
            transform: translateY(0);
        }
        form.inline { display: inline; }

        /* Neumorphic Form Container */
        .form-container {
            max-width: 600px;
            background: #eef2f7;
            border-radius: 22px;
            padding: 28px 32px;
            box-shadow: 8px 8px 22px rgba(166, 180, 200, 0.4), -8px -8px 22px #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.85);
            margin-top: 18px;
            margin-bottom: 24px;
        }
        label {
            display: block;
            margin-top: 14px;
            font-weight: 700;
            font-size: 12.5px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 11px 16px;
            margin-top: 6px;
            box-sizing: border-box;
            background: #eef2f7;
            border: 1px solid rgba(166, 180, 200, 0.35);
            border-radius: 13px;
            font-size: 14px;
            font-family: inherit;
            color: #1e293b;
            box-shadow: inset 3px 3px 6px rgba(166, 180, 200, 0.35), inset -3px -3px 6px #ffffff;
            outline: none;
            transition: all 0.2s ease;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #7ca1e6;
            box-shadow: inset 2px 2px 4px rgba(124, 161, 230, 0.3), inset -2px -2px 4px #ffffff, 0 0 0 3px rgba(124, 161, 230, 0.2);
        }
        .error {
            color: #b91c1c;
            font-size: 13px;
            font-weight: 600;
            margin-top: 4px;
        }

        /* Neumorphic Status Badges */
        .badge {
            display: inline-block;
            padding: 4px 13px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .badge-dikembalikan {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid rgba(134, 239, 172, 0.8);
            box-shadow: 2px 2px 6px rgba(34, 197, 94, 0.2), -2px -2px 6px #ffffff;
        }
        .badge-dipinjam {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid rgba(253, 230, 138, 0.8);
            box-shadow: 2px 2px 6px rgba(245, 158, 11, 0.2), -2px -2px 6px #ffffff;
        }
        .badge-terlambat {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid rgba(254, 202, 202, 0.8);
            box-shadow: 2px 2px 6px rgba(239, 68, 68, 0.2), -2px -2px 6px #ffffff;
        }

        /* Checkbox List & Readonly Box */
        .checkbox-list {
            border: 1px solid rgba(166, 180, 200, 0.3);
            border-radius: 14px;
            padding: 12px 14px;
            margin-top: 6px;
            max-height: 200px;
            overflow-y: auto;
            background: #eef2f7;
            box-shadow: inset 3px 3px 6px rgba(166, 180, 200, 0.3), inset -3px -3px 6px #ffffff;
        }
        .checkbox-list label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            font-size: 13.5px;
            text-transform: none;
            letter-spacing: normal;
            color: #334155;
            margin-top: 6px;
            cursor: pointer;
        }
        .checkbox-list input[type="checkbox"] {
            width: auto;
            margin: 0;
            box-shadow: none;
            cursor: pointer;
        }
        .readonly {
            background: #eef2f7;
            padding: 11px 16px;
            border-radius: 12px;
            margin-top: 6px;
            box-shadow: inset 2px 2px 5px rgba(166, 180, 200, 0.25), inset -2px -2px 5px #ffffff;
            color: #475569;
            font-weight: 600;
        }

        /* Neumorphic Stats Grid (Dashboard) */
        .stats-grid {
            display: flex;
            gap: 22px;
            flex-wrap: wrap;
            margin-top: 22px;
            margin-bottom: 26px;
        }
        .stat-card {
            flex: 1;
            min-width: 200px;
            background: #eef2f7;
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 22px;
            padding: 26px 20px;
            text-align: center;
            box-shadow: 8px 8px 20px rgba(166, 180, 200, 0.4), -8px -8px 20px #ffffff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 10px 10px 24px rgba(166, 180, 200, 0.45), -10px -10px 24px #ffffff;
        }
        .stat-card:nth-child(1) { border-top: 4px solid #7ca1e6; }
        .stat-card:nth-child(2) { border-top: 4px solid #a78bfa; }
        .stat-card:nth-child(3) { border-top: 4px solid #fcd385; }
        .stat-card .value {
            font-size: 42px;
            font-weight: 800;
            color: #3b82f6;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.1;
        }
        .stat-card .label {
            margin-top: 10px;
            color: #64748b;
            font-size: 13.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        /* Neumorphic Pagination */
        .pagination {
            margin-top: 24px;
            margin-bottom: 16px;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }
        .pagination a {
            padding: 8px 16px;
            border-radius: 12px;
            background: #eef2f7;
            color: #475569;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            box-shadow: 4px 4px 8px rgba(166, 180, 200, 0.4), -4px -4px 8px #ffffff;
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.7);
        }
        .pagination a:hover {
            color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 5px 5px 10px rgba(166, 180, 200, 0.45), -5px -5px 10px #ffffff;
        }
        .pagination a:active {
            box-shadow: inset 2px 2px 5px rgba(166, 180, 200, 0.4), inset -2px -2px 5px #ffffff;
            transform: translateY(0);
        }
        .pagination .pagination-current {
            padding: 8px 16px;
            border-radius: 12px;
            background: #e2e8f2;
            color: #2563eb;
            font-weight: 800;
            font-size: 13px;
            box-shadow: inset 3px 3px 6px rgba(166, 180, 200, 0.45), inset -3px -3px 6px #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .pagination .pagination-disabled {
            padding: 8px 16px;
            border-radius: 12px;
            background: #eef2f7;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            box-shadow: inset 1px 1px 3px rgba(166, 180, 200, 0.2), inset -1px -1px 3px #ffffff;
            opacity: 0.65;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 26px 20px 36px;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <main>
        @include('partials.alert')

        @yield('content')
    </main>

    <footer>
        &copy; {{ date('Y') }} Sistem Perpustakaan Digital Kampus &bull; Powered by Laravel 12
    </footer>
</body>
</html>
