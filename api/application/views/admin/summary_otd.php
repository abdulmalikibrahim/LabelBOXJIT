<style>
    .highlight-green {
        background-color: #0077C3;
        color: white;
    }
</style>
<form action="<?= base_url("set_adjust_otd"); ?>" id="form-adjust" method="post">
    <div class="row">
        <div class="col-lg-8"></div>
        <div class="col-lg-2">
            <p class="mb-1 mt-2">Periode</p>
            <div class="input-group">
                <select name="month" id="month" class="form-control">
                    <?php
                    for ($i=1; $i <= 12; $i++) {
                        if($month_sum == $i){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                        echo '<option value="'.$i.'" '.$selected.'>'.date("M",strtotime(date("Y-".sprintf("%02d",$i)."-01"))).'</option>';
                    }
                    ?>
                </select>
                <select name="year" id="year" class="form-control">
                    <?php
                    for ($i=2024; $i <= date("Y"); $i++) {
                        if($year_sum == $i){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                        echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-lg-2">
            <p class="mb-1 mt-2">Allowance Adjust</p>
            <select name="adjust" id="adjust" class="form-control">
                <option value="0">OnTime</option>
                <?php
                for ($i=1; $i <= 8; $i++) {
                    if($otd_adjust == $i){
                        $selected = "selected";
                    }else{
                        $selected = "";
                    }
                    echo '<option value="'.$i.'" '.$selected.'>'.$i.' Jam</option>';
                }
                ?>
            </select>
        </div>
    </div>
</form>
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-bordered table-hover m-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center align-middle" rowspan="3">Tanggal</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center align-middle" rowspan="3">Total Unit</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center align-middle" rowspan="3">Ontime</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center" colspan="9">Allowance</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center align-middle" rowspan="3">OTD</th>
                        </tr>
                        <tr>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">1 Jam</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">2 Jam</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">3 Jam</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">4 Jam</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">5 Jam</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">6 Jam</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">7 Jam</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">8 Jam</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">>8 Jam</th>
                        </tr>
                        <tr>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">1'-60'</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">61'-120'</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">121'-180'</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">181'-240'</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">241'-300'</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">301'-360'</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">361'-420'</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">421'-480'</th>
                            <th style="font-size:9pt;" class="pt-1 pb-1 text-center">>480'</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i=1; $i <= 31; $i++) {
                            $get_data_dot = $this->model->gd("set_ot","*","tanggal = '$i'","row");
                            $day = date("Y-m-d",strtotime(date($year_sum."-".$month_sum."-").sprintf("%02d",$i)));
                            $total_unit = $this->model->query_exec("SELECT COUNT(u1.vin) AS total_unit, 
                            COUNT(CASE WHEN u2.balance <= 0 THEN 1 END) AS total_ontime,
                            SUM(CASE WHEN u2.balance BETWEEN 1 AND 60 THEN 1 ELSE 0 END) AS total_1jam,
                            SUM(CASE WHEN u2.balance BETWEEN 61 AND 120 THEN 1 ELSE 0 END) AS total_2jam,
                            SUM(CASE WHEN u2.balance BETWEEN 121 AND 180 THEN 1 ELSE 0 END) AS total_3jam,
                            SUM(CASE WHEN u2.balance BETWEEN 181 AND 240 THEN 1 ELSE 0 END) AS total_4jam,
                            SUM(CASE WHEN u2.balance BETWEEN 241 AND 300 THEN 1 ELSE 0 END) AS total_5jam,
                            SUM(CASE WHEN u2.balance BETWEEN 301 AND 360 THEN 1 ELSE 0 END) AS total_6jam,
                            SUM(CASE WHEN u2.balance BETWEEN 361 AND 420 THEN 1 ELSE 0 END) AS total_7jam,
                            SUM(CASE WHEN u2.balance BETWEEN 421 AND 480 THEN 1 ELSE 0 END) AS total_8jam,
                            SUM(CASE WHEN u2.balance >= 481 THEN 1 ELSE 0 END) AS total_more8jam
                            FROM unit u1 LEFT JOIN unit u2 ON u1.vin = u2.vin WHERE u1.delivery BETWEEN '".$day." 07:00:00' AND '".date("Y-m-d",strtotime("+1 days",strtotime($day)))." 07:00:00';","row");
                            
                            // Assign results to variables
                            $totalUnit = $total_unit->total_unit;
                            $total_ontime = $total_unit->total_ontime;
                            $total_1jam = $total_unit->total_1jam;
                            $total_2jam = $total_unit->total_2jam;
                            $total_3jam = $total_unit->total_3jam;
                            $total_4jam = $total_unit->total_4jam;
                            $total_5jam = $total_unit->total_5jam;
                            $total_6jam = $total_unit->total_6jam;
                            $total_7jam = $total_unit->total_7jam;
                            $total_8jam = $total_unit->total_8jam;
                            $total_more8jam = $total_unit->total_more8jam;
                            
                            $otd_value = 0;
                            if($totalUnit > 0){
                                switch ($otd_adjust) {
                                    case 0:
                                        $otd_value = $total_ontime / $totalUnit;
                                        break;
                                    case 1:
                                        $otd_value = ($total_1jam + $total_ontime) / $totalUnit;
                                        break;
                                    case 2:
                                        $otd_value = ($total_2jam + $total_1jam + $total_ontime) / $totalUnit;
                                        break;
                                    case 3:
                                        $otd_value = ($total_3jam + $total_2jam + $total_1jam + $total_ontime) / $totalUnit;
                                        break;
                                    case 4:
                                        $otd_value = ($total_4jam + $total_3jam + $total_2jam + $total_1jam + $total_ontime) / $totalUnit;
                                        break;
                                    case 5:
                                        $otd_value = ($total_5jam + $total_4jam + $total_3jam + $total_2jam + $total_1jam + $total_ontime) / $totalUnit;
                                        break;
                                    case 6:
                                        $otd_value = ($total_6jam + $total_5jam + $total_4jam + $total_3jam + $total_2jam + $total_1jam + $total_ontime) / $totalUnit;
                                        break;
                                    case 7:
                                        $otd_value = ($total_7jam + $total_6jam + $total_5jam + $total_4jam + $total_3jam + $total_2jam + $total_1jam + $total_ontime) / $totalUnit;
                                        break;
                                    case 8:
                                        $otd_value = ($total_8jam + $total_7jam + $total_6jam + $total_5jam + $total_4jam + $total_3jam + $total_2jam + $total_1jam + $total_ontime) / $totalUnit;
                                        break;
                                    default:
                                        // Handle case where $otd_adjust is outside the expected range
                                        $otd_value = $total_ontime / $totalUnit;
                                        break;
                                }
                            }
                            ?>
                            <tr>
                                <td style="font-size:9pt;" class="text-center p-1 td-row"><?= date("d M",strtotime($day)) ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row"><?= $total_unit->total_unit; ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row cell-0"><?= $total_unit->total_ontime; ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row cell-1"><?= $total_unit->total_1jam; ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row cell-2"><?= $total_unit->total_2jam; ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row cell-3"><?= $total_unit->total_3jam; ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row cell-4"><?= $total_unit->total_4jam; ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row cell-5"><?= $total_unit->total_5jam; ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row cell-6"><?= $total_unit->total_6jam; ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row cell-7"><?= $total_unit->total_7jam; ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row cell-8"><?= $total_unit->total_8jam; ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row cell-9"><?= $total_unit->total_more8jam; ?></td>
                                <td style="font-size:9pt;" class="text-center p-1 td-row"><?= round($otd_value*100,1)."%"; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12 text-right mt-3">
        <a href="<?= base_url(""); ?>" class="btn btn-sm btn-danger">Kembali</a>
    </div>
</div>