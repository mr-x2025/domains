<?php
session_start();

// تحميل البيانات
function loadCities($file) {
    $cities = [];
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        array_shift($lines); // إزالة العنوان
        foreach ($lines as $line) {
            $parts = preg_split('/\t+/', $line);
            if (count($parts) >= 3) {
                $population = (int)str_replace(',', '', $parts[2]);
                $cities[] = [
                    'name' => trim($parts[1]),
                    'population' => $population
                ];
            }
        }
    }
    return $cities;
}

function loadUSACities($file) {
    $cities = [];
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        array_shift($lines); // إزالة العنوان
        foreach ($lines as $line) {
            $parts = preg_split('/\t+/', $line);
            if (count($parts) >= 4) {
                $population = (int)str_replace(',', '', $parts[3]);
                $cities[] = [
                    'name' => trim($parts[1]),
                    'state' => trim($parts[2]),
                    'population' => $population
                ];
            }
        }
    }
    return $cities;
}

function loadKeywords($file) {
    $keywords = [];
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        array_shift($lines); // إزالة العنوان
        foreach ($lines as $line) {
            $parts = preg_split('/\t+/', $line);
            if (count($parts) >= 3) {
                $keywords[] = [
                    'keyword' => trim($parts[1]),
                    'value' => (int)trim($parts[2])
                ];
            }
        }
    }
    return $keywords;
}

function loadEuropeanData($file) {
    $data = ['countries' => [], 'cities' => []];
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        array_shift($lines);
        
        $countries = ['France', 'Germany', 'Russia', 'Turkey', 'Ukraine', 'Italy', 'Spain', 'Poland', 
                     'Romania', 'Netherlands', 'Belgium', 'Greece', 'Czech Republic', 'Portugal', 
                     'Sweden', 'Hungary', 'Belarus', 'Austria', 'Serbia', 'Switzerland', 'Bulgaria', 
                     'Denmark', 'Finland', 'Slovakia', 'Norway', 'Ireland', 'Croatia', 'Moldova', 
                     'Bosnia and Herzegovina', 'Albania', 'Lithuania', 'Slovenia', 'Latvia', 
                     'North Macedonia', 'Estonia', 'Cyprus', 'Kosovo', 'Montenegro', 'Luxembourg', 
                     'Malta', 'Iceland', 'Andorra', 'Monaco', 'San Marino', 'Vatican City'];
        
        foreach ($lines as $line) {
            $parts = preg_split('/\t+/', $line);
            if (count($parts) >= 3) {
                $name = trim($parts[1]);
                $population = (int)str_replace(',', '', $parts[2]);
                
                if (in_array($name, $countries)) {
                    $data['countries'][] = [
                        'name' => $name,
                        'population' => $population
                    ];
                } else {
                    $data['cities'][] = [
                        'name' => $name,
                        'population' => $population
                    ];
                }
            }
        }
    }
    return $data;
}

// معالجة توليد الدومينات
$domains = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedCountries = $_POST['countries'] ?? [];
    $topCities = (int)($_POST['top_cities'] ?? 10);
    $customKeyword = trim($_POST['custom_keyword'] ?? '');
    $keywordPosition = $_POST['keyword_position'] ?? 'end';
    $topKeywords = (int)($_POST['top_keywords'] ?? 10);
    $europeMode = $_POST['europe_mode'] ?? 'cities';
    
    $keywords = loadKeywords('data/كلمات مفتاحيه جيو دومين.txt');
    $keywords = array_slice($keywords, 0, $topKeywords);
    
    foreach ($selectedCountries as $country) {
        $cities = [];
        $countryPrefix = '';
        
        switch ($country) {
            case 'USA':
                $cities = loadUSACities('data/مدن وولايات امريكا.txt');
                break;
            case 'Canada':
                $cities = loadCities('data/مدن كندا.txt');
                break;
            case 'UK':
                $cities = loadCities('data/مدن المملكه المتحده.txt');
                break;
            case 'Australia':
                $cities = loadCities('data/مدن استراليا.txt');
                break;
            case 'Europe':
                $euroData = loadEuropeanData('data/الدول الاوروبيه ومدنها.txt');
                if ($europeMode === 'countries') {
                    $cities = $euroData['countries'];
                } else {
                    $cities = $euroData['cities'];
                }
                break;
        }
        
        // ترتيب حسب عدد السكان
        usort($cities, function($a, $b) {
            return $b['population'] - $a['population'];
        });
        
        $cities = array_slice($cities, 0, $topCities);
        
        // إضافة الكلمة المفتاحية المخصصة
        if (!empty($customKeyword)) {
            foreach ($cities as $city) {
                $cityName = str_replace(' ', '', $city['name']);
                if ($keywordPosition === 'start') {
                    if (strpos($customKeyword, '@@@') !== false) {
                        $domain = str_replace('@@@', $cityName, $customKeyword);
                    } else {
                        $domain = $customKeyword . $cityName;
                    }
                } else {
                    if (strpos($customKeyword, '@@@') !== false) {
                        $domain = str_replace('@@@', $cityName, $customKeyword);
                    } else {
                        $domain = $cityName . $customKeyword;
                    }
                }
                $domains[] = strtolower($domain) . '.com';
            }
        }
        
        // توليد مع الكلمات المفتاحية من الملف
        foreach ($keywords as $kw) {
            foreach ($cities as $city) {
                $cityName = str_replace(' ', '', $city['name']);
                $keyword = $kw['keyword'];
                
                if (strpos($keyword, '@@@') !== false) {
                    $domain = str_replace('@@@', $cityName, $keyword);
                } else {
                    if ($keywordPosition === 'start') {
                        $domain = $keyword . $cityName;
                    } else {
                        $domain = $cityName . $keyword;
                    }
                }
                $domains[] = strtolower($domain) . '.com';
            }
        }
    }
    
    // إزالة التكرار
    $domains = array_unique($domains);
    
    // حفظ في الجلسة لصفحة الفحص
    $_SESSION['generated_domains'] = array_merge($_SESSION['generated_domains'] ?? [], $domains);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جيو دومين</title>
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
        <h2>🌍 توليد جيو دومين</h2>
        <form method="POST">
            <div class="form-group">
                <label>اختر الدول المستهدفة:</label>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" name="countries[]" value="USA" id="usa">
                        <label for="usa">الولايات المتحدة 🇺🇸</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="countries[]" value="Canada" id="canada">
                        <label for="canada">كندا 🇨🇦</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="countries[]" value="UK" id="uk">
                        <label for="uk">المملكة المتحدة 🇬🇧</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="countries[]" value="Australia" id="australia">
                        <label for="australia">أستراليا 🇦🇺</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="countries[]" value="Europe" id="europe">
                        <label for="europe">أوروبا 🇪🇺</label>
                    </div>
                </div>
            </div>

            <div class="form-group" id="europe-options" style="display: none;">
                <label>خيارات أوروبا:</label>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="radio" name="europe_mode" value="countries" id="euro-countries">
                        <label for="euro-countries">دولة + كلمة مفتاحية</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="europe_mode" value="cities" id="euro-cities" checked>
                        <label for="euro-cities">مدن + كلمة مفتاحية</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>عدد أكبر المدن حسب السكان:</label>
                <input type="number" name="top_cities" value="10" min="1" max="100">
            </div>

            <div class="form-group">
                <label>كلمة مفتاحية مخصصة (اختياري - استخدم @@@ للمدينة):</label>
                <input type="text" name="custom_keyword" placeholder="مثال: Visit@@@ أو @@@Hotels">
            </div>

            <div class="form-group">
                <label>موضع الكلمة المفتاحية:</label>
                <select name="keyword_position">
                    <option value="end">في النهاية</option>
                    <option value="start">في البداية</option>
                </select>
            </div>

            <div class="form-group">
                <label>عدد الكلمات المفتاحية الأعلى قيمة:</label>
                <input type="number" name="top_keywords" value="10" min="1" max="100">
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

        // إظهار خيارات أوروبا
        document.getElementById('europe').addEventListener('change', function() {
            document.getElementById('europe-options').style.display = this.checked ? 'block' : 'none';
        });

        // Context Menu
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
                csv += domain + ',Geo Domain\n';
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'geo-domains.csv';
            a.click();
        }

        function sendToChecker() {
            if (allDomains.length > 0) {
                window.parent.postMessage({
                    type: 'addDomains',
                    domains: allDomains.map(d => ({domain: d, type: 'Geo Domain'}))
                }, '*');
                alert('تم إرسال ' + allDomains.length + ' دومين للفحص');
            }
        }
    </script>
</body>
</html>
