<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calculator_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_units() {
        return [
            'temperature' => [
                'name' => 'Temperature',
                'pairs' => [
                    ['C', 'Celsius'],
                    ['F', 'Fahrenheit'],
                    ['K', 'Kelvin']
                ]
            ],
            'length' => [
                'name' => 'Length',
                'pairs' => [
                    ['mm', 'Millimeter'],
                    ['cm', 'Centimeter'],
                    ['m', 'Meter'],
                    ['km', 'Kilometer'],
                    ['in', 'Inch'],
                    ['ft', 'Foot'],
                    ['yd', 'Yard'],
                    ['mi', 'Mile']
                ]
            ],
            'weight' => [
                'name' => 'Weight',
                'pairs' => [
                    ['mg', 'Milligram'],
                    ['g', 'Gram'],
                    ['kg', 'Kilogram'],
                    ['oz', 'Ounce'],
                    ['lb', 'Pound'],
                    ['ton', 'Metric Ton']
                ]
            ],
            'volume' => [
                'name' => 'Volume',
                'pairs' => [
                    ['ml', 'Milliliter'],
                    ['L', 'Liter'],
                    ['gal', 'Gallon (US)'],
                    ['oz', 'Fluid Ounce'],
                    ['cup', 'Cup (US)']
                ]
            ],
            'speed' => [
                'name' => 'Speed',
                'pairs' => [
                    ['mps', 'Meter/Second'],
                    ['kmh', 'Kilometer/Hour'],
                    ['mph', 'Mile/Hour'],
                    ['knot', 'Knot']
                ]
            ],
            'area' => [
                'name' => 'Area',
                'pairs' => [
                    ['mm2', 'Square Millimeter'],
                    ['cm2', 'Square Centimeter'],
                    ['m2', 'Square Meter'],
                    ['km2', 'Square Kilometer'],
                    ['in2', 'Square Inch'],
                    ['ft2', 'Square Foot'],
                    ['mi2', 'Square Mile'],
                    ['acre', 'Acre']
                ]
            ]
        ];
    }
    
    private function fact($n) {
        if (!is_numeric($n)) return NAN;
        $n = intval($n);
        if ($n < 0) return NAN;
        $res = 1;
        for ($i = 2; $i <= $n; $i++) $res *= $i;
        return $res;
    }
    
    private function sind($a) { return sin($a * M_PI / 180); }
    private function cosd($a) { return cos($a * M_PI / 180); }
    private function tand($a) { return sin($a * M_PI / 180) / cos($a * M_PI / 180); }
    private function asind($a) { return asin($a) * 180 / M_PI; }
    private function acosd($a) { return acos($a) * 180 / M_PI; }
    private function atand($a) { return atan($a) * 180 / M_PI; }
    private function root3($n) { return pow($n, 1/3); }
    
    public function convert_temperature($value, $from, $to) {
        if($from === 'C') $celsius = $value;
        elseif($from === 'F') $celsius = ($value - 32) * 5/9;
        elseif($from === 'K') $celsius = $value - 273.15;
        else return '';
        
        if($to === 'C') return $celsius;
        elseif($to === 'F') return $celsius * 9/5 + 32;
        elseif($to === 'K') return $celsius + 273.15;
        return '';
    }
    
    public function convert_length($value, $from, $to) {
        $meters = [
            'mm' => 0.001, 'cm' => 0.01, 'm' => 1, 'km' => 1000,
            'in' => 0.0254, 'ft' => 0.3048, 'yd' => 0.9144, 'mi' => 1609.34
        ];
        if(!isset($meters[$from]) || !isset($meters[$to])) return '';
        return ($value * $meters[$from]) / $meters[$to];
    }
    
    public function convert_weight($value, $from, $to) {
        $grams = [
            'mg' => 0.001, 'g' => 1, 'kg' => 1000,
            'oz' => 28.3495, 'lb' => 453.592, 'ton' => 1000000
        ];
        if(!isset($grams[$from]) || !isset($grams[$to])) return '';
        return ($value * $grams[$from]) / $grams[$to];
    }
    
    public function convert_volume($value, $from, $to) {
        $liters = [
            'ml' => 0.001, 'L' => 1, 'gal' => 3.78541, 'oz' => 0.0295735, 'cup' => 0.236588
        ];
        if(!isset($liters[$from]) || !isset($liters[$to])) return '';
        return ($value * $liters[$from]) / $liters[$to];
    }
    
    public function convert_speed($value, $from, $to) {
        $mps = [
            'mps' => 1, 'kmh' => 0.27778, 'mph' => 0.44704, 'knot' => 0.51444
        ];
        if(!isset($mps[$from]) || !isset($mps[$to])) return '';
        return ($value * $mps[$from]) / $mps[$to];
    }
    
    public function convert_area($value, $from, $to) {
        $sqm = [
            'mm2' => 0.000001, 'cm2' => 0.0001, 'm2' => 1, 'km2' => 1000000,
            'in2' => 0.00064516, 'ft2' => 0.092903, 'mi2' => 2589988, 'acre' => 4046.86
        ];
        if(!isset($sqm[$from]) || !isset($sqm[$to])) return '';
        return ($value * $sqm[$from]) / $sqm[$to];
    }
    
    public function convert($category, $from, $to, $value) {
        $value = floatval($value);
        $method = 'convert_' . $category;
        
        if (method_exists($this, $method)) {
            return $this->$method($value, $from, $to);
        }
        return '';
    }
    
    public function evaluate_expression($expr, $angle = 'rad') {
        $expr = str_replace(['×', '÷', '–', '—', 'π', '^'], ['*', '/', '-', '-', 'M_PI', '**'], $expr);
        $expr = preg_replace('/(\d+(?:\.\d+)?)%/', '($1/100)', $expr);
        
        $expr = preg_replace('/\bln\(/i', '__LN__(', $expr);
        $expr = preg_replace('/\blog\(/i', 'log10(', $expr);
        $expr = str_replace('__LN__(', 'log(', $expr);
        
        $expr = preg_replace('/\be\b/i', 'M_E', $expr);
        
        if (strtolower($angle) === 'deg') {
            $expr = preg_replace('/\bsin\(/i', 'sind(', $expr);
            $expr = preg_replace('/\bcos\(/i', 'cosd(', $expr);
            $expr = preg_replace('/\btan\(/i', 'tand(', $expr);
            $expr = preg_replace('/\basin\(/i', 'asind(', $expr);
            $expr = preg_replace('/\bacos\(/i', 'acosd(', $expr);
            $expr = preg_replace('/\batan\(/i', 'atand(', $expr);
        }
        
        $allowed = [
            'sin','cos','tan','asin','acos','atan','sqrt','log','log10','exp','abs','floor','ceil','pow','fact','root3',
            'sind','cosd','tand','asind','acosd','atand','M_PI','M_E','sinh','cosh','tanh'
        ];
        
        if (preg_match_all('/([a-zA-Z_][a-zA-Z0-9_]*)/', $expr, $m)) {
            $words = array_unique($m[1]);
            foreach ($words as $w) {
                if (!in_array($w, $allowed, true)) {
                    return 'Invalid function or token: ' . $w;
                }
            }
        }
        
        $exprCheck = str_replace('**', '__POW__', $expr);
        if (preg_match('/[+\-\*\/\%]{2,}/', preg_replace('/\s+/', '', $exprCheck))) {
            return 'Malformed expression';
        }
        
        $openCount = substr_count($expr, '(');
        $closeCount = substr_count($expr, ')');
        if ($openCount > $closeCount) {
            $expr .= str_repeat(')', $openCount - $closeCount);
        }
        
        if (substr_count($expr, '(') !== substr_count($expr, ')')) {
            return 'Incomplete expression';
        }
        
        if (preg_match('/\(\s*\)/', $expr)) {
            return 'Incomplete expression';
        }
        
        if (preg_match('/\(\s*[\*\/\%]/', $expr)) {
            return 'Incomplete expression';
        }
        
        if (preg_match('/[\+\-\*\/\%\.]$/', trim($expr))) {
            return 'Incomplete expression';
        }
        
        if (preg_match('/\.{2,}/', $expr)) {
            return 'Incomplete expression';
        }
        if (preg_match('/[\+\-\*\/\(]\s*\./', $expr)) {
            return 'Incomplete expression';
        }
        
        $result = null;
        $error_occurred = false;
        set_error_handler(function($no, $str) use (&$error_occurred) {
            $error_occurred = true;
            return true;
        });
        
        try {
            eval('$result = ' . $expr . ';');
        } catch (Throwable $e) {
            $error_occurred = true;
        }
        
        restore_error_handler();
        
        if ($error_occurred) {
            return 'Incomplete expression';
        }
        
        if ($result === null || $result === INF || $result === -INF || is_nan($result)) {
            return 'Error';
        }
        if (is_float($result)) {
            $result = rtrim(rtrim(number_format($result, 10, '.', ''), '0'), '.');
        }
        return $result;
    }
}
