<?php
session_start();

$domains = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pattern = $_POST['pattern'] ?? 'cvcvc';
    $count = (int)($_POST['count'] ?? 50);
    $allowRepeat = isset($_POST['allow_repeat']);
    
    $vowels = ['a', 'e', 'i', 'o', 'u'];
    $consonants = ['b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'z'];
    
    $generated = [];
    $attempts = 0;
    $maxAttempts = $count * 10;
    
    while (count($generated) < $count && $attempts < $maxAttempts) {
        $attempts++;
        $name = '';
        $usedChars = [];
        
        if ($pattern === 'cvcvc') {
            // نمط CVCVC
            for ($i = 0; $i < 5; $i++) {
                if ($i % 2 == 0) {
                    // حرف ساكن
                    $availableChars = $allowRepeat ? $consonants : array_diff($consonants, $usedChars);
                    if (empty($availableChars)) {
                        $availableChars = $consonants;
                    }
                    $char = $availableChars[array_rand($availableChars)];
                    $name .= $char;
                    if (!$allowRepeat) $usedChars[] = $char;
                } else {
                    // حرف متحرك
                    $availableChars = $allowRepeat ? $vowels : array_diff($vowels, $usedChars);
                    if (empty($availableChars)) {
                        $availableChars = $vowels;
                    }
                    $char = $availableChars[array_rand($availableChars)];
                    $name .= $char;
                    if (!$allowRepeat) $usedChars[] = $char;
                }
            }
        } elseif ($pattern === 'cvccv') {
            // نمط CVCCV
            $patternArray = ['C', 'V', 'C', 'C', 'V'];
            foreach ($patternArray as $type) {
                if ($type === 'C') {
                    $availableChars = $allowRepeat ? $consonants : array_diff($consonants, $usedChars);
                    if (empty($availableChars)) {
                        $availableChars = $consonants;
                    }
                    $char = $availableChars[array_rand($availableChars)];
                    $name .= $char;
                    if (!$allowRepeat) $usedChars[] = $char;
                } else {
                    $availableChars = $allowRepeat ? $vowels : array_diff($vowels, $usedChars);
                    if (empty($availableChars)) {
                        $availableChars = $vowels;
                    }
                    $char = $availableChars[array_rand($availableChars)];
                    $name .= $char;
                    if (!$allowRepeat) $usedChars[] = $char;
                }
            }
        }
        
        if (!in_array($name, $generated) && strlen($name) === 5) {
            $generated[] = $name;
        }
    }
    
    foreach ($generated as $name) {
        $domains[] = strtolower($name) . '.com';
    }
    
    $_SESSION['generated_domains'] = array_merge($_SESSION['generated_domains'] ?? [], $domains);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نمط 5 أحرف</title>
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
            padding: 20px;
        }

        .form-container {
            background: rgba(15, 20, 35, 0.95);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }

        h2 {
            color: #e94560;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #e94560;
            font-weight: 600;
        }

        input[type="number"] {
            width: 100%;
            padding: 12px;
            background: #1a1f2e;
            border: 2px solid #2a3142;
            border-radius: 8px;
            color: #e4e4e4;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #e94560;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
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
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(233, 69, 96, 0.5);
        }

        .button-secondary {
            background: linear-gradient(135deg, #0f3460 0%, #16213e 100%);
            margin-right: 10px;
        }

        .results {
            background: rgba(15, 20, 35, 0.95);
            border-radius: 15px;
            padding: 25px;
        }

        .domain-item {
            background: #252b3a;
            padding: 10px 15px;
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
            margin-bottom: 20px;
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

        .domains-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .info-box {
            background: #1a1f2e;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-right: 4px solid #e94560;
        }

        .info-box h3 {
            color: #e94560;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #a0a0a0;
            line-height: 1.6;
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1f2e;
        }

        ::-webkit-scrollbar-thumb {
            background: #e94560;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>🔤 توليد دومينات بنمط 5 أحرف</h2>
        
        <div class="info-box">
            <h3>📝 الأنماط المدعومة:</h3>
            <p>
                <strong>CVCVC:</strong> حرف ساكن - حرف متحرك - حرف ساكن - حرف متحرك - حرف ساكن (مثال: balon, tisuk)<br>
                <strong>CVCCV:</strong> حرف ساكن - حرف متحرك - حرف ساكن - حرف ساكن - حرف متحرك (مثال: belta, pista)
            </p>
            <p style="margin-top: 10px;">
                <strong>الأحرف المتحركة (Vowels):</strong> a, e, i, o, u<br>
                <strong>الأحرف الساكنة (Consonants):</strong> باقي الأحرف
            </p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>اختر النمط:</label>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="radio" name="pattern" value="cvcvc" id="cvcvc" checked>
                        <label for="cvcvc">نمط CVCVC</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="pattern" value="cvccv" id="cvccv">
                        <label for="cvccv">نمط CVCCV</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>عدد النتائج:</label>
                <input type="number" name="count" value="50" min="10" max="500">
            </div>

            <div class="form-group">
                <label>خيارات إضافية:</label>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" name="allow_repeat" id="allow-repeat">
                        <label for="allow-repeat">السماح بتكرار الأحرف</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="button">توليد الدومينات 🚀</button>
            <button type="button" class="button button-secondary" onclick="sendToChecker()">إرسال للفحص ✓</button>
            <button type="button" class="button button-secondary" onclick="copyDomains()">نسخ الكل 📋</button>
            <button type="button" class="button button-secondary" onclick="downloadCSV()">تحميل CSV 💾</button>
        </form>
    </div>

    <?php if (!empty($domains)): ?>
    <div class="results">
        <div class="stats">
            <div class="stat-box">
                <div class="label">عدد الدومينات المولدة</div>
                <div class="value"><?php echo count($domains); ?></div>
            </div>
            <div class="stat-box">
                <div class="label">النمط المستخدم</div>
                <div class="value"><?php echo strtoupper($_POST['pattern'] ?? 'CVCVC'); ?></div>
            </div>
        </div>

        <h2>النتائج:</h2>
        <div class="domains-list" id="domainsList">
            <?php foreach ($domains as $domain): ?>
                <div class="domain-item" oncontextmenu="showContextMenu(event, '<?php echo htmlspecialchars($domain); ?>')">
                    <?php echo htmlspecialchars($domain); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="context-menu" id="contextMenu">
        <div class="context-menu-item" onclick="copyDomain()">نسخ</div>
        <div class="context-menu-item" onclick="deleteDomain()">حذف</div>
        <div class="context-menu-item" onclick="selectAll()">تحديد الكل</div>
    </div>

    <script>
        let selectedDomain = null;
        let allDomains = <?php echo json_encode($domains); ?>;

        function showContextMenu(event, domain) {
            event.preventDefault();
            selectedDomain = domain;
            const menu = document.getElementById('contextMenu');
            menu.style.display = 'block';
            menu.style.left = event.pageX + 'px';
            menu.style.top = event.pageY + 'px';
        }

        document.addEventListener('click', function() {
            document.getElementById('contextMenu').style.display = 'none';
        });

        function copyDomain() {
            if (selectedDomain) {
                navigator.clipboard.writeText(selectedDomain);
                alert('تم نسخ: ' + selectedDomain);
            }
        }

        function deleteDomain() {
            if (selectedDomain) {
                const items = document.querySelectorAll('.domain-item');
                items.forEach(item => {
                    if (item.textContent.trim() === selectedDomain) {
                        item.remove();
                        allDomains = allDomains.filter(d => d !== selectedDomain);
                    }
                });
            }
        }

        function selectAll() {
            const items = document.querySelectorAll('.domain-item');
            items.forEach(item => {
                item.style.background = '#e94560';
            });
        }

        function copyDomains() {
            if (allDomains.length > 0) {
                navigator.clipboard.writeText(allDomains.join('\n'));
                alert('تم نسخ ' + allDomains.length + ' دومين');
            }
        }

        function downloadCSV() {
            if (allDomains.length === 0) return;
            
            let csv = 'Domain,Type\n';
            allDomains.forEach(domain => {
                csv += domain + ',Pattern Domain\n';
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'pattern-domains.csv';
            a.click();
        }

        function sendToChecker() {
            if (allDomains.length > 0) {
                window.parent.postMessage({
                    type: 'addDomains',
                    domains: allDomains.map(d => ({domain: d, type: 'Pattern Domain'}))
                }, '*');
                alert('تم إرسال ' + allDomains.length + ' دومين للفحص');
            }
        }
    </script>
</body>
</html>
