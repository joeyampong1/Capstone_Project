<?php

$files = ['en', 'fil', 'es', 'fr'];

// Level-1 mojibake (UTF-8 bytes read as Windows-1252)
// Kini wala na-fix sa mb_convert_encoding kay partially-encoded ang file
$replacements = [
    // Arrows
    "\xC3\xA2\xE2\x80\xA0\xE2\x80\x99" => '→',   // â†'
    "\xC3\xA2\xE2\x80\xA0\xC2\x90"     => '⬅',   // â†'
    "\xC3\xA2\xE2\x80\xA0"             => '←',   // â† (generic)
    
    // Warning
    "\xC3\xA2\xC5\xA1\xC2\xA0\xC3\xAF\xC2\xB8\xC2\x8F" => '⚠️',   // âš ï¸
    
    // Emojis
    "\xC3\xB0\xC5\xB8\xC5\x92\xE2\x80\xA6" => '🌅',   // ðŸŒ…
    "\xC3\xB0\xC5\xB8\xC5\x92\xE2\x80\xA0" => '🌆',   // ðŸŒ†
    "\xC3\xB0\xC5\xB8\xC5\x92\xE2\x84\xA2" => '🌙',   // ðŸŒ™
    "\xC3\xB0\xC5\xB8\xE2\x80\x9C\xC2\xA8" => '🖨️',  // ðŸ–¨
    "\xC3\xB0\xC5\xB8\xC2\x90\xC2\xBE"     => '🐾',   // ðŸ ¾
    
    // Star
    "\xC3\xA2\xC2\xAD\xC2\x90" => '⭐',   // â­'
    
    // Sun
    "\xC3\xA2\xCB\x9C\xE2\x82\xAC\xC3\xAF\xC2\xB8\xC2\x8F" => '☀️',  // â˜€ï¸
    
    // Speech bubble
    "\xC3\xB0\xC5\xB8\xE2\x80\x99\xC2\xAC" => '💬',   // ðŸ'¬
    
    // Green heart
    "\xC3\xB0\xC5\xB8\xE2\x80\x99\xC5\xA1" => '💚',   // ðŸ'¡
    
    // Wave
    "\xC3\xB0\xC5\xB8\xE2\x80\x98\xE2\x80\xB9" => '👋',   // ðŸ'‹
    
    // Party
    "\xC3\xB0\xC5\xB8\xC5\xBD\xE2\x80\xB0" => '🎉',   // ðŸŽ°
    
    // Calendar
    "\xC3\xB0\xC5\xB8\xE2\x80\x9C\xE2\x80\xA6" => '📅',   // ðŸ“…"
];

foreach ($files as $locale) {
    $path = __DIR__ . "/lang/{$locale}/messages.php";
    
    if (!file_exists($path)) continue;
    
    $content = file_get_contents($path);
    $original = $content;
    $total = 0;
    
    foreach ($replacements as $bad => $good) {
        $count = substr_count($content, $bad);
        if ($count > 0) {
            $content = str_replace($bad, $good, $content);
            $total += $count;
            echo "  {$locale}: Replaced " . bin2hex($bad) . " → '{$good}' ({$count}x)\n";
        }
    }
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "✅ {$locale}: {$total} replacements\n\n";
    } else {
        echo "⏭️  {$locale}: No changes needed\n\n";
    }
}

echo "Done!\n";