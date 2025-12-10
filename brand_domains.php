<?php
session_start();

$prefixes = ['neo', 'meta', 'hyper', 'ultra', 'giga', 'omni', 'super', 'turbo', 'pro', 'max', 'zen', 'flux', 'nova', 'apex', 'vero', 'alto', 'aero', 'dyna', 'echo', 'kilo'];
$suffixes = ['ly', 'zo', 'ora', 'ico', 'hub', 'base', 'lab', 'ify', 'ero', 'on', 'io', 'ix', 'ia', 'us', 'ent', 'ant', 'ex', 'ux', 'ax'];

$domains = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $generationType = $_POST['generation_type'] ?? 'cvcvc';
    $count = (int)($_POST['count'] ?? 50);
    $maxLength = (int)($_POST['max_length'] ?? 15);
    $excludeChars = $_POST['exclude_chars'] ?? '';
    
    $vowels = ['a', 'e', 'i', 'o', 'u'];
    $consonants = ['b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'];
    
    // إزالة الأحرف المستثناة
    if (!empty($excludeChars)) {
        $excludeArray = str_split(strtolower($excludeChars));
        $vowels = array_diff($vowels, $excludeArray);
        $consonants = array_diff($consonants, $excludeArray);
    }
    
    if ($generationType === 'cvcvc') {
        // نمط CVCVC (5 أحرف)
        for ($i = 0; $i < $count; $i++) {
            $name = '';
            $name .= $consonants[array_rand($consonants)];
            $name .= $vowels[array_rand($vowels)];
            $name .= $consonants[array_rand($consonants)];
            $name .= $vowels[array_rand($vowels)];
            $name .= $consonants[array_rand($consonants)];
            
            if (strlen($name) <= $maxLength) {
                $domains[] = strtolower($name) . '.com';
            }
        }
    } elseif ($generationType === 'compound') {
        // براند مركّب (Prefix + Suffix)
        foreach ($prefixes as $prefix) {
            foreach ($suffixes as $suffix) {
                $name = $prefix . $suffix;
                if (strlen($name) <= $maxLength) {
                    $domains[] = strtolower($name) . '.com';
                }
                if (count($domains) >= $count) break 2;
            }
        }
    } elseif ($generationType === 'random') {
        // توليد عشوائي بطول متغير
        for ($i = 0; $i < $count; $i++) {
            $length = rand(5, min(8, $maxLength));
            $name = '';
            for ($j = 0; $j < $length; $j++) {
                if ($j % 2 == 0) {
                    $name .= $consonants[array_rand($consonants)];
                } else {
                    $name .= $vowels[array_rand($vowels)];
                }
            }
            if (strlen($name) <= $maxLength) {
                $domains[] = strtolower($name) . '.com';
            }
        }
    }
    
    $domains = array_unique($domains);
    $_SESSION['generated_domains'] = array_merge($_SESSION['generated_domains'] ?? [], $domains);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دومين براند</title>
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

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px;
            background: #1a1f2e;
            border: 2px solid #2a3142;
            border-radius: 8px;
            color: #e4e4e4;
            font-size: 14px;
        }

        input:focus,
        select:focus {
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
        <h2>✨ توليد دومين براند</h2>
        
        <div class="info-box">
            <h3>📝 أنواع التوليد:</h3>
            <p>
                <strong>CVCVC:</strong> نمط 5 أحرف (حرف ساكن - حرف متحرك - حرف ساكن - حرف متحرك - حرف ساكن)<br>
                <strong>مركّب:</strong> دمج البادئات (Prefixes) مع اللواحق (Suffixes)<br>
                <strong>عشوائي:</strong> توليد عشوائي بأطوال متنوعة
            </p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>نوع التوليد:</label>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="radio" name="generation_type" value="cvcvc" id="cvcvc" checked>
                        <label for="cvcvc">نمط CVCVC (5 أحرف)</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="generation_type" value="compound" id="compound">
                        <label for="compound">براند مركّب (Prefix + Suffix)</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="generation_type" value="random" id="random">
                        <label for="random">توليد عشوائي</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>عدد الأسماء المراد توليدها:</label>
                <input type="number" name="count" value="50" min="10" max="500">
            </div>

            <div class="form-group">
                <label>الحد الأقصى للطول:</label>
                <input type="number" name="max_length" value="15" min="5" max="20">
            </div>

            <div class="form-group">
                <label>استثناء حروف معينة (مثال: xyz):</label>
                <input type="text" name="exclude_chars" placeholder="أدخل الأحرف المراد استثناؤها">
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
                <div class="label">متوسط طول الدومين</div>
                <div class="value">
                    <?php 
                    $avgLength = 0;
                    foreach ($domains as $d) {
                        $avgLength += strlen(str_replace('.com', '', $d));
                    }
                    echo round($avgLength / count($domains), 1);
                    ?>
                </div>
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
                csv += domain + ',Brand Domain\n';
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'brand-domains.csv';
            a.click();
        }

        function sendToChecker() {
            if (allDomains.length > 0) {
                window.parent.postMessage({
                    type: 'addDomains',
                    domains: allDomains.map(d => ({domain: d, type: 'Brand Domain'}))
                }, '*');
                alert('تم إرسال ' + allDomains.length + ' دومين للفحص');
            }
        }
    </script>
</body>
</html>
