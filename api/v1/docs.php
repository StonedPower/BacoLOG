<?php
declare(strict_types=1);

// Load JSON file
$apiDocsJson = file_get_contents(__DIR__ . '/api-docs.json');
$apiDocs = json_decode($apiDocsJson, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mini API Docs</title>
<style>
:root {
    --bg-main:#0b1220;
    --bg-panel:#020617;
    --bg-panel-hover:#0f172a;
    --border:#1e293b;
    --text-main:#e5e7eb;
}

/* Base */
body { margin:0; background:var(--bg-main); color:var(--text-main); font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif; }
.container { max-width:900px; margin:20px auto; padding:0 14px; }
.endpoint { background:var(--bg-panel); border-left:4px solid #64748b; border-radius:8px; padding:12px 14px; margin-bottom:12px; transition:0.15s; cursor:pointer; }
.endpoint:hover { background:var(--bg-panel-hover); box-shadow:0 4px 12px rgba(0,0,0,.4); }
.endpoint-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; font-size:14px; }
.method-badge { padding:3px 8px; border-radius:6px; border:1px solid var(--border); font-size:11px; display:inline-flex; align-items:center; }
.details { display:none; margin-top:10px; }
.details pre { background:#1e293b; padding:8px; border-radius:5px; font-family:monospace; white-space:pre-wrap; }
.try-btn { margin-top:8px; padding:5px 10px; border:none; background:#17a2b8; color:#fff; border-radius:4px; cursor:pointer; }
.try-btn:hover { background:#138496; }
#output { background:#1e293b; padding:10px; border-radius:5px; font-family:monospace; white-space:pre-wrap; margin-top:10px; }
</style>
<script>
function toggleDetails(endpoint) {
    const details = endpoint.querySelector('.details');
    details.style.display = details.style.display === 'block' ? 'none' : 'block';
}

function tryRequest(event, method, url) {
    event.stopPropagation();
    const output = document.getElementById('output');
    let options = { method };

    if(method==='POST' || method==='PUT'){
        options.headers = { 'Content-Type':'application/json' };
        options.body = JSON.stringify({ name:"Test User", email:"test@example.com" });
    }

    fetch(url, options)
        .then(res=>res.json())
        .then(data=>{ output.textContent = JSON.stringify(data,null,2); })
        .catch(err=>{ output.textContent = 'Error: '+err; });
}
</script>
</head>
<body>

<div class="container">
<?php foreach ($apiDocs as $endpoint): 
    $method = strtoupper($endpoint['method']);
    $badgeColor = match($method) {
        'GET' => '#28a745',
        'POST' => '#007bff',
        'PUT' => '#facc15',
        'DELETE' => '#ef4444',
        default => '#64748b'
    };
?>
<div class="endpoint" data-method="<?= htmlspecialchars($method) ?>" onclick="toggleDetails(this)">
    <div class="endpoint-header">
        <span><?= htmlspecialchars($endpoint['path']) ?></span>
        <span class="method-badge" style="background:<?= $badgeColor ?>; color:<?= $method==='PUT' ? '#000' : '#fff' ?>"><?= $method ?></span>
    </div>
    <div class="details">
        <div><strong>Description:</strong> <?= htmlspecialchars($endpoint['description']) ?></div>
        <?php if (!empty($endpoint['parameters'])): ?>
            <div><strong>Parameters:</strong> <?= htmlspecialchars(implode(', ', $endpoint['parameters'])) ?></div>
        <?php endif; ?>
        <div><strong>Response Example:</strong></div>
        <pre><?= htmlspecialchars(json_encode($endpoint['response'], JSON_PRETTY_PRINT)) ?></pre>
        <button class="try-btn" onclick="tryRequest(event,'<?= $method ?>','<?= htmlspecialchars($endpoint['try_url']) ?>')">Try It</button>
    </div>
</div>
<?php endforeach; ?>
<h2>Output:</h2>
<div id="output"></div>
</div>

</body>
</html>
