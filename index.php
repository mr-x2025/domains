<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>برنامج إدارة وتوليد الدومينات</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #e4e4e4;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(15, 20, 35, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #0f3460 0%, #16213e 100%);
            padding: 25px;
            text-align: center;
            border-bottom: 3px solid #e94560;
        }

        .header h1 {
            color: #e94560;
            font-size: 2.2em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .tabs {
            display: flex;
            background: #0f1419;
            border-bottom: 2px solid #e94560;
            overflow-x: auto;
            gap: 5px;
            padding: 5px;
        }

        .tab {
            padding: 15px 25px;
            cursor: pointer;
            background: #1a1f2e;
            color: #a0a0a0;
            border: none;
            border-radius: 8px 8px 0 0;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
            border-bottom: 3px solid transparent;
        }

        .tab:hover {
            background: #252b3a;
            color: #e4e4e4;
        }

        .tab.active {
            background: linear-gradient(135deg, #e94560 0%, #d63651 100%);
            color: #ffffff;
            border-bottom: 3px solid #ff6b6b;
        }

        .tab-content {
            display: none;
            padding: 30px;
            animation: fadeIn 0.5s;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #e94560;
            font-weight: 600;
            font-size: 15px;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            background: #1a1f2e;
            border: 2px solid #2a3142;
            border-radius: 8px;
            color: #e4e4e4;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #e94560;
            box-shadow: 0 0 10px rgba(233, 69, 96, 0.3);
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        input[type="checkbox"],
        input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #e94560;
        }

        .button {
            background: linear-gradient(135deg, #e94560 0%, #d63651 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(233, 69, 96, 0.3);
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(233, 69, 96, 0.5);
        }

        .button:active {
            transform: translateY(0);
        }

        .button-secondary {
            background: linear-gradient(135deg, #0f3460 0%, #16213e 100%);
            margin-right: 10px;
        }

        .results-container {
            margin-top: 30px;
            background: #1a1f2e;
            border-radius: 8px;
            padding: 20px;
            max-height: 500px;
            overflow-y: auto;
        }

        .results-container h3 {
            color: #e94560;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: #0f3460;
            color: #fff;
            padding: 12px;
            text-align: right;
            font-weight: 600;
            border-bottom: 2px solid #e94560;
        }

        td {
            padding: 10px 12px;
            border-bottom: 1px solid #2a3142;
            color: #e4e4e4;
        }

        tr:hover {
            background: #252b3a;
        }

        .context-menu {
            position: fixed;
            background: #1a1f2e;
            border: 2px solid #e94560;
            border-radius: 8px;
            padding: 5px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
        }

        .context-menu-item {
            padding: 10px 20px;
            cursor: pointer;
            color: #e4e4e4;
            transition: all 0.2s ease;
        }

        .context-menu-item:hover {
            background: #e94560;
            color: #fff;
        }

        .domain-item {
            background: #252b3a;
            padding: 8px 12px;
            margin: 5px 0;
            border-radius: 5px;
            border-right: 3px solid #e94560;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .domain-item:hover {
            background: #2f3544;
            transform: translateX(-5px);
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .stat-box {
            background: #252b3a;
            padding: 15px 20px;
            border-radius: 8px;
            border-right: 4px solid #e94560;
            flex: 1;
            min-width: 150px;
        }

        .stat-box .label {
            color: #a0a0a0;
            font-size: 13px;
        }

        .stat-box .value {
            color: #e94560;
            font-size: 24px;
            font-weight: 700;
            margin-top: 5px;
        }

        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1f2e;
        }

        ::-webkit-scrollbar-thumb {
            background: #e94560;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #d63651;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #e94560;
            font-size: 18px;
        }

        .loading.show {
            display: block;
        }

        @media (max-width: 768px) {
            .tabs {
                flex-direction: column;
            }

            .tab {
                border-radius: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌐 برنامج إدارة وتوليد الدومينات</h1>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="openTab(event, 'geo-domains')">جيو دومين 🌍</button>
            <button class="tab" onclick="openTab(event, 'exact-match')">إكزاكت ماتش 🎯</button>
            <button class="tab" onclick="openTab(event, 'brand-domains')">دومين براند ✨</button>
            <button class="tab" onclick="openTab(event, 'pattern-domains')">نمط 5 أحرف 🔤</button>
            <button class="tab" onclick="openTab(event, 'checker')">فحص الدومينات ✓</button>
        </div>

        <div id="geo-domains" class="tab-content active">
            <iframe src="geo_domains.php" style="width: 100%; height: 80vh; border: none;"></iframe>
        </div>

        <div id="exact-match" class="tab-content">
            <iframe src="exact_match.php" style="width: 100%; height: 80vh; border: none;"></iframe>
        </div>

        <div id="brand-domains" class="tab-content">
            <iframe src="brand_domains.php" style="width: 100%; height: 80vh; border: none;"></iframe>
        </div>

        <div id="pattern-domains" class="tab-content">
            <iframe src="pattern_domains.php" style="width: 100%; height: 80vh; border: none;"></iframe>
        </div>

        <div id="checker" class="tab-content">
            <iframe src="checker.php" style="width: 100%; height: 80vh; border: none;"></iframe>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove('active');
            }
            
            tablinks = document.getElementsByClassName("tab");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove('active');
            }
            
            document.getElementById(tabName).classList.add('active');
            evt.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>
