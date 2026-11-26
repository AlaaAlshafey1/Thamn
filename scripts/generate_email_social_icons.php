<?php
/**
 * Generates self-hosted, email-safe social icons for the email templates
 * (PNG 144x144, rounded square, brand colours). No external CDN needed.
 *
 * Run once:  php scripts/generate_email_social_icons.php
 * Output:    public/assets/emails/social/{platform}.png
 */
$root   = dirname(__DIR__);
$outDir = $root . '/public/assets/emails/social';
if (!is_dir($outDir)) {
    mkdir($outDir, 0775, true);
}

$fontDir    = $root . '/public/assets/plugins/fontawesome-free/webfonts';
$brandsFont = $fontDir . '/fa-brands-400.ttf';
$solidFont  = $fontDir . '/fa-solid-900.ttf';
$boldFont   = 'C:/Windows/Fonts/arialbd.ttf';

foreach ([$brandsFont, $solidFont] as $f) {
    if (!file_exists($f)) {
        fwrite(STDERR, "Font not found: $f\n");
        exit(1);
    }
}

const ICON_PX = 144; // final size in px
const SS      = 4;   // supersampling factor for smooth edges
$S = ICON_PX * SS;
$R = (int) ($S * 0.235);

function newCanvas(int $s)
{
    $im = imagecreatetruecolor($s, $s);
    imagesavealpha($im, true);
    imagealphablending($im, false);
    imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));
    imagealphablending($im, true);
    return $im;
}

function lerp(array $a, array $b, float $t): array
{
    return [
        (int) ($a[0] + ($b[0] - $a[0]) * $t),
        (int) ($a[1] + ($b[1] - $a[1]) * $t),
        (int) ($a[2] + ($b[2] - $a[2]) * $t),
    ];
}

/** Rounded square filled with a diagonal gradient (and optional crisp inner border for dark tiles). */
function tile(int $s, int $r, array $from, array $to, ?array $mid = null, bool $blToTr = false, ?array $borderColor = null, int $borderThickness = 6)
{
    $im = newCanvas($s);
    imagealphablending($im, false);
    for ($y = 0; $y < $s; $y++) {
        for ($x = 0; $x < $s; $x++) {
            $inX = $x < $r ? $r - $x : ($x >= $s - $r ? $x - ($s - $r - 1) : 0);
            $inY = $y < $r ? $r - $y : ($y >= $s - $r ? $y - ($s - $r - 1) : 0);
            if ($inX > 0 && $inY > 0) {
                $distCorner = sqrt($inX * $inX + $inY * $inY);
                if ($distCorner > $r) {
                    continue; // outside the rounded corner -> stays transparent
                }
                $edgeDist = $r - $distCorner;
            } else {
                $edgeDist = min($x, $s - 1 - $x, $y, $s - 1 - $y);
            }

            if ($borderColor !== null && $edgeDist < $borderThickness) {
                imagesetpixel($im, $x, $y, imagecolorallocate($im, $borderColor[0], $borderColor[1], $borderColor[2]));
                continue;
            }

            $t = $blToTr
                ? ($x + ($s - 1 - $y)) / (2 * ($s - 1)) // 0 bottom-left .. 1 top-right
                : ($x + $y) / (2 * ($s - 1));           // 0 top-left .. 1 bottom-right
            if ($mid) {
                $c = $t < 0.5 ? lerp($from, $mid, $t * 2) : lerp($mid, $to, ($t - 0.5) * 2);
            } else {
                $c = lerp($from, $to, $t);
            }
            imagesetpixel($im, $x, $y, imagecolorallocate($im, $c[0], $c[1], $c[2]));
        }
    }
    imagealphablending($im, true);
    return $im;
}

/** Draws a glyph/text centred on the canvas using its real bounding box. */
function glyph($im, string $text, string $font, float $sizePx, int $color, float $dx = 0, float $dy = 0)
{
    $s   = imagesx($im);
    $box = imagettfbbox($sizePx, 0, $font, $text);
    $w   = $box[2] - $box[0];
    $h   = $box[1] - $box[7];
    $x   = (int) (($s - $w) / 2 - $box[0] + $dx);
    $y   = (int) (($s - $h) / 2 - $box[7] + $dy);
    imagettftext($im, $sizePx, 0, $x, $y, $color, $font, $text);
}

function saveIcon($im, string $path, int $from)
{
    $out = newCanvas(ICON_PX);
    imagealphablending($out, false);
    imagecopyresampled($out, $im, 0, 0, 0, 0, ICON_PX, ICON_PX, $from, $from);
    imagesavealpha($out, true);
    imagepng($out, $path, 9);
    imagedestroy($out);
    imagedestroy($im);
    echo 'OK  ' . basename($path) . "\n";
}

$fa    = fn(string $hex) => html_entity_decode('&#x' . $hex . ';', ENT_QUOTES, 'UTF-8');
$white = fn($im) => imagecolorallocate($im, 255, 255, 255);

// Facebook
$im = tile($S, $R, [36, 140, 255], [8, 95, 215]);
glyph($im, $fa('f39e'), $brandsFont, $S * 0.50, $white($im));
saveIcon($im, "$outDir/facebook.png", $S);

// Instagram (yellow bottom-left -> pink -> purple top-right)
$im = tile($S, $R, [254, 214, 100], [92, 82, 214], [222, 40, 120], true);
glyph($im, $fa('f16d'), $brandsFont, $S * 0.54, $white($im));
saveIcon($im, "$outDir/instagram.png", $S);

// YouTube (red tile + white play triangle)
$im = tile($S, $R, [255, 52, 52], [200, 12, 12]);
$cx = (int) ($S * 0.53);
$cy = (int) ($S * 0.50);
$u  = (int) ($S * 0.17);
imagefilledpolygon($im, [$cx - $u, $cy - (int) ($u * 1.35), $cx - $u, $cy + (int) ($u * 1.35), $cx + (int) ($u * 1.5), $cy], 3, $white($im));
saveIcon($im, "$outDir/youtube.png", $S);

// X
$im = tile($S, $R, [42, 42, 46], [8, 8, 10], null, false, [78, 78, 84], 6);
if (file_exists($boldFont)) {
    glyph($im, 'X', $boldFont, $S * 0.46, $white($im));
} else {
    glyph($im, $fa('f099'), $brandsFont, $S * 0.46, $white($im));
}
saveIcon($im, "$outDir/x.png", $S);

// TikTok (procedural eighth-note with cyan & pink-red offset layers)
function drawTikTokNote($im, int $s, int $color, float $dx = 0, float $dy = 0): void
{
    $tmp = newCanvas($s);
    imagealphablending($tmp, false);

    $sx1 = (int) round($s * 0.505 + $dx);
    $sx2 = (int) round($s * 0.595 + $dx);
    $sy1 = (int) round($s * 0.210 + $dy);
    $sy2 = (int) round($s * 0.630 + $dy);

    $ro = (int) round($s * 0.175);
    $ri = (int) round($s * 0.085);
    $bx = $sx2 - $ro;
    $by = $sy2;

    // Bowl outer hook (open at top-left-to-stem gap) + stem
    imagefilledarc($tmp, $bx, $by, $ro * 2, $ro * 2, 330, 255, $color, IMG_ARC_PIE);
    imagefilledrectangle($tmp, $sx1, $sy1, $sx2, $sy2, $color);

    // Top-right curved flag sweeping down from the stem top
    $rFlagIn  = (int) round($s * 0.160);
    $rFlagOut = $rFlagIn + ($sx2 - $sx1);
    $fx = $sx2 + $rFlagIn;
    $fy = $sy1;
    imagefilledarc($tmp, $fx, $fy, $rFlagOut * 2, $rFlagOut * 2, 90, 180, $color, IMG_ARC_PIE);

    // Punch transparent inner holes (bowl interior & flag inner curve)
    $erase = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
    imagefilledellipse($tmp, $bx, $by, $ri * 2, $ri * 2, $erase);
    imagefilledellipse($tmp, $fx, $fy, $rFlagIn * 2, $rFlagIn * 2, $erase);

    imagealphablending($im, true);
    imagecopy($im, $tmp, 0, 0, 0, 0, $s, $s);
    imagedestroy($tmp);
}

$im = tile($S, $R, [36, 36, 40], [8, 8, 10], null, false, [78, 78, 84], 6);
drawTikTokNote($im, $S, imagecolorallocate($im, 37, 244, 238), -$S * 0.022, -$S * 0.020);
drawTikTokNote($im, $S, imagecolorallocate($im, 254, 44, 85),  +$S * 0.022, +$S * 0.020);
drawTikTokNote($im, $S, $white($im), 0, 0);
saveIcon($im, "$outDir/tiktok.png", $S);

// Twitter (bird)
$im = tile($S, $R, [64, 177, 244], [29, 155, 240]);
glyph($im, $fa('f099'), $brandsFont, $S * 0.50, $white($im));
saveIcon($im, "$outDir/twitter.png", $S);

// LinkedIn
$im = tile($S, $R, [20, 135, 205], [0, 95, 160]);
glyph($im, $fa('f0e1'), $brandsFont, $S * 0.50, $white($im));
saveIcon($im, "$outDir/linkedin.png", $S);

// Telegram
$im = tile($S, $R, [55, 180, 240], [30, 150, 215]);
glyph($im, $fa('f3fe'), $brandsFont, $S * 0.52, $white($im), -$S * 0.01, $S * 0.005);
saveIcon($im, "$outDir/telegram.png", $S);

// WhatsApp
$im = tile($S, $R, [60, 220, 110], [18, 170, 80]);
glyph($im, $fa('f232'), $brandsFont, $S * 0.56, $white($im));
saveIcon($im, "$outDir/whatsapp.png", $S);

// Snapchat (yellow tile, white ghost with a thin dark outline)
$im = tile($S, $R, [255, 252, 0], [255, 230, 0]);
glyph($im, $fa('f2ac'), $brandsFont, $S * 0.56, imagecolorallocate($im, 20, 20, 20));
glyph($im, $fa('f2ac'), $brandsFont, $S * 0.50, $white($im));
saveIcon($im, "$outDir/snapchat.png", $S);

// Pinterest
$im = tile($S, $R, [235, 40, 55], [189, 8, 28]);
glyph($im, $fa('f231'), $brandsFont, $S * 0.52, $white($im));
saveIcon($im, "$outDir/pinterest.png", $S);

// Generic icon for any unknown link (globe, platform gold)
$im = tile($S, $R, [214, 170, 80], [176, 128, 40]);
glyph($im, $fa('f0ac'), $solidFont, $S * 0.52, $white($im));
saveIcon($im, "$outDir/link.png", $S);

echo "Done.\n";
