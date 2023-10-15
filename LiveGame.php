<?php
error_reporting(E_ALL);
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Expires: ' . gmdate("D, d M Y H:i:s") . ' GMT');

$r = 150;
$c = '';
$m = array();

if (isset($_GET['frame']) && !isset($_SERVER['HTTP_CACHE_CONTROL'])) {

    $n = $_GET['frame'];

    $c = file_get_contents('c.txt', $c);
    $c = gzinflate($c);

    for ($x = 0; $x < $r; ++$x)
        for ($y = 0; $y < $r; ++$y)
            $m[$x][$y] = $c[$x * $r + $y];

} else {
    for ($x = 0; $x < $r; ++$x)
        for ($y = 0; $y < $r; ++$y)
            $m[$x][$y] = mt_rand(0, 1);
}

//

$im = imagecreate($r, $r);
$col[] = imagecolorallocate($im, 0, 0, 0);
$col[] = imagecolorallocate($im, 0, 128, 0);
$c2 = '';
$m2 = array();

for ($x = 0; $x < $r; ++$x) {
    for ($y = 0; $y < $r; ++$y) {

        $l = 0;

        for ($i = -1; $i < 2; ++$i) {
            for ($j = -1; $j < 2; ++$j) {

                if ($i == 0 && $j == 0)
                    continue;

                $xx = $x + $i;
                if ($xx == -1)
                    $xx = $r - 1;
                elseif ($xx == $r)
                    $xx = 0;

                $yy = $y + $j;
                if ($yy == -1)
                    $yy = $r - 1;
                elseif ($yy == $r)
                    $yy = 0;

                $l += $m[$xx][$yy];

            }
        }

        if ($m[$x][$y] == 0 && $l == 3)
            $m2[$x][$y] = 1;
        elseif ($m[$x][$y] == 1 && ($l < 2 || $l > 3))
            $m2[$x][$y] = 0;
        else
            $m2[$x][$y] = $m[$x][$y];

        $c2 .= $m2[$x][$y];

        if ($m2[$x][$y] == 1)
            imagesetpixel($im, $x, $y, $col[1]);

    }
}

//

$imb = imagecreate($r * 4, $r * 4);
imagecopyresized($imb, $im, 0, 0, 0, 0, $r * 4, $r * 4, $r, $r);

++$n;
header('Refresh: 0;?frame=' . $n);
header('Content-Type: image/png');

$c2 = gzdeflate($c2, 1);
file_put_contents('c.txt', $c2);

imagepng($imb, NULL, 1);