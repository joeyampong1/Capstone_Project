<?php

$files = ['en', 'fil', 'es', 'fr'];

// Using hex escape sequences to avoid encoding issues in this file itself
$replacements = [
    // Peso: Ã¢â€šÂ±
    "\xC3\xA2\xE2\x82\xAC\xC5\xA1\xC2\xB1" => '₱',
    // En dash
    "\xC3\xA2\xE2\x82\xAC\xE2\x80\x9C" => '–',
    // Em dash
    "\xC3\xA2\xE2\x82\xAC\xE2\x80\x9D" => '—',
    // Bullet
    "\xC3\xA2\xE2\x82\xAC\xC2\xA2" => '•',
    // Right single quote
    "\xC3\xA2\xE2\x82\xAC\xE2\x84\xA2" => "'",
    // Left double quote
    "\xC3\xA2\xE2\x82\xAC\xC5\x93" => '"',
    // Ellipsis
    "\xC3\xA2\xE2\x82\xAC\xC2\xA6" => '…',
    // Right arrow
    "\xC3\xA2\xE2\x82\xAC\xC3\xA2\xE2\x82\xAC\xE2\x84\xA2" => '→',
];

// Simpler approach: use mb_convert_encoding to reverse double-encoding
foreach ($files as $locale) {
    $path = __DIR__ . "/lang/{$locale}/messages.php";
    
    if (!file_exists($path)) {
        echo "❌ Not found: {$locale}\n";
        continue;
    }
    
    $content = file_get_contents($path);
    $originalSize = strlen($content);
    
    // Skip if already clean
    if (strpos($content, "\xC3\xA3") === false && strpos($content, "\xC3\xA2") === false) {
        echo "⏭️  {$locale}: Already clean\n\n";
        continue;
    }
    
    // Reverse double-encoding: UTF-8 bytes that were interpreted as Windows-1252
    $fixed = mb_convert_encoding($content, 'Windows-1252', 'UTF-8');
    
    $fixedSize = strlen($fixed);
    
    // Safety check
    if ($fixed === $content) {
        echo "⚠️  {$locale}: No change\n\n";
        continue;
    }
    
    if ($fixedSize >= $originalSize) {
        echo "⚠️  {$locale}: Size didn't reduce ({$originalSize}b → {$fixedSize}b) — skipping\n\n";
        continue;
    }
    
    // Validate result is still valid PHP
    $testParse = @eval('return true; ?>' . $fixed);
    
    file_put_contents($path, $fixed);
    echo "✅ {$locale}: Fixed! ({$originalSize}b → {$fixedSize}b, saved " . ($originalSize - $fixedSize) . " bytes)\n\n";
}

echo "=== Final Scan ===\n";
foreach ($files as $locale) {
    $content = file_get_contents(__DIR__ . "/lang/{$locale}/messages.php");
    $hasMoji = (strpos($content, "\xC3\xA3") !== false || strpos($content, "\xC3\xA2\xE2\x82\xAC") !== false);
    echo "{$locale}: " . ($hasMoji ? "⚠️  Still has mojibake" : "✅ Clean") . "\n";
}

echo "\nDone!\n";