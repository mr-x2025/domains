<?php
session_start();

$nicheWords = [
    'finance' => ['finance', 'loan', 'credit', 'invest', 'stock', 'etf', 'forex', 'retirement', 'business', 'consulting'],
    'crypto' => ['crypto', 'bitcoin', 'ethereum', 'defi', 'trading', 'wallet', 'nft', 'bot'],
    'realestate' => ['realestate', 'property', 'rental', 'mortgage', 'land'],
    'marketing' => ['marketing', 'ads', 'affiliate', 'seo', 'brand', 'email', 'social', 'dropship', 'ecommerce'],
    'tech' => ['hosting', 'vpn', 'saas', 'ai', 'automation', 'app', 'security', 'cyber'],
    'health' => ['weight', 'nutrition', 'fitness', 'skincare', 'health', 'mental', 'wellness'],
    'education' => ['coding', 'language', 'certification', 'course', 'freelance', 'gig']
];

$powerWords = [
    'general' => ['pro', 'expert', 'master', 'elite', 'premium', 'prime', 'plus', 'ultra', 'mega', 'turbo', 'max', 'hyper', 'smart', 'genius', 'king', 'boss', 'legend', 'guru'],
    'trust' => ['secure', 'trusted', 'safe', 'official', 'classic', 'original', 'authentic', 'certified', 'verified', 'legit', 'true', 'solid', 'gold', 'platinum'],
    'best' => ['best', 'top', 'first', 'super', 'supreme', 'ultimate', 'trending', 'buzz', 'viral'],
    'money' => ['profit', 'cash', 'money', 'rich', 'wealth', 'capital', 'gain', 'income', 'value', 'deal', 'offers', 'sale', 'savings', 'jackpot'],
    'service' => ['service', 'services', 'solution', 'support', 'care', 'clinic', 'center', 'agency', 'group', 'partners', 'experts', 'team'],
    'platform' => ['hub', 'lab', 'zone', 'world', 'planet', 'land', 'city', 'base', 'port', 'network', 'club', 'community', 'space', 'room'],
    'store' => ['shop', 'store', 'mart', 'market', 'depot', 'basket', 'box', 'kit', 'pack', 'bundle', 'outlet', 'gear', 'tools'],
    'education' => ['academy', 'school', 'college', 'institute', 'course', 'courses', 'class', 'training', 'bootcamp', 'guide', 'lessons', 'mentor', 'coach'],
    'speed' => ['speed', 'fast', 'rapid', 'turbo', 'rocket', 'boost', 'grow', 'growth', 'scale', 'surge', 'leap', 'upgrade', 'evolve'],
    'tech' => ['digital', 'online', 'cyber', 'future', 'neo', 'cloud', 'data', 'code', 'app', 'soft', 'system', 'engine', 'bot', 'ai', 'core'],
    'brand' => ['central', 'corner', 'spot', 'point', 'base', 'edge', 'spark', 'wave', 'flow', 'vision', 'focus', 'trend', 'pulse', 'vibe']
];

$domains = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedNiches = $_POST['niches'] ?? [];
    $selectedPowerGroups = $_POST['power_groups'] ?? [];
    $combination = $_POST['combination'] ?? 'niche_power';
    $maxLength = (int)($_POST['max_length'] ?? 20);
    $maxResults = (int)($_POST['max_results'] ?? 100);
    
    $selectedNicheWords = [];
    foreach ($selectedNiches as $niche) {
        if (isset($nicheWords[$niche])) {
            $selectedNicheWords = array_merge($selectedNicheWords, $nicheWords[$niche]);
        }
    }
    
    $selectedPowerWords = [];
    foreach ($selectedPowerGroups as $group) {
        if (isset($powerWords[$group])) {
            $selectedPowerWords = array_merge($selectedPowerWords, $powerWords[$group]);
        }
    }
    
    if (!empty($selectedNicheWords) && !empty($selectedPowerWords)) {
        foreach ($selectedNicheWords as $niche) {
            foreach ($selectedPowerWords as $power) {
                if ($combination === 'niche_power') {
                    $domain = $niche . $power;
                } else {
                    $domain = $power . $niche;
                }
                
                if (strlen($domain) <= $maxLength) {
                    $domains[] = strtolower($domain) . '.com';
                }
                
                if (count($domains) >= $maxResults) break 2;
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
    <title>إكزاكت ماتش دومين</title>
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

        .niche-section {
            background: #1a1f2e;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .niche-section h3 {
            color: #e94560;
            font-size: 1.1em;
            margin-bottom: 10px;
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
        <h2>🎯 توليد إكزاكت ماتش دومين</h2>
        <form method="POST">
            <div class="form-group">
                <label>اختر النيتشات (Niches):</label>
                <div class="niche-section">
                    <h3>💰 المال والاستثمار</h3>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="niches[]" value="finance" id="finance">
                            <label for="finance">المال والاستثمار</label>
                        </div>
                    </div>
                </div>
                
                <div class="niche-section">
                    <h3>₿ الكريبتو</h3>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="niches[]" value="crypto" id="crypto">
                            <label for="crypto">الكريبتو</label>
                        </div>
                    </div>
                </div>
                
                <div class="niche-section">
                    <h3>🏠 العقار</h3>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="niches[]" value="realestate" id="realestate">
                            <label for="realestate">العقار</label>
                        </div>
                    </div>
                </div>
                
                <div class="niche-section">
                    <h3>📱 التسويق</h3>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="niches[]" value="marketing" id="marketing">
                            <label for="marketing">التسويق</label>
                        </div>
                    </div>
                </div>
                
                <div class="niche-section">
                    <h3>💻 التكنولوجيا</h3>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="niches[]" value="tech" id="tech">
                            <label for="tech">التكنولوجيا</label>
                        </div>
                    </div>
                </div>
                
                <div class="niche-section">
                    <h3>⚕️ الصحة</h3>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="niches[]" value="health" id="health">
                            <label for="health">الصحة</label>
                        </div>
                    </div>
                </div>
                
                <div class="niche-section">
                    <h3>🎓 التعليم</h3>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="niches[]" value="education" id="education">
                            <label for="education">التعليم</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>اختر مجموعات الكلمات القوية (Power Words):</label>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" name="power_groups[]" value="general" id="general">
                        <label for="general">عامة</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="power_groups[]" value="trust" id="trust">
                        <label for="trust">الثقة</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="power_groups[]" value="best" id="best">
                        <label for="best">الأفضل</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="power_groups[]" value="money" id="money">
                        <label for="money">المال</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="power_groups[]" value="service" id="service">
                        <label for="service">الخدمات</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="power_groups[]" value="platform" id="platform">
                        <label for="platform">المنصات</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="power_groups[]" value="store" id="store">
                        <label for="store">المتاجر</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="power_groups[]" value="education" id="edu">
                        <label for="edu">التعليم</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="power_groups[]" value="speed" id="speed">
                        <label for="speed">السرعة</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="power_groups[]" value="tech" id="tech-power">
                        <label for="tech-power">التقنية</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="power_groups[]" value="brand" id="brand">
                        <label for="brand">العلامة التجارية</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>ترتيب الكلمات:</label>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="radio" name="combination" value="niche_power" id="niche-power" checked>
                        <label for="niche-power">كلمة النيتش + كلمة قوة</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" name="combination" value="power_niche" id="power-niche">
                        <label for="power-niche">كلمة قوة + كلمة النيتش</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>الحد الأقصى لطول الدومين:</label>
                <input type="number" name="max_length" value="20" min="5" max="30">
            </div>

            <div class="form-group">
                <label>عدد الدومينات المولدة:</label>
                <input type="number" name="max_results" value="100" min="10" max="1000">
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
                csv += domain + ',Exact Match\n';
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'exact-match-domains.csv';
            a.click();
        }

        function sendToChecker() {
            if (allDomains.length > 0) {
                window.parent.postMessage({
                    type: 'addDomains',
                    domains: allDomains.map(d => ({domain: d, type: 'Exact Match'}))
                }, '*');
                alert('تم إرسال ' + allDomains.length + ' دومين للفحص');
            }
        }
    </script>
</body>
</html>
