<?php
session_start();

// معالجة إضافة دومينات من الصفحات الأخرى
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_domains') {
        $domainsData = json_decode($_POST['domains'], true);
        if (!isset($_SESSION['checker_domains'])) {
            $_SESSION['checker_domains'] = [];
        }
        foreach ($domainsData as $domainData) {
            $_SESSION['checker_domains'][] = $domainData;
        }
        echo json_encode(['success' => true, 'count' => count($_SESSION['checker_domains'])]);
        exit;
    } elseif ($_POST['action'] === 'check_domain') {
        $domain = $_POST['domain'];
        $result = checkDomainAvailability($domain);
        echo json_encode($result);
        exit;
    } elseif ($_POST['action'] === 'clear_all') {
        $_SESSION['checker_domains'] = [];
        echo json_encode(['success' => true]);
        exit;
    }
}

// وظيفة فحص الدومين
function checkDomainAvailability($domain) {
    // إزالة .com من الدومين
    $domainName = str_replace('.com', '', $domain);
    
    // استخدام DNS lookup كطريقة بسيطة للفحص
    $available = !checkdnsrr($domainName . '.com', 'ANY');
    
    return [
        'domain' => $domain,
        'available' => $available,
        'status' => $available ? 'متاح' : 'محجوز'
    ];
}

$checkerDomains = $_SESSION['checker_domains'] ?? [];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فحص الدومينات</title>
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

        .container {
            background: rgba(15, 20, 35, 0.95);
            border-radius: 15px;
            padding: 25px;
        }

        h2 {
            color: #e94560;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
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

        .button:disabled {
            background: #555;
            cursor: not-allowed;
            transform: none;
        }

        .button-secondary {
            background: linear-gradient(135deg, #0f3460 0%, #16213e 100%);
        }

        .button-success {
            background: linear-gradient(135deg, #28a745 0%, #20893a 100%);
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

        .table-container {
            background: #1a1f2e;
            border-radius: 8px;
            overflow: hidden;
            max-height: 600px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #0f3460;
            color: #fff;
            padding: 15px;
            text-align: right;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #2a3142;
            color: #e4e4e4;
        }

        tr:hover {
            background: #252b3a;
        }

        .status-checking {
            color: #ffc107;
        }

        .status-available {
            color: #28a745;
            font-weight: 600;
        }

        .status-taken {
            color: #dc3545;
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

        .progress-bar {
            width: 100%;
            height: 30px;
            background: #1a1f2e;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #e94560, #d63651);
            width: 0%;
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .filter-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 20px;
            background: #252b3a;
            border: 2px solid #2a3142;
            border-radius: 8px;
            color: #e4e4e4;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: #e94560;
            border-color: #e94560;
            color: white;
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #a0a0a0;
        }

        .empty-state h3 {
            color: #e94560;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>✓ فحص الدومينات</h2>

        <div class="stats">
            <div class="stat-box">
                <div class="label">إجمالي الدومينات</div>
                <div class="value" id="totalCount"><?php echo count($checkerDomains); ?></div>
            </div>
            <div class="stat-box">
                <div class="label">تم فحصه</div>
                <div class="value" id="checkedCount">0</div>
            </div>
            <div class="stat-box">
                <div class="label">متاح</div>
                <div class="value" id="availableCount" style="color: #28a745;">0</div>
            </div>
            <div class="stat-box">
                <div class="label">محجوز</div>
                <div class="value" id="takenCount" style="color: #dc3545;">0</div>
            </div>
        </div>

        <div class="progress-bar">
            <div class="progress-fill" id="progressBar">0%</div>
        </div>

        <div class="controls">
            <button class="button" onclick="startChecking()" id="startBtn">بدء الفحص 🚀</button>
            <button class="button button-secondary" onclick="stopChecking()" id="stopBtn" disabled>إيقاف</button>
            <button class="button button-secondary" onclick="filterAvailable()">عرض المتاح فقط</button>
            <button class="button button-secondary" onclick="showAll()">عرض الكل</button>
            <button class="button button-success" onclick="copyAvailable()">نسخ المتاح 📋</button>
            <button class="button button-secondary" onclick="downloadCSV()">تحميل CSV 💾</button>
            <button class="button button-secondary" onclick="clearAll()">مسح الكل</button>
        </div>

        <?php if (empty($checkerDomains)): ?>
        <div class="empty-state">
            <h3>لا توجد دومينات للفحص</h3>
            <p>قم بتوليد دومينات من الصفحات الأخرى وإرسالها للفحص</p>
        </div>
        <?php else: ?>
        <div class="table-container">
            <table id="domainsTable">
                <thead>
                    <tr>
                        <th>الدومين</th>
                        <th>النوع</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($checkerDomains as $index => $domainData): ?>
                    <tr data-index="<?php echo $index; ?>" oncontextmenu="showContextMenu(event, <?php echo $index; ?>)">
                        <td class="domain-name"><?php echo htmlspecialchars($domainData['domain']); ?></td>
                        <td><?php echo htmlspecialchars($domainData['type']); ?></td>
                        <td class="status" data-status="pending">بانتظار الفحص</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <div class="context-menu" id="contextMenu">
        <div class="context-menu-item" onclick="copyDomain()">نسخ</div>
        <div class="context-menu-item" onclick="deleteDomain()">حذف</div>
        <div class="context-menu-item" onclick="checkSingleDomain()">فحص</div>
    </div>

    <script>
        let checking = false;
        let currentIndex = 0;
        let selectedRow = null;
        let results = {
            total: <?php echo count($checkerDomains); ?>,
            checked: 0,
            available: 0,
            taken: 0
        };

        // استقبال رسائل من الصفحات الأخرى
        window.addEventListener('message', function(event) {
            if (event.data.type === 'addDomains') {
                addDomainsToChecker(event.data.domains);
            }
        });

        function addDomainsToChecker(domains) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=add_domains&domains=' + encodeURIComponent(JSON.stringify(domains))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        async function startChecking() {
            if (checking) return;
            
            checking = true;
            currentIndex = 0;
            document.getElementById('startBtn').disabled = true;
            document.getElementById('stopBtn').disabled = false;

            const rows = document.querySelectorAll('#tableBody tr');
            
            for (let i = 0; i < rows.length && checking; i++) {
                currentIndex = i;
                const row = rows[i];
                const domain = row.querySelector('.domain-name').textContent;
                const statusCell = row.querySelector('.status');
                
                statusCell.textContent = 'جاري الفحص...';
                statusCell.className = 'status status-checking';
                
                try {
                    const result = await checkDomain(domain);
                    
                    results.checked++;
                    if (result.available) {
                        results.available++;
                        statusCell.textContent = 'متاح ✓';
                        statusCell.className = 'status status-available';
                        statusCell.dataset.status = 'available';
                        row.style.background = 'rgba(40, 167, 69, 0.1)';
                    } else {
                        results.taken++;
                        statusCell.textContent = 'محجوز ✗';
                        statusCell.className = 'status status-taken';
                        statusCell.dataset.status = 'taken';
                    }
                    
                    updateStats();
                    updateProgress();
                    
                } catch (error) {
                    statusCell.textContent = 'خطأ في الفحص';
                    statusCell.className = 'status';
                }
                
                // تأخير بسيط بين الفحوصات
                await new Promise(resolve => setTimeout(resolve, 500));
            }
            
            checking = false;
            document.getElementById('startBtn').disabled = false;
            document.getElementById('stopBtn').disabled = true;
        }

        async function checkDomain(domain) {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=check_domain&domain=' + encodeURIComponent(domain)
            });
            return await response.json();
        }

        function stopChecking() {
            checking = false;
            document.getElementById('startBtn').disabled = false;
            document.getElementById('stopBtn').disabled = true;
        }

        function updateStats() {
            document.getElementById('checkedCount').textContent = results.checked;
            document.getElementById('availableCount').textContent = results.available;
            document.getElementById('takenCount').textContent = results.taken;
        }

        function updateProgress() {
            const percentage = (results.checked / results.total * 100).toFixed(1);
            const progressBar = document.getElementById('progressBar');
            progressBar.style.width = percentage + '%';
            progressBar.textContent = percentage + '%';
        }

        function filterAvailable() {
            const rows = document.querySelectorAll('#tableBody tr');
            rows.forEach(row => {
                const status = row.querySelector('.status').dataset.status;
                row.style.display = status === 'available' ? '' : 'none';
            });
        }

        function showAll() {
            const rows = document.querySelectorAll('#tableBody tr');
            rows.forEach(row => {
                row.style.display = '';
            });
        }

        function copyAvailable() {
            const rows = document.querySelectorAll('#tableBody tr');
            const availableDomains = [];
            
            rows.forEach(row => {
                const status = row.querySelector('.status').dataset.status;
                if (status === 'available') {
                    availableDomains.push(row.querySelector('.domain-name').textContent);
                }
            });
            
            if (availableDomains.length > 0) {
                navigator.clipboard.writeText(availableDomains.join('\n'));
                alert('تم نسخ ' + availableDomains.length + ' دومين متاح');
            } else {
                alert('لا توجد دومينات متاحة');
            }
        }

        function downloadCSV() {
            const rows = document.querySelectorAll('#tableBody tr');
            let csv = 'Domain,Type,Status\n';
            
            rows.forEach(row => {
                const domain = row.querySelector('.domain-name').textContent;
                const type = row.cells[1].textContent;
                const status = row.querySelector('.status').textContent;
                csv += `${domain},${type},${status}\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'domain-check-results.csv';
            a.click();
        }

        function clearAll() {
            if (confirm('هل أنت متأكد من مسح جميع الدومينات؟')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=clear_all'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }

        function showContextMenu(event, index) {
            event.preventDefault();
            selectedRow = index;
            const menu = document.getElementById('contextMenu');
            menu.style.display = 'block';
            menu.style.left = event.pageX + 'px';
            menu.style.top = event.pageY + 'px';
        }

        document.addEventListener('click', function() {
            document.getElementById('contextMenu').style.display = 'none';
        });

        function copyDomain() {
            if (selectedRow !== null) {
                const row = document.querySelector(`tr[data-index="${selectedRow}"]`);
                const domain = row.querySelector('.domain-name').textContent;
                navigator.clipboard.writeText(domain);
                alert('تم نسخ: ' + domain);
            }
        }

        function deleteDomain() {
            if (selectedRow !== null) {
                const row = document.querySelector(`tr[data-index="${selectedRow}"]`);
                row.remove();
                results.total--;
                document.getElementById('totalCount').textContent = results.total;
            }
        }

        async function checkSingleDomain() {
            if (selectedRow !== null) {
                const row = document.querySelector(`tr[data-index="${selectedRow}"]`);
                const domain = row.querySelector('.domain-name').textContent;
                const statusCell = row.querySelector('.status');
                
                statusCell.textContent = 'جاري الفحص...';
                statusCell.className = 'status status-checking';
                
                try {
                    const result = await checkDomain(domain);
                    
                    if (result.available) {
                        statusCell.textContent = 'متاح ✓';
                        statusCell.className = 'status status-available';
                        statusCell.dataset.status = 'available';
                        row.style.background = 'rgba(40, 167, 69, 0.1)';
                    } else {
                        statusCell.textContent = 'محجوز ✗';
                        statusCell.className = 'status status-taken';
                        statusCell.dataset.status = 'taken';
                    }
                } catch (error) {
                    statusCell.textContent = 'خطأ في الفحص';
                    statusCell.className = 'status';
                }
            }
        }
    </script>
</body>
</html>
