<?php
function make_axis_scale($xmin, $xmax) {
  $dx = $xmax - $xmin;

  // figure out overall scale
  if ($dx<10) {
    $scale = 1;
  } else {
    $scale = pow(10,floor(log10($dx)));
  }
  $dxs = $dx/$scale;
  $xsmin = $xmin/$scale;
  $xsmax = $xmax/$scale;

  // choose the tick step
  if ($dxs<2) {
    $step = 0.2;
  } else if ($dxs<5) {
    $step = 0.5;
  } else {
    $step = 1;
  }

  // set sup the range and ticks
  $x0 = floor($xsmin/$step)*$step;
  $a = array();
  $a[] = $x0*$scale;
  $x1 = $x0;
  while ($x1<$xsmax) {
    $x1 += $step;
    $a[] = $x1*$scale;
  }

  return $a;
}

function draw_frame($im,$font,$u0,$v0,$u1,$v1,$color,$ticker_data) {

  // draw frame
  imagesetthickness($im,3);
  imageline($im,$u0,$v0,$u1,$v0,$color);
  imageline($im,$u1,$v0,$u1,$v1,$color);
  imageline($im,$u1,$v1,$u0,$v1,$color);
  imageline($im,$u0,$v1,$u0,$v0,$color);
  imagesetthickness($im,1);

  // determine xmin, xmax
  $xmin = 1e9; $xmax = 0;
  foreach ($ticker_data as $line) {
    $data = explode(",",$line);
    for ($i = 1; $i<=4; ++$i) {
      $value = floatval($data[$i]);
      if ($xmin>$value) $xmin = $value;
      if ($xmax<$value) $xmax = $value;
    }
  }

  // draw horizontal grid
  imagesetstyle($im,array($color,$color,$color,$color,IMG_COLOR_TRANSPARENT,IMG_COLOR_TRANSPARENT,IMG_COLOR_TRANSPARENT,IMG_COLOR_TRANSPARENT));
  $yticks = make_axis_scale($xmin,$xmax);
  $tick0 = $yticks[0]; $tick1 = end($yticks);
  foreach ($yticks as $tick) {
    if ($tick!=$tick0 && $tick!=$tick1) {
      $v = $v0 + ($v1-$v0)*($tick-$tick0)/($tick1-$tick0);
      imageline($im,$u0,$v,$u1,$v,IMG_COLOR_STYLED);
    }
  }

  // draw labels
  foreach ($yticks as $tick) {
    $v = $v0 + ($v1-$v0)*($tick-$tick0)/($tick1-$tick0);
    $label = strval($tick);
    $textwidth = strlen($label)*8;
    imagestring($im,$font,$u0-$textwidth-6,$v-7,$label,$color);
  }

  return array($tick0, $tick1);
}

function draw_dates($im,$font,$u0,$v0,$u1,$v1,$color,$ticker_data) {

  // select first days of the week
  $xticks = array();
  $n = 1;
  $previous_dayofweek = -1;
  foreach ($ticker_data as $line) {
    $date = substr($line,0,10);
    $dayofweek = date("w",strtotime($date));
    if (($previous_dayofweek>=0 && $dayofweek<$previous_dayofweek) || ($previous_dayofweek<0 && $dayofweek==1)) {
     $xticks[] = $n;
    }
    $previous_dayofweek = $dayofweek;
    ++$n;
  }
  $tick0 = 0; $tick1 = $n;

  // draw vertical grid
  imagesetstyle($im,array($color,$color,$color,$color,IMG_COLOR_TRANSPARENT,IMG_COLOR_TRANSPARENT,IMG_COLOR_TRANSPARENT,IMG_COLOR_TRANSPARENT));
  foreach ($xticks as $tick) {
    $u = $u0 + ($u1-$u0)*($tick-$tick0)/($tick1-$tick0);
    imageline($im,$u,$v0,$u,$v1,IMG_COLOR_STYLED);
  }

  // draw labels
  foreach ($xticks as $tick) {
    $u = $u0 + ($u1-$u0)*($tick-$tick0)/($tick1-$tick0);
    $date = substr($ticker_data[$tick-1],0,10);
    $label = date("M j",strtotime($date));
    $textwidth = strlen($label)*8;
    imagestring($im,$font,$u-$textwidth/2,$v0+12,$label,$color);
  }

  return array($tick0, $tick1);
}

function draw_points($im,$u0,$v0,$u1,$v1,$color,$fillcolor,$xrange,$yrange,$ticker_data) {
  $n = 1;
  foreach ($ticker_data as $line) {
    $data = explode(",",$line);
    $vdata = array(); // open/high/low/close
    for ($i = 1; $i<=4; ++$i) {
      $value = floatval($data[$i]);
      $vdata[] = $v0 + ($v1-$v0)*($value-$yrange[0])/($yrange[1]-$yrange[0]);
    }
    $u = $u0 + ($u1-$u0)*($n-$xrange[0])/($xrange[1]-$xrange[0]);

    imagesetthickness($im,3);
    imageline($im,$u,$vdata[2],$u,$vdata[1],$color);
    imagesetthickness($im,1);

    $c = $vdata[3]; $o = $vdata[0]; $w = 3; $dw = 2;
    if ($c>$o+1) { // black candle
      imagefilledrectangle($im,$u-$w-$dw,$o-$dw,$u+$w+$dw,$c+$dw,$color);
    } else if ($c<$o-1) { // white candle
      imagefilledrectangle($im,$u-$w-$dw,$c-$dw,$u+$w+$dw,$o+$dw,$color);
      imagefilledrectangle($im,$u-$w,$c,$u+$w,$o,$fillcolor);
    } else { // doji
      imagesetthickness($im,3);
      imageline($im,$u-$w-$dw,$c,$u+$w+$dw,$c,$color);
      imagesetthickness($im,1);
    }

    ++$n;
  }
}

function draw_positions($im,$u0,$v0,$u1,$v1,$color,$xrange,$ticker_data,$positions) {
  $n = 0;
  $ticks = array();
  foreach($positions as $date => $price) {
    $n = 1; $miss = true;
    foreach ($ticker_data as $line) {
      $tdate = substr($line,0,10);
      if ($date==$tdate) { $ticks[] = $n; $miss = false; }
      ++$n;
    }
    if ($miss) {
      $ticks[] = 0;
    }
  }
  $tick0 = 0; $tick1 = $n;
  $np = 0;
  foreach($ticks as $tick) {
    $x0 = $u0 + ($u1-$u0)*($tick-$tick0)/($tick1-$tick0);
    $x1 = $u1;
    $y0 = $v1-14-$np*2;
    $y1 = $y0+8;
    imagerectangle($im,$x0,$y0,$x1,$y1,$color);
    ++$np;
  }
}

function draw_actions($im,$u0,$v0,$u1,$v1,$color,$yrange,$actions) {
  $pmin = $yrange[0];
  $pmax = $yrange[1];
  $blocks = array();
  $previous_price = -1;
  $previous_action = 0;
  foreach($actions as $price => $action) {
    if ($previous_price>0) {
      $p0 = $previous_price; if ($p0<$pmin) $p0 = $pmin;
      $p1 = $price; if ($p1>$pmax) $p1 = $pmax;
      if ($p1>$pmin && $p0<$pmax) $blocks[] = array($p0,$p1,$previous_action);
    }
    $previous_price = $price;
    $previous_action = $action;
  }
  if ($previous_price<$pmax) {
    $p0 = $previous_price; if ($p0<$pmin) $p0 = $pmin;
    $p1 = $pmax;
    if ($p1>$pmin && $p0<$pmax) $blocks[] = array($p0,$p1,$previous_action);
  }

  foreach($blocks as $block) {
    $y0 = $v0 + ($v1-$v0)*($block[0]-$pmin)/($pmax - $pmin);
    $y1 = $v0 + ($v1-$v0)*($block[1]-$pmin)/($pmax - $pmin);
    $x0 = $u1+6; $x1 = $u1+14;
    if ($block[2]!=0) {
      imagerectangle($im,$x0,$y0,$x1,$y1,$color);
    //} else {
    //  imagefilledrectangle($im,$x0,$y0,$x1,$y1,$color);
    }
  }
}
?>
