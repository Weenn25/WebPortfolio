<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Scientific PHP Calculator</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/calculator.css') ?>">
     <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
</head>
<body>
    <div class="calculator-wrapper">
        <div class="history-panel" id="historyPanel">
            <div class="history-header">
                <h3>History</h3>
                <button class="history-close-btn" onclick="toggleHistory()">&times;</button>
            </div>
            <div id="historyContent" class="history-content"></div>
            <div class="history-footer">
                <button type="button" class="history-btn" onclick="clearHistory()">Clear</button>
            </div>
        </div>

        <div class="conversion-panel" id="conversionPanel">
            <div class="conversion-header">
                <h3>Unit Converter</h3>
                <button class="conversion-close-btn" onclick="toggleConversion()">&times;</button>
            </div>
            <div class="conversion-content">
                <div class="category-selector" id="categorySelector">
                    <?php foreach($units as $key => $unit): ?>
                        <button type="button" class="category-btn <?php echo $key === 'temperature' ? 'active' : ''; ?>" 
                                onclick="selectConvCategory('<?php echo $key; ?>')"
                                data-category="<?php echo $key; ?>">
                            <?php echo $unit['name']; ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <form id="conversionForm" style="display:flex;flex-direction:column;gap:16px;padding:16px;">
                    <input type="hidden" name="conv_category" id="conv_category" value="temperature">
                    <div style="display:grid;gap:8px;">
                        <label style="font-size:0.9rem;color:#9aa3ad;font-weight:600;">Enter Value</label>
                        <input type="text" name="conv_value" id="conv_value" placeholder="0" 
                               style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.1);color:#e6eef7;padding:12px;border-radius:8px;font-size:1rem;font-family:inherit;transition:border-color 0.2s;">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:12px;align-items:end;">
                        <div style="display:grid;gap:8px;">
                            <label style="font-size:0.9rem;color:#9aa3ad;font-weight:600;">From</label>
                            <select name="conv_from" id="conv_from" 
                                    style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.1);color:#e6eef7;padding:12px;border-radius:8px;font-size:1rem;font-family:inherit;transition:border-color 0.2s;">
                            </select>
                        </div>

                        <button type="button" class="swap-btn" onclick="swapConvUnits()" 
                                style="background:rgba(255,138,0,0.2);border:1px solid rgba(255,138,0,0.3);color:#ff8a00;padding:10px;border-radius:8px;cursor:pointer;font-size:1.2rem;transition:all 0.2s;height:42px;display:flex;align-items:center;justify-content:center;">⇆</button>

                        <div style="display:grid;gap:8px;">
                            <label style="font-size:0.9rem;color:#9aa3ad;font-weight:600;">To</label>
                            <select name="conv_to" id="conv_to" 
                                    style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.1);color:#e6eef7;padding:12px;border-radius:8px;font-size:1rem;font-family:inherit;transition:border-color 0.2s;">
                            </select>
                        </div>
                    </div>
                </form>

                <div id="conversionResult" style="display:none;background:rgba(255,138,0,0.1);border:1px solid rgba(255,138,0,0.3);border-radius:8px;padding:16px;margin:16px;">
                    <div style="font-size:0.85rem;color:#9aa3ad;margin-bottom:4px;">Result:</div>
                    <div id="conversionResultValue" style="font-size:1.8rem;color:#ff8a00;font-weight:700;font-family:'Courier New',monospace;word-break:break-all;"></div>
                    <div id="conversionResultLabel" style="font-size:0.85rem;color:#9aa3ad;margin-top:8px;"></div>
                </div>
            </div>
        </div>

        <main class="calculator" id="mainCalculator" role="main" aria-label="Scientific Calculator">
        <div class="display-wrap">
            <div class="result">
                <div class="expression" id="exprDisplay"><?= htmlspecialchars($expression ?: '') ?></div>
                <div class="value" id="valueDisplay"><?= $output === '' ? '' : htmlspecialchars($output) ?></div>
            </div>
        </div>

        <form method="post" id="calcForm" autocomplete="off" onsubmit="return submitCalc()">
            <input type="hidden" name="expression" id="expression" value="<?= htmlspecialchars($expression) ?>">
            <div class="pad" role="group" aria-label="Scientific keys">
                <div class="row-span-6">
                    <button type="button" class="key small ghost" onclick="press('log10(')">log</button>
                    <button type="button" class="key small ghost" onclick="press('log(')">ln</button>
                    <button type="button" class="key small ghost" onclick="press('asin(')">asin</button>
                    <button type="button" class="key small ghost" onclick="press('acos(')">acos</button>
                    <button type="button" class="key small ghost" onclick="backspace()">⌫</button>
                    <button type="button" class="key small ghost" onclick="press('C')">C</button>
                </div>

                <button type="button" class="key small ghost" onclick="press('(')">(</button>
                <button type="button" class="key small ghost" onclick="press(')')">)</button>
                <button type="button" class="key small ghost" onclick="press('1 / ')">1/x</button>
                <button type="button" class="key small ghost" onclick="press('^2')">x²</button>
                <button type="button" class="key small ghost" onclick="press('^3')">x³</button>
                <button type="button" class="key small ghost" onclick="press('**')">xʸ</button>

                <button type="button" class="key small ghost" onclick="press('10**')">10ˣ</button>
                <button type="button" class="key small ghost" onclick="press('M_E')">e</button>
                <button type="button" class="key small ghost" onclick="press('EE')">EE</button>
                <button type="button" class="key small ghost" onclick="press('sqrt(')">√</button>
                <button type="button" class="key small ghost" onclick="press('root3(')">∛</button>
                <button type="button" class="key small ghost" onclick="press('fact(')">x!</button>

                <button type="button" class="key small ghost" onclick="press('sin(')">sin</button>
                <button type="button" class="key small ghost" onclick="press('cos(')">cos</button>
                <button type="button" class="key small ghost" onclick="press('tan(')">tan</button>
                <button type="button" class="key small ghost" onclick="press('sinh(')">sinh</button>
                <button type="button" class="key small ghost" onclick="press('cosh(')">cosh</button>
                <button type="button" class="key small ghost" onclick="press('tanh(')">tanh</button>

                <button type="button" class="key num" onclick="press('7')">7</button>
                <button type="button" class="key num" onclick="press('8')">8</button>
                <button type="button" class="key num" onclick="press('9')">9</button>
                <button type="button" class="key" onclick="press(' / ')">÷</button>
                <button type="button" class="key" onclick="press('M_PI')">π</button>
                <button type="submit" class="key op btn-equal">=</button>

                <button type="button" class="key num" onclick="press('4')">4</button>
                <button type="button" class="key num" onclick="press('5')">5</button>
                <button type="button" class="key num" onclick="press('6')">6</button>
                <button type="button" class="key" onclick="press(' * ')">×</button>
                <button type="button" class="key" onclick="press('.')">.</button>
                <button type="button" class="key" onclick="press('%')">%</button>

                <button type="button" class="key num" onclick="press('1')">1</button>
                <button type="button" class="key num" onclick="press('2')">2</button>
                <button type="button" class="key num" onclick="press('3')">3</button>
                <button type="button" class="key" onclick="press(' - ')">−</button>
                <button type="button" class="key" onclick="press(' + ')">+</button>
                <button type="button" class="key small ghost btn-rounded" onclick="clearEntry()">CE</button>

                <button type="button" class="key num" onclick="press('0')">0</button>
                <button type="button" class="key ghost key-expand" onclick="toggleHistory()">History</button>
                <button type="button" class="key ghost key-expand" onclick="toggleConversion()">Convert</button>
            </div>
        </form>

        </main>
    </div>

    <script src="<?= base_url('assets/js/calculator.js') ?>"></script>
    <script>
        // Set base URL for AJAX requests
        const BASE_URL = '<?= base_url() ?>';
    </script>
</body>
</html>
