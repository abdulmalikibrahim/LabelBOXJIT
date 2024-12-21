<?php
$tt = "";
$eff = "";
$leadtime_std = "";
$wip_weld = "";
$wip_paint = "";
$wip_pbs = "";
$wip_assy = "";
$wip_ru = "";
$total_wip = 0;
if(!empty($data_std)){
    foreach ($data_std as $data_std) {
        if($data_std->item == "tt"){
            $tt = $data_std->nilai;
        }

        if($data_std->item == "eff"){
            $eff = $data_std->nilai*100;
        }
        
        if($data_std->item == "wipw"){
            $wip_weld = $data_std->nilai;
            $total_wip += $wip_weld;
        }
        
        if($data_std->item == "wipt"){
            $wip_paint = $data_std->nilai;
            $total_wip += $wip_paint;
        }
        
        if($data_std->item == "wipp"){
            $wip_pbs = $data_std->nilai;
            $total_wip += $wip_pbs;
        }
        
        if($data_std->item == "wipa"){
            $wip_assy = $data_std->nilai;
            $total_wip += $wip_assy;
        }
        
        if($data_std->item == "wipr"){
            $wip_ru = $data_std->nilai;
            $total_wip += $wip_ru;
        }
    }
    $leadtime_std = round(($total_wip*$tt)/($eff/100));
}
?>
<div class="row mt-3 ml-3">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body pt-2">
                <form action="<?= base_url("simpan_leadtime"); ?>" method="post">
                    <div class="row">
                        <div class="col-12"><p class="text-danger">*Press Enter for submit</p></div>
                        <div class="col-4 mb-2">Tack Time</div>
                        <div class="col-8 mb-2"><input type="text" name="tt" id="tt" class="form-control" placeholder="Masukkan Tack Time" value="<?= $tt; ?>"></div>
                        <div class="col-4 mb-2">Effeciency</div>
                        <div class="col-8 mb-2">
                            <div class="input-group">
                                <input type="number" class="form-control" name="eff" id="eff" placeholder="Masukkan Effeciency" value="<?= $eff; ?>">
                                <span class="input-group-text rounded-0" id="inputGroupPrepend">%</span>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-2">
                            <h4>WIP STANDARD</h4>
                        </div>
                        <div class="col-4 mb-2">Welding</div>
                        <div class="col-8 mb-2"><input type="text" id="wip-weld" name="wip-weld" class="form-control" placeholder="Masukkan Std WIP" value="<?= $wip_weld; ?>"></div>
                        <div class="col-4 mb-2">Painting</div>
                        <div class="col-8 mb-2"><input type="text" id="wip-paint" name="wip-paint" class="form-control" placeholder="Masukkan Std WIP" value="<?= $wip_paint; ?>"></div>
                        <div class="col-4 mb-2">PBS</div>
                        <div class="col-8 mb-2"><input type="text" id="wip-pbs" name="wip-pbs" class="form-control" placeholder="Masukkan Std WIP" value="<?= $wip_pbs; ?>"></div>
                        <div class="col-4 mb-2">Assy</div>
                        <div class="col-8 mb-2"><input type="text" id="wip-assy" name="wip-assy" class="form-control" placeholder="Masukkan Std WIP" value="<?= $wip_assy; ?>"></div>
                        <div class="col-4 mb-2">Running Unit</div>
                        <div class="col-8 mb-2"><input type="text" id="wip-ru" name="wip-ru" class="form-control" placeholder="Masukkan Std WIP" value="<?= $wip_ru; ?>"></div>

                        <div class="col-4 mb-2 font-weight-bold">Total WIP</div>
                        <div class="col-8 mb-2"><input type="text" id="total-wip" name="total-wip" class="form-control font-weight-bold" placeholder="Total WIP" value="<?= $total_wip; ?>" readonly></div>

                        <div class="col-lg-12 mb-2 text-right"><hr class="mt-1 mb-1" style="border:2px solid;"></div>

                        <div class="col-4 mb-2 font-weight-bold">Lead Time Normal</div>
                        <div class="col-8 mb-2"><input type="text" id="lead-time-normal" name="lead-time-normal" class="form-control font-weight-bold" placeholder="Standard Lead Time" value="<?= $leadtime_std; ?>" readonly></div>

                        <div class="col-lg-12 text-right">
                            <button class="btn btn-info btn-sm">Simpan</button>
                            <a href="<?= base_url(""); ?>" class="btn btn-sm btn-danger">Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>