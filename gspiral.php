<?php
// Highlight source code and exit
if (isset($_GET['s']) == 'true')
{
    highlight_file(__FILE__);
    exit();
}

// Pass the browser the image header
header('Content-type: image/png');

// Set default width
isset($_GET['w']) ? $w = $_GET['w'] : $w = 400;

// Set default colors
isset($_GET['bg']) ? $bg = sscanf($_GET['bg'], '%2x%2x%2x') : $bg = array(255, 255, 255);
isset($_GET['arc']) ? $arc = sscanf($_GET['arc'], '%2x%2x%2x') : $arc = array(0, 0, 0);
isset($_GET['rect']) ? $rect = sscanf($_GET['rect'], '%2x%2x%2x') : $rect = array(0, 0, 0);

// Golden Mean
$gm = ((1 + sqrt(5)) / 2);

// Establish height of Golden Rectangle based on width
$h = ($w / $gm);

// Set current height of golden rectangle
$nh = $ch = $h;

// Set points for original rectangle and arc center
$x1 = 0;
$y1 = 0;
$cx = $x2 = $ch;
$cy = $y2 = $ch;

// Set starting power
$pow = 1;

// Set starting angle
$angle = 180;

// Create image (true color)
$img = imagecreatetruecolor($w + 1, $h + 1);

// Colors
$bg = imagecolorallocate($img, $bg[0], $bg[1], $bg[2]);
$arc = imagecolorallocate($img, $arc[0], $arc[1], $arc[2]);
$rect = imagecolorallocate($img, $rect[0], $rect[1], $rect[2]);

// Fill in background with white
imagefill($img, 0, 0, $bg);

// Draw original rectangle
imageRectangle($img, 0, 0, $w, $h, $rect);

// Loop through the drawing cycle until the height is less than 5
while ($ch > 1)
{
    // Calculate the next golden rectangle
    if ($pow > 1)
    {
        // New height =  (width / phi^x)
        $nh = $w / pow($gm, $pow);

        /**
         * The role of angles here makes it possible for the squares to get
         * closer and closer together in a spiral motion, which also allows
         * us to draw the logarithmic spiral itself.
         */

        switch($angle)
        {
            // 360 degrees (zero)
            case 0:
            {
                $cx = $x1 = ($x2 - $nh);
                $cy = $y1 = ($y2 + $ch);

                $x2 = ($x1 + $nh);
                $y2 = ($y1 + $nh);
                break;
            }

            // 90 degrees
            case 90:
            {
                $x1 = ($x2 - ($ch + $nh));
                $cy = $y1 = ($y2 - $nh);

                $cx = $x2 = ($x1 + $nh);
                $y2 = ($y1 + $nh);
                break;
            }

            // 180 degrees
            case 180:
            {
                $x1 = ($x2 - $ch);
                $y1 = ($y2 - ($ch + $nh));

                $cx = $x2 = ($x1 + $nh);
                $cy = $y2 = ($y1 + $nh);
                break;
            }

            // 270 degrees
            case 270:
            {
                $cx = $x1 = $x2;
                $y1 = (($y2 + $nh) - $ch);

                $x2 = ($x1 + $nh);
                $y2 = ($y1 - $nh);

                $cy = ($y2 + $nh);
                break;
            }
        }
    }

    // Draw the current rectangle to the screen
    imageRectangle($img, $x1, $y1, $x2, $y2, $rect);

    // Draw current arc to the screen
    imageArc($img, $cx, $cy, ($nh * 2), ($nh * 2), $angle, ($angle + 90), $arc);

    // Increase angle by 90 degrees (reset to zero if == 360)
    $angle = (($angle == 270) ? $angle = 0 : $angle += 90);

    // Set current height equal to new height
    $ch = $nh;

    // Increase the power
    $pow++;
}

// Display image
imagepng($img, null, 9);

// Destroy image
imagedestroy($img);
