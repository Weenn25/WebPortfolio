const exprInput = document.getElementById('expression');
const exprDisplay = document.getElementById('exprDisplay');
const valueDisplay = document.getElementById('valueDisplay');

let MEM = parseFloat(localStorage.getItem('calc_mem') || '0');
let shouldClearInput = false;

// Save calculation to history if result exists
function saveToHistory(){
    const expr = exprDisplay.textContent.trim();
    const result = valueDisplay.textContent.trim();
    if(expr && result && !result.includes('Incomplete') && !result.includes('Invalid')){
        const calcHistory = JSON.parse(localStorage.getItem('calc_history') || '[]');
        const entry = `${expr} = ${result}`;
        // Don't save duplicate entries
        if(!calcHistory.includes(entry)){
            calcHistory.push(entry);
            localStorage.setItem('calc_history', JSON.stringify(calcHistory.slice(-50))); // Keep last 50
        }
    }
}

// Save to history on page load if there's a result
document.addEventListener('DOMContentLoaded', () => {
    saveToHistory();
});

function updateDisplays(){
    exprDisplay.textContent = exprInput.value || '';
    valueDisplay.textContent = '';
}

function clearEntry(){
    exprInput.value = '';
    updateDisplays();
    shouldClearInput = false;
}

if(valueDisplay.textContent.trim() !== ''){
    shouldClearInput = true;
}

function press(key){
    if(key === 'C'){
        exprInput.value = '';
        updateDisplays();
        shouldClearInput = false;
        window.location.href = BASE_URL + 'calculator?clear=1';
        return;
    }
    
    const isNumber = /^\d$/.test(key) || key === '.';
    const isOperator = /[\s+\-*/%()]|EE|\*\*/.test(key);
    const isFunction = /\(/.test(key);
    
    if(shouldClearInput && isNumber){
        exprInput.value = '';
        shouldClearInput = false;
    }
    
    if(isOperator || isFunction){
        shouldClearInput = false;
    }
    
    if(key === 'EE'){
        exprInput.value += '*10**';
    } else {
        exprInput.value = (exprInput.value || '') + key;
    }
    updateDisplays();
}

function backspace(){
    exprInput.value = (exprInput.value || '').slice(0,-1);
    updateDisplays();
}

function memory(action){
    let current = 0;
    if(valueDisplay.textContent.trim() !== ''){
        current = parseFloat(valueDisplay.textContent) || 0;
    } else if(exprInput.value.trim() !== ''){
        current = parseFloat(exprInput.value) || 0;
    }
    
    let memValue = parseFloat(localStorage.getItem('calc_mem') || '0') || 0;
    
    if(action === 'mc'){ 
        localStorage.setItem('calc_mem', '0');
        return; 
    }
    if(action === 'm+'){ 
        memValue = memValue + current;
        localStorage.setItem('calc_mem', String(memValue)); 
        MEM = memValue;
        return; 
    }
    if(action === 'm-'){ 
        memValue = memValue - current;
        localStorage.setItem('calc_mem', String(memValue)); 
        MEM = memValue;
        return; 
    }
    if(action === 'mr'){ 
        exprInput.value = String(memValue);
        shouldClearInput = false;
        updateDisplays(); 
        return; 
    }
}

function validateExpression(expr) {
    if (!expr.trim()) return { valid: false, msg: 'Empty expression' };
    
    // Remove spaces for validation
    const clean = expr.replace(/\s+/g, '');
    
    // Check for operators right after opening parenthesis
    if (/\([*/%]/.test(clean)) return { valid: false, msg: 'Invalid format' };
    
    // Check for incomplete patterns ending with operators or dots
    if (/[+\-*/%.]$/.test(clean)) return { valid: false, msg: 'Invalid format' };
    
    // Check for double dots
    if (/\.{2,}/.test(clean)) return { valid: false, msg: 'Invalid format' };
    
    // Check for operator before dot
    if (/[+\-*/%\(\s]\./.test(expr)) return { valid: false, msg: 'Invalid format' };
    
    // Check for balanced parentheses
    const openCount = (expr.match(/\(/g) || []).length;
    const closeCount = (expr.match(/\)/g) || []).length;
    if (openCount > closeCount) return { valid: false, msg: 'Incomplete expression' };
    if (closeCount > openCount) return { valid: false, msg: 'Invalid format' };
    
    // Check for empty parentheses
    if (/\(\s*\)/.test(expr)) return { valid: false, msg: 'Invalid format' };
    
    // Check for number directly followed by letter without operator
    if (/\d\s*[a-zA-Z_]/.test(expr)) {
        if (!/[\+\-*/%\(\s]\s*[a-zA-Z_]/.test(expr)) {
            return { valid: false, msg: 'Missing operator' };
        }
    }
    
    // Check for consecutive operators (but allow ** and unary minus/plus)
    const opCheck = clean.replace(/\*\*/g, '__POW__');
    if (/[+\-*/%]{2,}/.test(opCheck)) return { valid: false, msg: 'Invalid format' };
    
    return { valid: true, msg: '' };
}

function submitCalc(){
    const expr = exprInput.value.trim();
    const validation = validateExpression(expr);
    
    if (!validation.valid) {
        valueDisplay.textContent = 'Error';
        valueDisplay.style.color = '#ff6b6b';
        setTimeout(() => {
            valueDisplay.textContent = '';
            valueDisplay.style.color = '#e6eef7';
        }, 2000);
        return false;
    }
    
    // Save to history after form submission (on page reload)
    setTimeout(() => {
        saveToHistory();
    }, 100);
    
    return true;
}

function toggleHistory(){
    const calcHistory = JSON.parse(localStorage.getItem('calc_history') || '[]');
    const historyPanel = document.getElementById('historyPanel');
    const mainCalculator = document.getElementById('mainCalculator');
    const historyContent = document.getElementById('historyContent');
    
    // Clear previous items
    historyContent.innerHTML = '';
    
    if(calcHistory.length === 0){
        historyContent.innerHTML = '<div style="padding: 20px; text-align: center; color: #9aa3ad;">No calculation history yet</div>';
    } else {
        // Display history in reverse order (newest first)
        calcHistory.slice().reverse().forEach((item) => {
            const div = document.createElement('div');
            div.className = 'history-item';
            div.textContent = item;
            div.onclick = () => {
                // Parse history item to extract expression
                const expr = item.split(' = ')[0];
                exprInput.value = expr;
                updateDisplays();
                toggleHistory(); // Close panel
            };
            historyContent.appendChild(div);
        });
    }
    
    // Toggle active class to slide panel in/out with calculator rotation
    historyPanel.classList.toggle('active');
    mainCalculator.classList.toggle('slide-out');
}

function closeHistory(){
    const historyPanel = document.getElementById('historyPanel');
    const mainCalculator = document.getElementById('mainCalculator');
    historyPanel.classList.remove('active');
    mainCalculator.classList.remove('slide-out');
}

function toggleConversion(){
    const conversionPanel = document.getElementById('conversionPanel');
    const mainCalculator = document.getElementById('mainCalculator');
    
    // Initialize on first open
    if(!conversionPanel.dataset.initialized){
        initConversion();
        conversionPanel.dataset.initialized = 'true';
    }
    
    // Toggle active class to slide panel in/out with calculator rotation (opposite direction)
    conversionPanel.classList.toggle('active');
    mainCalculator.classList.toggle('slide-out-conversion');
}

function initConversion(){
    selectConvCategory('temperature');
}

function selectConvCategory(cat){
    document.getElementById('conv_category').value = cat;
    const units = {
        'temperature': [['C', 'Celsius'], ['F', 'Fahrenheit'], ['K', 'Kelvin']],
        'length': [['mm', 'Millimeter'], ['cm', 'Centimeter'], ['m', 'Meter'], ['km', 'Kilometer'], ['in', 'Inch'], ['ft', 'Foot'], ['yd', 'Yard'], ['mi', 'Mile']],
        'weight': [['mg', 'Milligram'], ['g', 'Gram'], ['kg', 'Kilogram'], ['oz', 'Ounce'], ['lb', 'Pound'], ['ton', 'Metric Ton']],
        'volume': [['ml', 'Milliliter'], ['L', 'Liter'], ['gal', 'Gallon (US)'], ['oz', 'Fluid Ounce'], ['cup', 'Cup (US)']],
        'speed': [['mps', 'Meter/Second'], ['kmh', 'Kilometer/Hour'], ['mph', 'Mile/Hour'], ['knot', 'Knot']],
        'area': [['mm2', 'Square Millimeter'], ['cm2', 'Square Centimeter'], ['m2', 'Square Meter'], ['km2', 'Square Kilometer'], ['in2', 'Square Inch'], ['ft2', 'Square Foot'], ['mi2', 'Square Mile'], ['acre', 'Acre']]
    };
    
    // Update category buttons
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
        if(btn.dataset.category === cat) btn.classList.add('active');
    });
    
    // Update unit selects
    const unitList = units[cat] || [];
    const fromSelect = document.getElementById('conv_from');
    const toSelect = document.getElementById('conv_to');
    
    fromSelect.innerHTML = '';
    toSelect.innerHTML = '';
    
    unitList.forEach((pair, idx) => {
        const opt1 = document.createElement('option');
        opt1.value = pair[0];
        opt1.textContent = pair[1];
        fromSelect.appendChild(opt1);
        
        const opt2 = document.createElement('option');
        opt2.value = pair[0];
        opt2.textContent = pair[1];
        if(idx === 1) opt2.selected = true;
        toSelect.appendChild(opt2);
    });
    
    performConversion();
}

function swapConvUnits(){
    const from = document.getElementById('conv_from');
    const to = document.getElementById('conv_to');
    const temp = from.value;
    from.value = to.value;
    to.value = temp;
    performConversion();
}

function performConversion(){
    const value = parseFloat(document.getElementById('conv_value').value) || 0;
    const category = document.getElementById('conv_category').value;
    const fromUnit = document.getElementById('conv_from').value;
    const toUnit = document.getElementById('conv_to').value;
    
    // AJAX request to controller
    fetch(BASE_URL + 'calculator/convert', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'category=' + encodeURIComponent(category) + '&from=' + encodeURIComponent(fromUnit) + '&to=' + encodeURIComponent(toUnit) + '&value=' + encodeURIComponent(value)
    })
    .then(response => response.json())
    .then(data => {
        if(data.result !== ''){
            const result = Number(data.result).toFixed(8).replace(/\.?0+$/, '');
            document.getElementById('conversionResult').style.display = 'block';
            document.getElementById('conversionResultValue').textContent = result;
            document.getElementById('conversionResultLabel').textContent = `${value} ${fromUnit} = ${result} ${toUnit}`;
        } else {
            document.getElementById('conversionResult').style.display = 'none';
        }
    })
    .catch(err => console.error('Conversion error:', err));
}

function closeConversion(){
    const conversionPanel = document.getElementById('conversionPanel');
    const mainCalculator = document.getElementById('mainCalculator');
    conversionPanel.classList.remove('active');
    mainCalculator.classList.remove('slide-out-conversion');
}

function clearHistory(){
    if(confirm('Are you sure you want to clear all calculation history?')){
        localStorage.removeItem('calc_history');
        document.getElementById('historyContent').innerHTML = '<div style="padding: 20px; text-align: center; color: #9aa3ad;">No calculation history yet</div>';
    }
}

// Close on ESC key
document.addEventListener('keydown', (e)=>{
    if(e.key === 'Escape'){
        closeHistory();
        closeConversion();
        return;
    }
    
    const allowed = '0123456789+-*/().%';
    if(allowed.includes(e.key)){
        e.preventDefault();
        press(e.key);
    } else if(e.key === 'Enter'){
        e.preventDefault();
        document.getElementById('calcForm').requestSubmit();
    } else if(e.key === 'Backspace'){
        e.preventDefault();
        backspace();
    } else if(e.key.toLowerCase() === 'c'){
        e.preventDefault();
        press('C');
    }
});

// Add event listeners for conversion inputs
document.addEventListener('DOMContentLoaded', () => {
    const convValue = document.getElementById('conv_value');
    const convFrom = document.getElementById('conv_from');
    const convTo = document.getElementById('conv_to');
    
    if(convValue){
        convValue.addEventListener('input', (e) => {
            // Remove any non-numeric characters except decimal point
            e.target.value = e.target.value.replace(/[^\d.]/g, '');
            // Prevent multiple decimal points
            if((e.target.value.match(/\./g) || []).length > 1){
                e.target.value = e.target.value.replace(/\.+/g, '.');
            }
            performConversion();
        });
        convValue.addEventListener('keypress', (e) => {
            // Stop propagation so calculator keyboard handler doesn't catch it
            e.stopPropagation();
            // Only allow numbers and decimal point
            const char = e.key;
            if(!/[\d.]/.test(char)){
                e.preventDefault();
            }
        });
        convValue.addEventListener('keydown', (e) => {
            // Stop propagation for keydown too
            e.stopPropagation();
        });
        convValue.addEventListener('keyup', (e) => {
            // Stop propagation for keyup too
            e.stopPropagation();
        });
    }
    if(convFrom) convFrom.addEventListener('change', performConversion);
    if(convTo) convTo.addEventListener('change', performConversion);
});
