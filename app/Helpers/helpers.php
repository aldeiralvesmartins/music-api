<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('xdeir')) {
    /**
     * Debug profissional para Laravel / PHP
     *
     * @param mixed $data Dado a ser exibido
     * @param bool $stop Interrompe a execução se true
     * @param string $format 'json', 'php' ou 'both'
     * @param int $maxDepth Profundidade máxima de normalização
     */
    function xdeir($data, bool $stop = true, string $format = 'both', int $maxDepth = 6)
    {
        $env = function_exists('app') && app() ? app()->environment() : getenv('APP_ENV');
        if (in_array($env, ['production', 'mpProduction'])) return;

        // IDs únicos para elementos HTML
        $uid = uniqid('dbg-');
        $jsonId = $uid.'-json';
        $phpId  = $uid.'-php';
        $copyId = $uid.'-copy';

        // Backtrace
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 12);

        // Normaliza dados
        $normalized = normalize_debug_data($data, 0, $maxDepth);

        // HTML container principal
        echo '<div style="background:#1e1e1e;color:#f5f5f5;padding:18px;font-family:Consolas,Monaco,monospace;border-radius:10px;margin:24px 0;box-shadow:0 6px 16px rgba(0,0,0,0.4);max-width:95%;font-size:13px;overflow-x:auto;position:relative;">';

        // Header com botões
        echo <<<HTML
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:6px;">
            <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                <span style="color:#61afef;font-weight:bold;font-size:15px;">🐞 Debug</span>
                <span style="opacity:.7;">|</span>
HTML;

        if ($format !== 'php') {
            echo '<button onclick="document.getElementById(\''.$jsonId.'\').style.display=\'block\';document.getElementById(\''.$phpId.'\').style.display=\'none\';"
                style="background:#2c2c2c;color:#fff;border:1px solid #444;padding:6px 10px;font-size:12px;cursor:pointer;border-radius:5px;">JSON</button>';
        }
        if ($format !== 'json') {
            echo '<button onclick="document.getElementById(\''.$phpId.'\').style.display=\'block\';document.getElementById(\''.$jsonId.'\').style.display=\'none\';"
                style="background:#2c2c2c;color:#fff;border:1px solid #444;padding:6px 10px;font-size:12px;cursor:pointer;border-radius:5px;">PHP array</button>';
        }

        // Botão copiar
        echo <<<HTML
            </div>
            <button id="{$copyId}" onclick="(function(){
                var el=document.getElementById('{$jsonId}').style.display!=='none'?document.getElementById('{$jsonId}'):document.getElementById('{$phpId}');
                navigator.clipboard.writeText(el.innerText).then(function(){
                    var b=document.getElementById('{$copyId}');b.innerText='✔ Copiado';b.style.background='#28a745';
                    setTimeout(function(){b.innerText='📋 Copiar';b.style.background='#2c2c2c';},1600);
                }).catch(function(){
                    var b=document.getElementById('{$copyId}');b.innerText='Erro!';b.style.background='#e06c75';
                    setTimeout(function(){b.innerText='📋 Copiar';b.style.background='#2c2c2c';},1600);
                });
            })()"
            style="background:#2c2c2c;color:#fff;border:1px solid #444;padding:6px 14px;font-size:12px;cursor:pointer;border-radius:5px;">📋 Copiar</button>
        </div>
HTML;

        // Tipo da variável
        $type = is_object($data) ? get_class($data) : gettype($data);
        echo "<div style='margin-bottom:10px;'><strong style='color:#e5c07b;'>Tipo:</strong><span style='color:#56b6c2;'> {$type}</span></div>";

        // Backtrace detalhado
        // Backtrace detalhado (Percurso da execução) - inicia fechado
        echo "<details style='margin-bottom: 12px;'>
        <summary style='cursor:pointer;color:#c678dd;font-weight:bold;'>Percurso da execução</summary>
        <ol style='padding-left:20px;margin-top:8px;list-style:decimal;'>";
        foreach ($backtrace as $i => $trace) {
            $file = $trace['file'] ?? '[internal]';
            $line = $trace['line'] ?? '?';
            $function = $trace['function'] ?? '';
            $class = $trace['class'] ?? '';
            $typeChar = $trace['type'] ?? '';
            $highlight = $i === 0 ? "background:#3e4451;padding:6px 8px;border-radius:5px;" : "";
            echo "<li style='margin-bottom:8px;{$highlight}'><div><span style='color:#61afef;'>{$file}</span>:<span style='color:#98c379;'>{$line}</span></div><div><span style='color:#d19a66;'>&#8627; {$class}{$typeChar}{$function}</span></div></li>";
        }
        echo "</ol></details>";

        // Tempo de execução
        echo "<div style='margin-bottom: 12px; color:#d19a66;'>⏱ ".tempo_execucao()."</div>";

        // Exibição JSON e PHP array
        if ($format !== 'php') {
            $jsonHtml = highlightJson($normalized);
            echo '<div id="'.$jsonId.'" style="display:block;background:#2c2c2c;padding:14px;border-radius:7px;font-size:12px;overflow-x:auto;">'.$jsonHtml.'</div>';
        }
        if ($format !== 'json') {
            $phpHtml = highlightArray($normalized);
            echo '<div id="'.$phpId.'" style="display:none;background:#2c2c2c;padding:14px;border-radius:7px;font-size:12px;overflow-x:auto;">'.$phpHtml.'</div>';
        }

        // VarDumper para objetos complexos
        if (is_object($data) && !($data instanceof Arrayable || $data instanceof JsonResource)) {
            echo '<div style="margin-top:12px;">';
            VarDumper::dump($data);
            echo '</div>';
        }

        echo '</div>';

        if ($stop) die();
    }
}

if (!function_exists('is_http_code')) {
    function is_http_code(int $code): bool
    {
        $validStatusCodes = [
            100, 101, 102, 103,  // Informational
            200, 201, 202, 203, 204, 205, 206, 207, 208, 226, // Successful
            300, 301, 302, 303, 304, 305, 306, 307, 308,  // Redirection
            400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 427, 428, 429, 431, 451, // Client Errors
            500, 501, 502, 503, 504, 505, 506, 507, 508, 510, 511 // Server Errors
        ];

        return in_array($code, $validStatusCodes);
    }
}

/**
 * Calcula tempo de execução em ms
 */
if (!function_exists('tempo_execucao')) {
    function tempo_execucao(): string
    {
        $inicio = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);
        $fim = microtime(true);
        $total = ($fim - $inicio) * 1000;
        return number_format($total, 3) . ' ms';
    }
}

/**
 * Normaliza dados para exibição no debug
 */
if (!function_exists('normalize_debug_data')) {
    function normalize_debug_data($value, int $depth = 0, int $maxDepth = 6, array &$seen = [])
    {
        if ($depth >= $maxDepth) return '…(max-depth)…';
        if (is_null($value) || is_scalar($value)) return $value;
        if ($value instanceof \DateTimeInterface) return $value->format(\DateTime::ATOM);

        if (is_object($value)) {
            $oid = spl_object_id($value);
            if (isset($seen[$oid])) return '…(circular-ref '.get_class($value).')…';
            $seen[$oid] = true;
        }

        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) $out[$k] = normalize_debug_data($v, $depth + 1, $maxDepth, $seen);
            return $out;
        }

        if ($value instanceof JsonResource) return normalize_debug_data($value->resolve(request()), $depth + 1, $maxDepth, $seen);
        if ($value instanceof Arrayable) return normalize_debug_data($value->toArray(), $depth + 1, $maxDepth, $seen);
        if ($value instanceof \JsonSerializable) return normalize_debug_data($value->jsonSerialize(), $depth + 1, $maxDepth, $seen);
        if ($value instanceof \Traversable) {
            $out = [];
            foreach ($value as $k => $v) $out[$k] = normalize_debug_data($v, $depth + 1, $maxDepth, $seen);
            return $out;
        }

        if (is_object($value)) {
            $vars = get_object_vars($value);
            if (!$vars) return '(object) '.get_class($value);
            $out = ['__class' => get_class($value)];
            foreach ($vars as $k => $v) $out[$k] = normalize_debug_data($v, $depth + 1, $maxDepth, $seen);
            return $out;
        }

        return $value;
    }
}

/**
 * Highlight PHP array
 */
function highlightArray($data, $indent = 0, $spaceCount = 2)
{
    $html = '';
    $space = str_repeat('&nbsp;', $spaceCount * $indent);
    if (is_array($data)) {
        $html .= $space.'<span style="color:#ffffff">[</span><br>';
        foreach ($data as $k => $v) {
            $keyColor = is_string($k) ? '#61afef' : '#d19a66';
            $html .= $space.str_repeat('&nbsp;', $spaceCount).'<span style="color:'.$keyColor.';">'.htmlspecialchars((string)$k).'</span> <span style="color:#ffffff">=> </span>';
            if (is_array($v)) $html .= highlightArray($v, $indent + 1, $spaceCount);
            elseif (is_string($v)) $html .= '<span style="color:#98c379;">"'.htmlspecialchars($v).'"</span>';
            elseif (is_int($v) || is_float($v)) $html .= '<span style="color:#d19a66;">'.$v.'</span>';
            elseif (is_bool($v)) $html .= '<span style="color:#c678dd;">'.($v ? 'true' : 'false').'</span>';
            elseif (is_null($v)) $html .= '<span style="color:#7f848e;">null</span>';
            else $html .= '<span style="color:#56b6c2;">'.htmlspecialchars((string)$v).'</span>';
            $html .= ",<br>";
        }
        $html .= $space.'<span style="color:#ffffff">]</span><br>';
    } else {
        $html .= $space.htmlspecialchars((string)$data, ENT_QUOTES,'UTF-8').'<br>';
    }
    return $html;
}

/**
 * Highlight JSON
 */
function highlightJson($data, $indent = 0, $spaceCount = 2)
{
    $html = '';
    $space = str_repeat('&nbsp;', $spaceCount * $indent);
    if (is_array($data)) {
        $html .= $space.'<span style="color:#ffffff">{</span><br>';
        $lastKey = array_key_last($data);
        foreach ($data as $k => $v) {
            $keyColor = is_string($k) ? '#61afef' : '#d19a66';
            $html .= $space.str_repeat('&nbsp;', $spaceCount).'<span style="color:'.$keyColor.';">'.htmlspecialchars((string)$k).'</span><span style="color:#ffffff">: </span>';
            if (is_array($v)) $html .= highlightJson($v, $indent + 1, $spaceCount);
            elseif (is_string($v)) $html .= '<span style="color:#98c379;">"'.htmlspecialchars($v).'"</span>';
            elseif (is_int($v) || is_float($v)) $html .= '<span style="color:#d19a66;">'.$v.'</span>';
            elseif (is_bool($v)) $html .= '<span style="color:#c678dd;">'.($v ? 'true' : 'false').'</span>';
            elseif (is_null($v)) $html .= '<span style="color:#7f848e;">null</span>';
            else $html .= '<span style="color:#56b6c2;">'.htmlspecialchars((string)$v).'</span>';
            $html .= ($k !== $lastKey ? ',' : '').'<br>';
        }
        $html .= $space.'<span style="color:#ffffff">}</span><br>';
    } else {
        if (is_string($data)) $html .= $space.'<span style="color:#98c379;">"'.htmlspecialchars($data).'"</span><br>';
        elseif (is_int($data) || is_float($data)) $html .= $space.'<span style="color:#d19a66;">'.$data.'</span><br>';
        elseif (is_bool($data)) $html .= $space.'<span style="color:#c678dd;">'.($data ? 'true' : 'false').'</span><br>';
        elseif (is_null($data)) $html .= $space.'<span style="color:#7f848e;">null</span><br>';
        else $html .= $space.'<span style="color:#56b6c2;">'.htmlspecialchars((string)$data).'</span><br>';
    }
    return $html;
}
