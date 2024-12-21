<form action="<?= base_url("simpan_set_wh"); ?>" method="post">
    <div class="row mt-3 ml-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover m-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="pt-1 pb-1 text-center">Tanggal</th>
                                <th class="pt-1 pb-1 text-center">On/Off</th>
                                <th class="pt-1 pb-1 text-center">Post DOT DS</th>
                                <th class="pt-1 pb-1 text-center">Pre DOT NS</th>
                                <th class="pt-1 pb-1 text-center">Post DOT NS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($i=1; $i <= 16; $i++) {
                                $get_data_dot = $this->model->gd("set_ot","*","tanggal = '$i'","row");
                                $day = date("D",strtotime(date("Y-m-").sprintf("%02d",$i)));
                                if(substr_count("Sat Sun",$day) > 0){
                                    $checked = "";
                                    $bg_day = "bg-secondary text-light";
                                }else{
                                    $bg_day = "";
                                    $checked = "checked";
                                }
                                ?>
                                <tr>
                                    <td class="<?= $bg_day; ?> pt-1 pb-1"><?= sprintf("%02d",$i)." ".date("M"); ?></td>
                                    <td class="<?= $bg_day; ?> pt-1 pb-1 text-center"><input type="checkbox" name="onoff[<?= $i; ?>]" value="<?= $i; ?>" <?= $checked; ?>></td>
                                    <td class="p-0"><input type="text" name="post_ot_ds[<?= $i; ?>]" class="form-control text-center" value="<?= $get_data_dot->post_ot_ds; ?>"></td>
                                    <td class="p-0"><input type="text" name="pre_ot_ns[<?= $i; ?>]" class="form-control text-center" value="<?= $get_data_dot->pre_ot_ns; ?>"></td>
                                    <td class="p-0"><input type="text" name="post_ot_ns[<?= $i; ?>]" class="form-control text-center" value="<?= $get_data_dot->post_ot_ns; ?>"></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover m-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="pt-1 pb-1 text-center">Tanggal</th>
                                <th class="pt-1 pb-1 text-center">On/Off</th>
                                <th class="pt-1 pb-1 text-center">Post DOT DS</th>
                                <th class="pt-1 pb-1 text-center">Pre DOT NS</th>
                                <th class="pt-1 pb-1 text-center">Post DOT NS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($i=17; $i <= 31; $i++) { 
                                $get_data_dot = $this->model->gd("set_ot","*","tanggal = '$i'","row");
                                $day = date("D",strtotime(date("Y-m-").sprintf("%02d",$i)));
                                if(substr_count("Sat Sun",$day) > 0){
                                    $checked = "";
                                    $bg_day = "bg-secondary text-light";
                                }else{
                                    $bg_day = "";
                                    $checked = "checked";
                                }
                                ?>
                                <tr>
                                    <td class="<?= $bg_day; ?> pt-1 pb-1"><?= sprintf("%02d",$i)." ".date("M"); ?></td>
                                    <td class="<?= $bg_day; ?> pt-1 pb-1 text-center"><input type="checkbox" name="onoff[<?= $i; ?>]" value="<?= $i; ?>" <?= $checked; ?>></td>
                                    <td class="p-0"><input type="text" name="post_ot_ds[<?= $i; ?>]" class="form-control text-center" value="<?= $get_data_dot->post_ot_ds; ?>"></td>
                                    <td class="p-0"><input type="text" name="pre_ot_ns[<?= $i; ?>]" class="form-control text-center" value="<?= $get_data_dot->pre_ot_ns; ?>"></td>
                                    <td class="p-0"><input type="text" name="post_ot_ns[<?= $i; ?>]" class="form-control text-center" value="<?= $get_data_dot->post_ot_ns; ?>"></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12 text-right">
            <button class="btn btn-info btn-sm">Simpan</button>
            <a href="<?= base_url(""); ?>" class="btn btn-sm btn-danger">Kembali</a>
            <a href="<?= base_url("clear_ot"); ?>" class="btn btn-sm btn-warning">Clear All</a>
        </div>
    </div>
</form>