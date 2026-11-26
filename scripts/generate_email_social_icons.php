<?php
/**
 * Generates self-hosted, email-safe social icons for the email templates
 * (PNG 144x144, dark obsidian squircle with colored gradient border and crisp white glyph).
 * No external CDN needed.
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
$R = (int) ($S * 0.25);

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

/**
 * Dark-filled rounded squircle (#12151E) with a diagonal brand-gradient outline.
 */
function tile(int $s, int $r, array $from, array $to, ?array $mid = null, bool $blToTr = false, int $borderThickness = 26)
{
    $im = newCanvas($s);
    imagealphablending($im, false);
    $interior = imagecolorallocate($im, 18, 21, 30);

    for ($y = 0; $y < $s; $y++) {
        for ($x = 0; $x < $s; $x++) {
            $inX = $x < $r ? $r - $x : ($x >= $s - $r ? $x - ($s - $r - 1) : 0);
            $inY = $y < $r ? $r - $y : ($y >= $s - $r ? $y - ($s - $r - 1) : 0);
            if ($inX > 0 && $inY > 0) {
                $distCorner = sqrt($inX * $inX + $inY * $inY);
                if ($distCorner > $r) {
                    continue; // outside rounded corner -> stays transparent
                }
                $edgeDist = $r - $distCorner;
            } else {
                $edgeDist = min($x, $s - 1 - $x, $y, $s - 1 - $y);
            }

            if ($edgeDist < $borderThickness) {
                $t = $blToTr
                    ? ($x + ($s - 1 - $y)) / (2 * ($s - 1)) // 0 bottom-left .. 1 top-right
                    : ($x + $y) / (2 * ($s - 1));           // 0 top-left .. 1 bottom-right
                if ($mid) {
                    $c = $t < 0.5 ? lerp($from, $mid, $t * 2) : lerp($mid, $to, ($t - 0.5) * 2);
                } else {
                    $c = lerp($from, $to, $t);
                }
                imagesetpixel($im, $x, $y, imagecolorallocate($im, $c[0], $c[1], $c[2]));
            } else {
                imagesetpixel($im, $x, $y, $interior);
            }
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

/** Draws a scaled/centred pure TikTok eighth-note. */
function drawTikTokNote($im, int $s, int $color, float $scale = 0.90, float $dx = 0, float $dy = 0): void
{
    $tmp = newCanvas($s);
    imagealphablending($tmp, false);

    $cx = $s * 0.5;
    $cy = $s * 0.5;
    $px = fn(float $nx) => (int) round($cx + ($nx - 0.5) * $s * $scale + $dx);
    $py = fn(float $ny) => (int) round($cy + ($ny - 0.5) * $s * $scale + $dy);

    $sx1 = $px(0.505);
    $sx2 = $px(0.595);
    $sy1 = $py(0.210);
    $sy2 = $py(0.630);

    $ro = (int) round($s * 0.175 * $scale);
    $ri = (int) round($s * 0.085 * $scale);
    $bx = $sx2 - $ro;
    $by = $sy2;

    imagefilledarc($tmp, $bx, $by, $ro * 2, $ro * 2, 330, 255, $color, IMG_ARC_PIE);
    imagefilledrectangle($tmp, $sx1, $sy1, $sx2, $sy2, $color);

    $rFlagIn  = (int) round($s * 0.160 * $scale);
    $rFlagOut = $rFlagIn + ($sx2 - $sx1);
    $fx = $sx2 + $rFlagIn;
    $fy = $sy1;
    imagefilledarc($tmp, $fx, $fy, $rFlagOut * 2, $rFlagOut * 2, 90, 180, $color, IMG_ARC_PIE);

    $erase = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
    imagefilledellipse($tmp, $bx, $by, $ri * 2, $ri * 2, $erase);
    imagefilledellipse($tmp, $fx, $fy, $rFlagIn * 2, $rFlagIn * 2, $erase);

    imagealphablending($im, true);
    imagecopy($im, $tmp, 0, 0, 0, 0, $s, $s);
    imagedestroy($tmp);
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

// 1. TikTok (cyan -> pink-red border + crisp white note)
$im = tile($S, $R, [37, 244, 238], [254, 44, 85]);
drawTikTokNote($im, $S, $white($im), 0.90, 0, 0);
saveIcon($im, "$outDir/tiktok.png", $S);

// 2. Instagram (yellow bottom-left -> magenta -> purple top-right border + white icon)
$im = tile($S, $R, [254, 214, 100], [131, 58, 180], [225, 48, 108], true);
glyph($im, $fa('f16d'), $brandsFont, $S * 0.48, $white($im));
saveIcon($im, "$outDir/instagram.png", $S);

// 3. X (silver -> slate border + crisp white X)
$im = tile($S, $R, [226, 232, 240], [100, 116, 139]);
if (file_exists($boldFont)) {
    glyph($im, 'X', $boldFont, $S * 0.43, $white($im));
} else {
    glyph($im, $fa('f099'), $brandsFont, $S * 0.44, $white($im));
}
saveIcon($im, "$outDir/x.png", $S);

// 4. WhatsApp (green -> emerald border + white icon)
$im = tile($S, $R, [74, 222, 128], [16, 185, 129]);
glyph($im, $fa('f232'), $brandsFont, $S * 0.50, $white($im));
saveIcon($im, "$outDir/whatsapp.png", $S);

// 5. YouTube (vibrant red border + centered white play triangle)
$im = tile($S, $R, [255, 75, 75], [210, 20, 30]);
$cx = (int) ($S * 0.525);
$cy = (int) ($S * 0.500);
$u  = (int) ($S * 0.145);
imagefilledpolygon($im, [$cx - $u, $cy - (int) ($u * 1.32), $cx - $u, $cy + (int) ($u * 1.32), $cx + (int) ($u * 1.48), $cy], 3, $white($im));
saveIcon($im, "$outDir/youtube.png", $S);

// Remaining icons in consistent dark-outlined style
// Facebook
$im = tile($S, $R, [59, 130, 246], [29, 78, 216]);
glyph($im, $fa('f39e'), $brandsFont, $S * 0.46, $white($im));
saveIcon($im, "$outDir/facebook.png", $S);

// Twitter
$im = tile($S, $R, [56, 189, 248], [2, 132, 199]);
glyph($im, $fa('f099'), $brandsFont, $S * 0.46, $white($im));
saveIcon($im, "$outDir/twitter.png", $S);

// LinkedIn
$im = tile($S, $R, [14, 165, 233], [3, 105, 161]);
glyph($im, $fa('f0e1'), $brandsFont, $S * 0.46, $white($im));
saveIcon($im, "$outDir/linkedin.png", $S);

// Telegram
$im = tile($S, $R, [56, 189, 248], [14, 116, 144]);
glyph($im, $fa('f3fe'), $brandsFont, $S * 0.47, $white($im), -$S * 0.01, $S * 0.005);
saveIcon($im, "$outDir/telegram.png", $S);

// Snapchat
$im = tile($S, $R, [253, 224, 71], [234, 179, 8]);
glyph($im, $fa('f2ac'), $brandsFont, $S * 0.48, $white($im));
saveIcon($im, "$outDir/snapchat.png", $S);

// Pinterest
$im = tile($S, $R, [244, 63, 94], [190, 18, 60]);
glyph($im, $fa('f231'), $brandsFont, $S * 0.48, $white($im));
saveIcon($im, "$outDir/pinterest.png", $S);

// Generic link (platform gold)
$im = tile($S, $R, [214, 170, 80], [176, 128, 40]);
glyph($im, $fa('f0ac'), $solidFont, $S * 0.46, $white($im));
saveIcon($im, "$outDir/link.png", $S);

echo "Done.\n";
