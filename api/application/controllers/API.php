<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
require_once FCPATH . 'vendor/autoload.php';
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Dompdf\Dompdf;

class API extends MY_Controller {

    function uploadData()
    {
        // Konfigurasi upload file
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'xls|xlsx';

        $this->upload->initialize($config);
        if (!$this->upload->do_upload('upload-file')) {
            // Jika upload gagal, tampilkan error
            $error = $this->upload->display_errors();
            $this->fb(["statusCode" => 500, "res" => $error]);
        }
        
        // Jika upload berhasil
        $file_data = $this->upload->data();
        $file_path = $file_data['full_path'];
        // Load PHPExcel
        require 'vendor/autoload.php';
        $objPHPExcel = IOFactory::load($file_path);

        $clear_data = $this->model->delete("master", "id !=");
        // Membaca sheet pertama
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $data = [];
        // Looping untuk membaca data dari setiap baris
        for ($row = 2; $row <= $highestRow; $row++) { // Mulai dari baris ke-2 (baris pertama biasanya header)
            if(!empty(str_replace(" ","",$sheet->getCell('A' . $row)->getValue()))){
                $rack_address = $sheet->getCell('AR' . $row)->getCalculatedValue();
                if($rack_address == "#N/A"){
                    $rack_address = "";
                }
    
                $part_type = $sheet->getCell('AT' . $row)->getCalculatedValue();
                if($part_type == "#N/A"){
                    $part_type = "";
                }
    
                $wh_zone = $sheet->getCell('AU' . $row)->getCalculatedValue();
                if($wh_zone == "#N/A"){
                    $wh_zone = "";
                }
    
                $data[] = [
                    'plant_code' => $sheet->getCell('A' . $row)->getValue(),
                    'shop_code' => $sheet->getCell('B' . $row)->getValue(),
                    'part_category' => $sheet->getCell('C' . $row)->getValue(),
                    'route' => $sheet->getCell('D' . $row)->getValue(),
                    'lp' => $sheet->getCell('E' . $row)->getValue(),
                    'trip' => $sheet->getCell('F' . $row)->getValue(),
                    'vendor_code' => $sheet->getCell('G' . $row)->getValue(),
                    'vendor_alias' => $sheet->getCell('H' . $row)->getValue(),
                    'vendor_site' => $sheet->getCell('I' . $row)->getValue(),
                    'vendor_site_alias' => $sheet->getCell('J' . $row)->getValue(),
                    'order_no' => $sheet->getCell('K' . $row)->getValue(),
                    'po_number' => $sheet->getCell('L' . $row)->getValue(),
                    'calc_date' => Date::excelToDateTimeObject($sheet->getCell('M' . $row)->getValue())->format("Y-m-d"),
                    'order_date' => Date::excelToDateTimeObject($sheet->getCell('N' . $row)->getValue())->format("Y-m-d"),
                    'order_time' => Date::excelToDateTimeObject($sheet->getCell('O' . $row)->getValue())->format("H:i:s"),
                    'del_date' => Date::excelToDateTimeObject($sheet->getCell('R' . $row)->getValue())->format("Y-m-d"),
                    'del_time' => Date::excelToDateTimeObject($sheet->getCell('S' . $row)->getValue())->format("H:i:s"),
                    'del_cycle' => $sheet->getCell('T' . $row)->getValue(),
                    'doc_no' => str_replace(" ","",$sheet->getCell('U' . $row)->getValue()),
                    'part_no' => $sheet->getCell('AB' . $row)->getValue(),
                    'part_name' => $sheet->getCell('AC' . $row)->getValue(),
                    'job_no' => $sheet->getCell('AD' . $row)->getValue(),
                    'lane' => $sheet->getCell('AE' . $row)->getValue(),
                    'qty_kanban' => $sheet->getCell('AF' . $row)->getValue(),
                    'order_kbn' => $sheet->getCell('AG' . $row)->getValue(),
                    'order_pcs' => $sheet->getCell('AH' . $row)->getValue(),
                    'rack_address' => $rack_address,
                    'packaging_type' => $sheet->getCell('AS' . $row)->getCalculatedValue(),
                    'part_type' => $part_type,
                    'wh_zone' => $wh_zone,
                    'vendor_name' => $sheet->getCell('AV' . $row)->getCalculatedValue(),
                ];
            }
        }
        
        $insert = $this->model->insert_batch("master",$data);
        if($insert){
            $fb = ["statusCode" => 200, "res" => "Upload success"];
        }else{
            $fb = ["statusCode" => 500, "res" => "Upload failed"];
        }
        unlink($file_path);
        $this->fb($fb);
    }

    function getDataVendor()
    {
        //GET DATA
        $data = $this->model->gd("master","vendor_code,vendor_alias,download_kanban,download_dn","id != '' GROUP BY vendor_code ORDER BY vendor_code,download_kanban,download_dn ASC","result");
        if(empty($data)){
            $fb = ["statusCode" => 404, "res" => "Data kosong"];
            $this->fb($fb);
        }

        $fb = ["statusCode" => 200, "res" => $data];
        $this->fb($fb);
    }

    function getDataByVendor()
    {
        $jsonInput = json_decode(file_get_contents('php://input'), true);
        $vendor_code = $jsonInput["vendor_code"] ?? null;

        $this->form_validation->set_data($jsonInput);
        $this->form_validation->set_rules('vendor_code', 'Vendor Code', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $fb = ["statusCode" => 401, "res" => validation_errors()];
            $this->fb($fb);
        }

        $data = $this->model->gd("master","*","vendor_code = '$vendor_code'","result");
        if(empty($data)){
            $fb = ["statusCode" => 404, "res" => "Data kosong"];
            $this->fb($fb);
        }

        $fb = ["statusCode" => 200, "res" => $data];
        $this->fb($fb);
    }

    function getDataDN()
    {
        $jsonInput = json_decode(file_get_contents('php://input'), true);
        $vendor_code = $jsonInput["vendor_code"] ?? null;

        $this->form_validation->set_data($jsonInput);
        $this->form_validation->set_rules('vendor_code', 'Vendor Code', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $fb = ["statusCode" => 401, "res" => validation_errors()];
            $this->fb($fb);
        }

        $dataByDN = $this->model->gd("master","order_no","vendor_code = '$vendor_code' GROUP BY order_no","result");
        if(empty($dataByDN)){
            $fb = ["statusCode" => 404, "res" => "Data kosong"];
            $this->fb($fb);
        }

        $result = [];
        foreach ($dataByDN as $dataByDN) {
            //GET DATA DN
            $dn = $this->model->gd("master","*","order_no = '".$dataByDN->order_no."' AND vendor_code = '$vendor_code'","result");
            if(!empty($dn)){
                foreach ($dn as $dn) {
                    $dn_new = [
                        "calc_date"=> date("d-M-Y",strtotime($dn->calc_date)),
                        "del_cycle" => $dn->del_cycle,
                        "del_date" => date("d-M-Y",strtotime($dn->del_date)),
                        "del_day" => date("D",strtotime($dn->del_date)),
                        "del_time" => $dn->del_time,
                        "doc_no" => $dn->doc_no,
                        "download_dn" => $dn->download_dn,
                        "download_kanban" => $dn->download_kanban,
                        "id" => $dn->id,
                        "job_no" => $dn->job_no,
                        "lane" => $dn->lane,
                        "lp" => $dn->lp,
                        "order_date" => date("d-M-Y",strtotime($dn->order_date)),
                        "order_kbn" => $dn->order_kbn,
                        "order_no" => $dn->order_no,
                        "order_pcs" => $dn->order_pcs,
                        "order_time" => $dn->order_time,
                        "packaging_type" => $dn->packaging_type,
                        "part_category" => $dn->part_category,
                        "part_name" => $dn->part_name,
                        "part_no" => $dn->part_no,
                        "part_type" => $dn->part_type,
                        "plant_code" => $dn->plant_code,
                        "po_number" => $dn->po_number,
                        "qty_kanban" => $dn->qty_kanban,
                        "rack_address" => $dn->rack_address,
                        "route" => $dn->route,
                        "shop_code" => $dn->shop_code,
                        "trip" => $dn->trip,
                        "vendor_alias" => $dn->vendor_alias,
                        "vendor_code" => $dn->vendor_code,
                        "vendor_name" => $dn->vendor_name,
                        "vendor_site" => $dn->vendor_site,
                        "vendor_site_alias" => $dn->vendor_site_alias,
                        "wh_zone" => $dn->wh_zone
                    ];
                    $result[$dataByDN->order_no][] = $dn_new;
                }
            }
        }

        $fb = ["statusCode" => 200, "res" => $result];
        $this->fb($fb);
    }
    
    function updateStatus()
    {
        $jsonInput = json_decode(file_get_contents('php://input'), true);
        $vendor_code = $jsonInput["vendor_code"] ?? null;
        $typeDownload = $jsonInput["typeDownload"] ?? null;

        $this->form_validation->set_data($jsonInput);
        $this->form_validation->set_rules('vendor_code', 'Vendor Code', 'trim|required');
        $this->form_validation->set_rules('typeDownload', 'Vendor Code', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $fb = ["statusCode" => 401, "res" => validation_errors()];
            $this->fb($fb);
        }

        $updateDate = [];
        if($typeDownload == "kanban"){
            $updateDate["download_kanban"] = "1";
        }else if($typeDownload == "dn"){
            $updateDate["download_dn"] = "1";
        }else{
            $fb = ["statusCode" => 500, "res" => "Type download hanya Kanban dan DN"];
            $this->fb($fb);
        }

        $data = $this->model->update("master","vendor_code = '$vendor_code'",$updateDate);
        if(empty($data)){
            $fb = ["statusCode" => 401, "res" => "Data kosong"];
            $this->fb($fb);
        }

        $fb = ["statusCode" => 200, "res" => $data];
        $this->fb($fb);
    }

    function openStatus()
    {
        $jsonInput = json_decode(file_get_contents('php://input'), true);
        $vendor_code = $jsonInput["vendor_code"] ?? null;
        $typeDownload = $jsonInput["typeDownload"] ?? null;

        $this->form_validation->set_data($jsonInput);
        $this->form_validation->set_rules('vendor_code', 'Vendor Code', 'trim|required');
        $this->form_validation->set_rules('typeDownload', 'Vendor Code', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $fb = ["statusCode" => 401, "res" => validation_errors()];
            $this->fb($fb);
        }

        $updateDate = [];
        if($typeDownload == "kanban"){
            $updateDate["download_kanban"] = "0";
        }else if($typeDownload == "dn"){
            $updateDate["download_dn"] = "0";
        }else{
            $fb = ["statusCode" => 500, "res" => "Type download hanya Kanban dan DN"];
            $this->fb($fb);
        }

        $data = $this->model->update("master","vendor_code = '$vendor_code'",$updateDate);
        if(empty($data)){
            $fb = ["statusCode" => 401, "res" => "Data kosong"];
            $this->fb($fb);
        }

        $fb = ["statusCode" => 200, "res" => $data];
        $this->fb($fb);
    }

    function printDN()
    {
        ob_start(); 
        $vendor_code = $this->input->get("vendor_code");
        $vendor_alias = $this->input->get("vendor_alias");
        
        $get_data = $this->model->gd("master","*,SUM(order_kbn) as total_kbn","vendor_code = '$vendor_code' GROUP BY order_no","result");

        if(empty($get_data)){
            $fb = ["statusCode" => 404, "res" => "Data Kosong"];
            $this->fb($fb);
        }
        
        $html = '
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>QR Code dan Barcode</title>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap.min.css1" />
                <style type="text/css">
                    @page {
                        size:A4,
                        margin:3rem 1re 3rem 1rem;
                    }
                    @font-face {
                        font-family: "source_sans_proregular";           
                        src: local("Source Sans Pro"), url("fonts/sourcesans/sourcesanspro-regular-webfont.ttf") format("truetype");
                        font-weight: normal;
                        font-style: normal;

                    }        
                    body{
                        font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;            
                    }
                    .page_break {
                        page-break-before: always;
                        position:relative;
                        min-height:340px;
                    }
                    .page_break:first-of-type {
                        page-break-before: avoid; /* Prevent break before the first element */
                    }
                </style>
            </head>
            <body style="font-size:10px !important;">';
        $dompdf = new Dompdf();
        foreach ($get_data as $get_data) {
            $listOrder = $this->model->gd("master","*","order_no = '".$get_data->order_no."' AND vendor_code = '$vendor_code'","result");

            $orderNo = $get_data->order_no;

            //BUAT BARCODE
            $barcode_generator = new BarcodeGeneratorHTML();
            $barcodeOrderNo = $barcode_generator->getBarcode($orderNo, $barcode_generator::TYPE_CODE_128,2,30);

            // BUAT QR CODE
            // Create an instance of QROptions to set the QR code settings
            $options = new QROptions([
                'eccLevel' => QRCode::ECC_L, // Error correction level (L, M, Q, H)
                'addQuietzone' => false,
                'scale' => 5, // Scale doesn't affect SVG size
                'imageBase64' => true, // Whether to output as a base64 image
            ]);

            // Create a new QRCode instance with the options
            $qrcode = new QRCode($options);
            $qrCodeOrderNo = $qrcode->render($orderNo);

            $ttd = "(___________________)";
            
            $listPart = "";
            if(!empty($listOrder)){
                $no = 1;
                foreach ($listOrder as $listOrder) {
                    $listPart .= '
                    <tr style="text-align:center; vertical-align:middle;">
                        <td>'.$no++.'</td>
                        <td>'.$listOrder->part_no.'</td>
                        <td>'.$listOrder->job_no.'</td>
                        <td>'.$listOrder->part_name.'</td>
                        <td>'.$listOrder->order_pcs.'</td>
                        <td>'.$listOrder->order_kbn.'</td>
                        <td>'.($listOrder->order_pcs * $listOrder->order_kbn).'</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>';
                }
            }

            $html .= '
            <div class="page_break">
                <table style="width:100%">
                    <tr>
                        <td align="center" style="width:70%; vertical-align:middle !important; font-size:30px; font-weight:bold;">Delivery Notes</td>
                        <td style="width:30%">'.$barcodeOrderNo.'<h4 style="margin:0; font-size:17pt;">'.$get_data->order_no.'</h4></td>
                    </tr>
                </table>

                <table style="width:100%">
                    <tr>
                        <td style="width:33%;">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>Vendor Code</td>
                                        <td>:</td>
                                        <td>'.$get_data->vendor_code.'</td>
                                    </tr>
                                    <tr>
                                        <td>Vendor Name</td>
                                        <td>:</td>
                                        <td>'.$get_data->vendor_name.'</td>
                                    </tr>
                                    <tr>
                                        <td>Vendor Site</td>
                                        <td>:</td>
                                        <td>'.$get_data->vendor_site.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="width:33%;">
                        </td>
                        <td style="width:33%; vertical-align:top;">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>Transporter</td>
                                        <td>:</td>
                                        <td>'.$get_data->lp.'</td>
                                    </tr>
                                    <tr>
                                        <td>Group Route</td>
                                        <td>:</td>
                                        <td>'.$get_data->route.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
                
                <table style="width:100%">
                    <tr>
                        <td style="width:33%; vertical-align:top;">
                            <h3 style="font-weight:bold; margin:0;">ORDER</h3>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>Date</td>
                                        <td>:</td>
                                        <td>'.date("d-M-Y",strtotime($get_data->order_date)).'</td>
                                    </tr>
                                    <tr>
                                        <td>Lane No</td>
                                        <td>:</td>
                                        <td>'.$get_data->lane.'</td>
                                    </tr>
                                    <tr>
                                        <td>Delivery / Day</td>
                                        <td>:</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Category</td>
                                        <td>:</td>
                                        <td>'.$get_data->part_category.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="width:33%;">
                            <h3 style="font-weight:bold; margin:0;">DELIVERY</h3>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>Shop</td>
                                        <td>:</td>
                                        <td>'.$get_data->shop_code.'</td>
                                    </tr>
                                    <tr>
                                        <td>Date</td>
                                        <td>:</td>
                                        <td>'.date("d-M-Y",strtotime($get_data->del_date)).'</td>
                                    </tr>
                                    <tr>
                                        <td>Del. Cycle</td>
                                        <td>:</td>
                                        <td>'.$get_data->del_time.' / '.$get_data->del_cycle.'</td>
                                    </tr>
                                    <tr>
                                        <td>Plant Site</td>
                                        <td>:</td>
                                        <td>'.$get_data->plant_code.'</td>
                                    </tr>
                                    <tr>
                                        <td>Parking No</td>
                                        <td>:</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="width:33%; position:relative;">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>DN No</td>
                                        <td>:</td>
                                        <td style="padding-right:40px;">'.$get_data->order_no.'</td>
                                    </tr>
                                    <tr>
                                        <td>Page</td>
                                        <td>:</td>
                                        <td>1/1</td>
                                    </tr>
                                    <tr>
                                        <td>PO No</td>
                                        <td>:</td>
                                        <td>'.$get_data->po_number.'</td>
                                    </tr>
                                    <tr>
                                        <td>Total KBN</td>
                                        <td>:</td>
                                        <td>'.$get_data->total_kbn.'</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="position:absolute; top:10px; right:20px; width:60px; height:60px; padding:8px; border:1px solid;">
                                <img src="'.$qrCodeOrderNo.'" width="100%">
                            </div>
                        </td>
                    </tr>
                </table>
                
                <table style="margin-top:10px; width:100%; border-collapse: collapse;" border="1">
                    <thead>
                        <tr style="text-align:center;">
                            <th rowspan="2">No</th>
                            <th rowspan="2">Material No</th>
                            <th rowspan="2">Job No</th>
                            <th rowspan="2">Material Name</th>
                            <th rowspan="2">Qty/Box</th>
                            <th rowspan="2">Total Kanban</th>
                            <th rowspan="2">Total Qty (PCS)</th>
                            <th colspan="3">Confirmation Check</th>
                            <th rowspan="2">Remark</th>
                        </tr>
                        <tr style="text-align:center; vertical-align:middle;">
                            <th>Vendor</th>
                            <th>Log Partner</th>
                            <th>ADM</th>
                        </tr>
                    </thead>
                    <tbody>
                    '.$listPart.'
                    </tbody>
                </table>

                <table style="width:100%; margin-top:20px; font-size:8pt; position: absolute; bottom:100px; right:0;">
                    <tr>
                        <td style="width:40%"></td>
                        <td style="width:60%;">
                            <table style="width:100%;">
                                <tbody>
                                    <tr style="font-weight:bold; text-align:center;">
                                        <td colspan="2" style="width:33%">SUPPLIER</td>
                                        <td colspan="2" style="width:33%">TRANSPORTER</td>
                                        <td colspan="2" style="width:33%">PT. ADM</td>
                                    </tr>
                                    <tr style="text-align:center;">
                                        <td>APPROVED</td>
                                        <td>PREPARED</td>
                                        <td>APPROVED</td>
                                        <td>PREPARED</td>
                                        <td>APPROVED</td>
                                        <td>PREPARED</td>
                                    </tr>
                                    <tr style="text-align:center; vertical-align:bottom;">
                                        <td style="height:60px">'.$ttd.'</td>
                                        <td>'.$ttd.'</td>
                                        <td>'.$ttd.'</td>
                                        <td>'.$ttd.'</td>
                                        <td>'.$ttd.'</td>
                                        <td>'.$ttd.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            ';
        }
        $html .= '
            </body>
        </html>';
        // DomPDF Operations
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        ob_end_clean();
        $dompdf->get_canvas()->get_cpdf()->setEncryption('adm');
        $dompdf->stream($vendor_alias." (".$vendor_code.").pdf", ["Attachment" => false]);
        $updateDate["download_dn"] = "1";
        $data = $this->model->update("master","vendor_code = '$vendor_code'",$updateDate);
        if(empty($data)){
            $fb = ["statusCode" => 401, "res" => "Data kosong"];
            $this->fb($fb);
        }
        exit();
    }
}
