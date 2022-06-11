<?php

namespace App\Services;

class ChancesService
{
    public $m;
    public $pr = [];
    public $cena;
    public $p_a = [];
    public $sum_proc = 0;
    public $zadrenta = 90;
    public $renta;
    public $masI = [];
    public $maccount;
    public $tochnost = 0.1;

    public function rentabl(): float
    {
        $sum = 0;
        for ($i = 0; $i < $this->m; $i++) {
            $sum += ($this->pr[$i] / 100) * ($this->cena - $this->p_a[$i]);
        }

        $result = 100 - ($sum / $this->cena) * 100;

        return $result;
    }

    public function rentabl_($PR): float
    {
        $sum = 0;
        for ($i = 0; $i < $this->m; $i++) {
            $sum += ($PR[$i] / 100) * ($this->cena - $this->p_a[$i]);
        }

        $result = 100 - ($sum / $this->cena) * 100;

        return $result;
    }

    public function serchInMasI($i): bool
    {
        for ($j = 0; $j < $this->maccount; $j++) {
            if ($this->masI[$j] == $i) {
                return true;
            }
        }

        return false;
    }

    public function Ravnoraspred($temp): void
    {
        $countIzmen = $this->m - $this->maccount;
        $whatsum = $temp / $countIzmen;

        for ($i = 0; $i < $this->m; $i++) {
            if (!$this->serchInMasI($i)) {
                $this->pr[$i] += $whatsum;
            }
        }
    }

    public function Nizetochnost($mas, $dlinna): bool
    {
        for ($i = 0; $i < $dlinna - 1; $i++) {
            if ($mas[$i] != $mas[$i + 1]) {
                return false;
            }
        }

        return true;
    }

    public function calculateChances(Array $prices, $cena)
    {
        ini_set('max_execution_time', 5);
        $str = '';
        sort($prices);
        $this->cena = $cena;
        $count = count($prices);
        $this->m = $count;
        $this->p_a = [];

        for ($i = 0; $i < $this->m; $i++) {
            $this->p_a[$i] = $prices[$i];
            $str .= 'Price[' . $i . '] is ' . $prices[$i] . PHP_EOL;
        }
        
        $min_a = $this->p_a[0];
        $max_a = $this->p_a[$this->m - 1];
        $R_a = $max_a - $min_a;
        $n_a = 1 + 3.322 * log10($this->m);
        $n = round($n_a);
        $h_a = $R_a / $n;

        $p_i = [];
        $p_i[0] = $min_a;
        $str .= 'Min = ' . $min_a . ', max = ' . $max_a . ', R = ' . $R_a . ', n_a = ' . $n_a . ', n = ' . $n . ', h = ' . $h_a . PHP_EOL;

        for ($i = 0; $i < $n; $i++) {
            $p_i[$i + 1] = $p_i[$i] + $h_a;
        }

        $p_i[$n + 1] = $max_a;

        //str
        $str .= 'Интервалы: ' . PHP_EOL;
        for ($i = 0; $i < $n + 1; $i++) {
            $str .= $p_i[$i] . PHP_EOL;
        }
        $str .= PHP_EOL;
        //
        $Xser = [];
        $str .= 'Середина интервала: ' . PHP_EOL;
        for ($i = 0; $i < $n; $i++) {
            $Xser[$i] = ($p_i[$i] + $p_i[$i + 1]) / 2;
            $str .= 'Xser[' . $i . '] is ' . $Xser[$i] . PHP_EOL;
        }

        $fi = [];
        $q = 0;
        
        $str .= 'Кол-во эл-тов в int' . PHP_EOL;
        for ($i = 0; $i < $n; $i++) {
            $k = 0;
            
            for ($j = 0; $j < $this->m; $j++) {
                if ($this->p_a[$j] >= $p_i[$i] && $this->p_a[$j] <= $p_i[$i + 1]) {
                    $k++;
                    $str .= 'p_a[' . $j . '] is ' . $this->p_a[$j] . PHP_EOL;
                }
            }

            $str .= 'k = ' . $k . ', p_i[' . $i . '] = ' . $p_i[$i] . ', p_i[' . $i . ' + 1] = ' . $p_i[$i + 1] . PHP_EOL;
            $fi[$q] = $k;
            $q++;
        }

        $Pxf = [];
        //str
        for ($i = 0; $i < $n; $i++) {
            $str .= 'fi[' . $i . '] = ' . $fi[$i] . PHP_EOL;
        }
        //
        $str .= 'Xser * fi' . PHP_EOL;
        for ($i = 0; $i < $n; $i++) {
            $Pxf[$i] = $Xser[$i] * $fi[$i];
            $str .= 'Pxf[' . $i . '] = ' . $Pxf[$i] . PHP_EOL;
        }
        
        $Sxf = 0;

        for ($i = 0; $i < $n; $i++) {
            $Sxf += $Pxf[$i];
        }

        $Xsv = $Sxf / $this->m;
        $str .= 'Sxf = ' . $Sxf . ', Xsv = ' . $Xsv . PHP_EOL;
        $str .= 'Сумма произв' . PHP_EOL;
        $Spr = [];
        $SS = 0;

        for ($i = 0; $i < $n; $i++) {
            $Spr[$i] = pow(($Xser[$i] - $Xsv), 2) * $fi[$i];
            $str .= 'Pxf[' . $i . '] = ' . $Pxf[$i] . ', fi[' . $i . '] = ' . $fi[$i] . ', Spr[' . $i . '] = ' . $Spr[$i] . PHP_EOL;
            $SS += $Spr[$i];
        }

        $str .= 'SS = ' . $SS . PHP_EOL;
        $sigm = $SS / $this->m;
        $sigmn = sqrt($sigm);
        $str .= 'sigm = ' . $sigm . ', sigmn = ' . $sigmn . PHP_EOL;

        $Fa = 1 / ($sigmn * sqrt(2 * 3.1416));
        $str .= 'Fa = ' . $Fa . PHP_EOL;
        $Fax = abs(($Fa - 0.5) / 0.5);
        $str .= 'Fax = ' . $Fax . PHP_EOL;
        $ma = 5;
        $str .= 'ma = ' . $ma . PHP_EOL;
        $str .= 'Плотность' . PHP_EOL;
        $Px = [];
        $SPx = 0;

        for ($i = 0; $i < $this->m; $i++) {
            $Px[$i] = $Fa * exp(-(pow(($this->p_a[$i] - $ma), 2) / (2 * $sigm)));
            $SPx += $Px[$i];
            $str .= 'p_a[' . $i . '] = ' . $this->p_a[$i] . ', Px[' . $i . '] = ' . $Px[$i] . PHP_EOL;
        }

        $str .= 'SPx = ' . $SPx . PHP_EOL;
        $str .= 'Процент' . PHP_EOL;
        $this->pr = [];
        $SPr = 0;

        for ($i = 0; $i < $this->m; $i++) {
            $this->pr[$i] = ($Px[$i] / $SPx) * 100;
            $SPr = $SPr + $this->pr[$i];
            $str .= 'p_a[' . $i . '] = ' . $this->p_a[$i] . ', Pr[' . $i . '] = ' . $this->pr[$i] . PHP_EOL;
        }

        $str .= 'SPr = ' . $SPr . PHP_EOL;
        $str .= 'Цена открытий' . PHP_EOL;
        $Cot = [];
        $SCot = 0;

        for ($i = 0; $i < $this->m; $i++) {
            $Cot[$i] = $this->p_a[$i] * ($this->pr[$i] / 100);
            $SCot += $Cot[$i];
            $str .= 'p_a[' . $i . '] = ' . $this->p_a[$i] . ', Pr[' . $i . ' ] = ' . $this->pr[$i] . ', Cot[' . $i . '] = ' . $Cot[$i] . PHP_EOL;
        }

        $str .= 'SCot = ' . $SCot . PHP_EOL;
        $str .= 'Цена = ' . $this->cena . PHP_EOL;
        $Rent = $SCot / $this->cena * 100;
        $str .= 'Rent = ' . $Rent . PHP_EOL;
        $Dohod = [];
        $SD = 0;
        $i_extr = 0;

        for ($i = 0; $i < $this->m; $i++) {
            $Dohod[$i] = ($this->cena - $this->p_a[$i]) * ($this->pr[$i] / 100);

            if ($i > 0 && $Dohod[$i] < 0 && $Dohod[$i - 1] >= 0) {
                $i_extr = $i - 1;
            }

            $SD += $Dohod[$i];
            $str .= 'Dohod[' . $i . '] = ' . $Dohod[$i] . PHP_EOL;
        }

        $RentD = 100 - $SD / $this->cena * 100;
        $str .= 'SD = ' . $SD . ', RentD = ' . $RentD . ', i_extr = ' . $i_extr . PHP_EOL;

        $str .= 'Max & Точность' . PHP_EOL;
        $this->sum_proc = 0;
        $max = 0;
        $imax = 0;

        for ($i = 0; $i < $this->m; $i++) {
            if ($this->pr[$i] > $max) {
                $max = $this->pr[$i];
                $imax = $i;
            }
        }

        for ($i = 0; $i < $this->m; $i++) {
            if ($this->pr[$i] < $this->tochnost) {
                $this->pr[$imax] = $this->pr[$imax] + $this->pr[$i] - $this->tochnost;
                $this->pr[$i] = $this->tochnost;
            }
            $this->sum_proc += $this->pr[$i];
        }

        $str .= 'Sum_proc = ' . $this->sum_proc . PHP_EOL;

        if ($this->rentabl() > $this->zadrenta) {
            $str .= 'Rentabl > zadrenta || ' . $this->rentabl() . ' > ' . $this->zadrenta . PHP_EOL;
            for ($i = $i_extr + 1; $i < $this->m; $i++) {
                if ($this->pr[$i] > $this->tochnost) {
                    $this->pr[$i_extr] += $this->pr[$i] * $this->tochnost;
                    $this->pr[$i] = $this->pr[$i] - $this->pr[$i] * $this->tochnost;
                }
                
                if ($this->rentabl() < $this->zadrenta) {
                    break;
                }
            }

            $this->sum_proc = 0;
            
            for ($i = 0; $i < $this->m; $i++) {
                $str .= 'Pr[' . $i . '] = ' . $this->pr[$i] . PHP_EOL;
                $this->sum_proc += $this->pr[$i];
            }

            $str .= 'Sum_proc = ' . $this->sum_proc . PHP_EOL;
            $str .= '10%' . PHP_EOL . PHP_EOL;
            $ii = 1;
            
            while ($ii < $i_extr && $this->rentabl() > $this->zadrenta) {
                $temp_proc = 0;

                for ($j = $i_extr + $ii + 1; $j < $this->m; $j++) {
                    if ($this->pr[$j] > $this->tochnost) {
                        $temp_proc += $this->pr[$j] * $this->tochnost;
                        $this->pr[$j] = $this->pr[$j] - $this->pr[$j] * $this->tochnost;
                    }
                }

                if ($ii + $i_extr > $count - 1) {
                    break;
                }

                $this->pr[$i_extr - $ii] += 0.61 * $temp_proc;
                $this->pr[$i_extr + $ii] += 0.39 * $temp_proc;
                $ii++;
            }

            $this->renta = $this->rentabl();
            $str .= 'rentabelnost = ' . $this->renta . PHP_EOL;
            $this->sum_proc = 0;

            for ($i = 0; $i < $this->m; $i++) {
                $str .= 'Pr[' . $i . '] = ' . $this->pr[$i] . PHP_EOL;
                $this->sum_proc += $this->pr[$i];
            }

            $str .= 'Sum_proc = ' . $this->sum_proc . PHP_EOL . PHP_EOL;

            for ($i = 0; $i < $this->m; $i++) {
                if ($this->pr[$i] > 0.0016) {
                    $this->pr[$i] = round($this->pr[$i] * 100) / 100;
                } else {
                    $this->pr[$i] = round($this->pr[$i] * 1000) / 1000;
                }
            }

            $this->sum_proc = 0;

            for ($i = 0; $i < $this->m; $i++) {
                $this->sum_proc += $this->pr[$i];
            }

            $sum_proc1 = 100 - $this->sum_proc;
            $this->pr[$i_extr] += $sum_proc1;
            
            $str .= 'Проверка минусов' . PHP_EOL;

            $tsum = 0;

            for ($i = 0; $i < $this->m; $i++) {
                $str .= 'Pr[' . $i . '] = ' . $this->pr[$i] . PHP_EOL;
                $tsum += $this->pr[$i];
            }

            $str .= 'tsum = ' . $tsum . PHP_EOL;

            for ($i = 0; $i < $this->m; $i++) {
                if ($this->pr[$i] > 0.0016) {
                    $this->pr[$i] = round($this->pr[$i] * 100) / 100;
                } else {
                    $this->pr[$i] = round($this->pr[$i] * 1000) / 1000;
                }
            }

            $this->sum_proc = 0;

            for ($i = 0; $i < $this->m; $i++) {
                $this->sum_proc += $this->pr[$i];
            }

            $sum_proc1 = 100 - $this->sum_proc;

            if (!$this->serchInMasI($i_extr)) {
                $this->pr[$i_extr] += $sum_proc1;
            } else {
                if ($i_extr == $this->m) {
                    $temp_extr = 1;

                    while ($this->serchInMasI($i_extr - $temp_extr)) {
                        $temp_extr++;
                    }

                    $this->pr[$i_extr - $temp_extr] += $sum_proc1;
                } else {
                    if ($i_extr == 0) {
                        $temp_extr = 1;

                        while ($this->serchInMasI($i_extr + $temp_extr)) {
                            $temp_extr++;
                        }

                        $this->pr[$i_extr + $temp_extr] += $sum_proc1;
                    } else {
                        $temp_extr = 1;

                        while ($this->serchInMasI($i_extr - $temp_Extr)) {
                            $temp_extr++;
                        }

                        $this->pr[$i_extr - $temp_extr] += $sum_proc1;
                    }
                }
            }

            $sum_Pr_temp = 0;
            $usingsum = 100;
            $Pr_temp = [];

            for ($i = 0; $i < $this->m; $i++) {
                $Pr_temp[$i] = $this->pr[$i];
            }

            $str .= 'Проверка минусов 2' . PHP_EOL;
            $tsum = 0;

            for ($i = 0; $i < $this->m; $i++) {
                $str .= 'Pr[' . $i . '] = ' . $Pr_temp[$i] . PHP_EOL;
                $tsum += $Pr_temp[$i];
            }

            $str .= 'tsum = ' . $tsum . PHP_EOL;

            for ($i = $this->m - 1; $i >= 0; $i--) {
                while ($this->serchInMasI($i)) {
                    $usingsum -= $Pr_temp[$i];
                    $i--;
                }
                
                if ($i <= $i_extr) {
                    $Pr_temp[$i] = round(($usingsum / ($i_extr + 1) - 0.01 * ($i_extr - $i)) * 100) / 100;
                } else {
                    $Pr_temp[$i] = 0.01;
                    $usingsum -= 0.01;
                }
            }

            $tsum = 0;

            for ($i = 0; $i < $this->m; $i++) {
                $tsum += $Pr_temp[$i];
            }

            if ($this->serchInMasI(0)) {
                $temp_extr = 1;

                while ($this->serchInMasI(0 + $temp_extr)) {
                    $temp_extr++;
                }

                $Pr_temp[0 + $temp_extr] += 100 - $tsum;
            } else {
                $Pr_temp[0] += 100 - $tsum;
            }

            $str .= 'Проверка минусов 3' . PHP_EOL;
            $tsum = 0;

            for ($i = 0; $i < $this->m; $i++) {
                $str .= 'Pr[' . $i . '] = ' . $Pr_temp[$i];
                $tsum += $Pr_temp[$i];
            }

            $str .= 'tsum = ' . $tsum . PHP_EOL;

            for ($i = 0; $i < $this->m; $i++) {
                $sum_Pr_temp += $Pr_temp[$i];
            }

            $Pr_temp[$i_extr] += 100 - $sum_Pr_temp;
            $sum_Pr_temp = 0;

            for ($i = 0; $i < $this->m; $i++) {
                $str .= 'Pr_temp[' . $i . '] = ' . $Pr_temp[$i] . PHP_EOL;
                $sum_Pr_temp += $Pr_temp[$i];
            }

            $str .= 'min rentabl = ' . $this->rentabl_($Pr_temp) . ', sum_Pr_temp = ' . $sum_Pr_temp . ', usingsum = ' . $usingsum . PHP_EOL;

            if ($this->zadrenta < $this->rentabl_($Pr_temp)) {
                $str .= 'Zadrenta < min rentabl' . PHP_EOL;
                return 'Zadrenta < min rentabl';
            }

            $usingsum = 100;

            for ($i = 0; $i < $this->m; $i++) {
                $Pr_temp[$i] = $this->pr[$i];
            }

            $sum_Pr_temp = 0;

            for ($i = 0; $i < $this->m; $i++) {
                while ($this->serchInMasI($i)) {
                    $i++;
                }

                if ($i < $i_extr) {
                    $Pr_temp[$i] = 0.01;
                    $usingsum -= 0.01;
                } else {
                    $Pr_temp[$i] = round(($usingsum / ($this->m - $i_extr) - 0.01 * ($i - $i_extr)) * 100) / 100;
                }
            }

            for ($i = 0; $i < $this->m; $i++) {
                $sum_Pr_temp += $Pr_temp[$i];
            }

            $Pr_temp[$i_extr] += 100 - $sum_Pr_temp;
            $sum_Pr_temp = 0;

            for ($i = 0; $i < $this->m; $i++) {
                $str .= 'Pr_temp[' . $i . '] = ' . $Pr_temp[$i];
                $sum_Pr_temp += $Pr_temp[$i];
            }

            $str .= 'Max rentabl = ' . $this->rentabl_($Pr_temp) . ', sum_Pr_temp = ' . $sum_Pr_temp . PHP_EOL;

            if ($this->zadrenta > $this->rentabl_($Pr_temp)) {
                $str .= 'Zadrenta > max rentabl';
                return 'Zadrenta > max rentabl';
            }

            $temp_rentabl = [
                0,
                1,
                2,
                3,
            ];
            $temp_rentabl_1 = [
                0,
                1,
                2,
                3,
            ];
            $temp_rentablcounter = 0;
            $ponizhcounter = 0;
            $temp_rentablcounter_1 = 0;
            $temp_proc = 0.1;

            while ($this->rentabl() > $this->zadrenta + 0.4) {
                if (!$this->Nizetochnost($temp_rentabl, 4) && $ponizhcounter <= 1) {
                    $temp_proc = $this->tochnost;
                    $itemp = 0;

                    for ($i = $this->m - 1; $i >= 0; $i--) {
                        while ($this->serchInMasI($i)) {
                            $i--;
                        }

                        while ($this->serchInMasI($itemp)) {
                            $itemp++;
                        }

                        if ($this->pr[$i] - $temp_proc > $temp_proc) {
                            $this->pr[$i] = $this->pr[$i] - $temp_proc;
                            $this->pr[$itemp] += $temp_proc;
                        }

                        if ($itemp < $i_extr) {
                            $itemp++;
                        } else {
                            $itemp = 0;
                        }

                        if ($this->rentabl() <= $this->zadrenta + 0.4) {
                            break;
                        }

                        $str .= $this->rentabl() . ' perv privbliz itemp = ' . $itemp . ' ' . $this->Nizetochnost($temp_rentabl, 4) . PHP_EOL;
                        $temp_rentabl[$temp_rentablcounter] = $this->rentabl();
                        $temp_rentablcounter++;

                        if ($temp_rentablcounter > 3) {
                            $temp_rentablcounter = 0;
                        }

                        if ($this->Nizetochnost($temp_rentabl, 4) && $ponizhcounter <= 1) {
                            $temp_proc = $temp_proc / 10;
                            $temp_rentabl[$temp_rentablcounter] = 1;
                            $ponizhcounter++;
                        }
                    }
                } else {
                    if ($ponizhcounter < 1) {
                        $this->tochnost = $this->tochnost / 10;
                        $temp_rentabl[$temp_rentablcounter] = 1;
                        $ponizhcounter++;
                    }
                }

                if ($ponizhcounter >= 1) {
                    $itemp = 0;

                    for ($i = $this->m - 1; $i >= 0; $i--) {
                        while ($this->serchInMasI($i)) {
                            $i--;
                        }

                        while ($this->serchInMasI($itemp)) {
                            $itemp++;
                        }

                        if ($this->pr[$i] - $temp_proc > $temp_proc) {
                            $this->pr[$i] = $this->pr[$i] - $temp_proc;
                            $this->pr[$itemp] += $temp_proc;
                        }

                        if ($itemp < $i_extr) {
                            $itemp++;
                        } else {
                            $itemp = 0;
                        }

                        if ($this->rentabl() <= $this->zadrenta + 0.4) {
                            break;
                        }
                    }

                    $temp_rentabl_1[$temp_rentablcounter_1] = $this->rentabl();
                    $temp_rentablcounter_1++;

                    if ($temp_rentablcounter_1 > 3) {
                        $temp_rentablcounter_1 = 0;
                    }

                    if ($this->Nizetochnost($temp_rentabl_1, 4)) {
                        $temp_proc = $temp_proc / 10;
                        $temp_rentabl_1[$temp_rentablcounter_1] = 1;
                    }

                    $str .= $this->rentabl() . 'vt pribliz' . PHP_EOL;
                }
            }
        } else {
            $str .= 'rentabl < zadrenta || ' . $this->rentabl() . ' < ' . $this->zadrenta . PHP_EOL;
            for ($i = 0; $i < $this->m; $i++) {
                if ($this->pr[$i] > 0.0016) {
                    $this->pr[$i] = round($this->pr[$i] * 100) / 100;
                } else {
                    $this->pr[$i] = round($this->pr[$i] * 1000) / 1000;
                }
            }

            $this->sum_proc = 0;

            for ($i = 0; $i < $this->m; $i++) {
                $this->sum_proc += $this->pr[$i];
            }

            $sum_proc1 = 100 - $this->sum_proc;
            $this->pr[$i_extr] += $sum_proc1;

            $i_extr++;

            for ($i = 0; $i < $i_extr; $i++) {
                if ($this->pr[$i] > 0.001) {
                    $this->pr[$i_extr] += $this->pr[$i] * $this->tochnost;
                    $this->pr[$i] = $this->pr[$i] - $this->pr[$i] * $this->tochnost;
                }

                $x = $this->rentabl();

                if ($this->rentabl() > $this->zadrenta) {
                    break;
                }
            }

            if ($this->rentabl() < $this->zadrenta) {
                $this->sum_proc = 0;

                for ($i = 0; $i < $this->m; $i++) {
                    $str .= 'Pr[' . $i . '] = ' . $this->pr[$i] . PHP_EOL;
                    $this->sum_proc += $this->pr[$i];
                }

                $str .= 'Sum_proc = ' . $this->sum_proc . PHP_EOL;
                $str .= '10%' . PHP_EOL;
                $ii = 0;
                
                while ($ii < $i_extr && $this->rentabl() < $this->zadrenta) {
                    $temp_proc = 0;
                    
                    for ($j = $i_extr - $ii - 1; $j > 0; $j--) {
                        if ($this->pr[$j] > 0.001) {
                            $temp_proc += $this->pr[$j] * $this->tochnost;
                            $this->pr[$j] = $this->pr[$j] - $this->pr[$j] * $this->tochnost;
                        }
                    }
                    
                    if ($ii + $i_extr > $count - 1) {
                        break;
                    }

                    $this->pr[$i_extr - $ii] += 0.39 * $temp_proc;
                    $this->pr[$i_extr + $ii] += 0.61 * $temp_proc;
                    $ii++;
                }
                
                $this->renta = $this->rentabl();
                
                $str .= 'Rentabelnost = ' . $this->renta . PHP_EOL;
                
                for ($i = 0; $i < $this->m; $i++) {
                    if ($this->pr[$i] > 0.0016) {
                        $this->pr[$i] = round($this->pr[$i] * 100) / 100;
                    } else {
                        $this->pr[$i] = round($this->pr[$i] * 1000) / 1000;
                    }
                }

                $this->sum_proc = 0;

                for ($i = 0; $i < $this->m; $i++) {
                    $this->sum_proc += $this->pr[$i];
                }

                $sum_proc1 = 100 - $this->sum_proc;
                $this->pr[$i_extr] += $sum_proc1;

                $this->sum_proc = 0;

                for ($i = 0; $i < $this->m; $i++) {
                    $str .= 'Pr[' . $i . '] = ' . $this->pr[$i] . PHP_EOL;
                    $this->sum_proc += $this->pr[$i];
                }

                $str .= 'Sum_proc = ' . $this->sum_proc . PHP_EOL;
                $itemp = $i_extr;
                    
                if ($this->rentabl() < $this->zadrenta) {  // infinity loop if while
                    $str .= 'Rentabl = ' . $this->rentabl() . ', i_extr = ' . $i_extr . PHP_EOL;

                    for ($i = 0; $i < $this->m; $i++) {
                        $str .= 'Pr[' . $i . '] = ' . $this->pr[$i] . PHP_EOL;
                    }

                    $temp_proc = 0.1;

                    for ($i = 0; $i < $i_extr; $i++) {
                        if ($this->pr[$i] - $temp_proc > $temp_proc) {
                            $this->pr[$i] = $this->pr[$i] - $temp_proc;
                            $this->pr[$this->m - $itemp] += $temp_proc;
                        }

                        if ($itemp < $this->m) {
                            $itemp++;
                        } else {
                            $itemp = $i_extr;
                        }
                            
                        if ($this->rentabl() >= $this->zadrenta) {
                            break;
                        }
                    }
                }
            } else {
                $i_extr--;

                while ($this->rentabl() > $this->zadrenta) {
                    $temp_proc = 0.1;
                    $itemp = 0;

                    for ($i = $this->m - 1; $i > $i_extr; $i--) {
                        if ($this->serchInMasI($i)) {
                            $i--;
                        }

                        if ($this->serchInMasI($itemp)) {
                            $itemp++;
                        }

                        if ($this->pr[$i] - $temp_proc > $temp_proc) {
                            $this->pr[$i] = $this->pr[$i] - $temp_proc;
                            $this->pr[$i_extr - $itemp] += $temp_proc;
                        }

                        if ($itemp < $i_extr) {
                            $itemp++;
                        } else {
                            $itemp = 0;
                        }

                        if ($this->rentabl() <= $this->zadrenta) {
                            break;
                        }
                    }
                }
            }
        }
        
        $this->renta = $this->rentabl();
        $str .= 'Rentabelnost = ' . $this->renta . PHP_EOL;
        error_log($this->renta);
        if ($this->renta < 85) {
            return 'Рентабельность меньше 85%';
        }

        $str .= 'Проценты:' . PHP_EOL;
        $this->sum_proc = 0;

        for ($i = 0; $i < $this->m; $i++) {
            $str .= $this->pr[$i] . PHP_EOL;
            $this->sum_proc += $this->pr[$i];
        }

        $str .= 'Сумма процентов ' . $this->sum_proc . PHP_EOL;
        
        if (intval(round($this->sum_proc)) != 100) {
            return 'Все сломалось';
        } else {
            return $this->pr;
        }
    }
}