<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTD CALCULLATION SYSTEM</title>
    <?php $this->load->view("template/head"); ?>
</head>
<body class="pb-5" style="overflow-x:hidden;">
    <?php $this->load->view("template/navbar"); ?>
    <div class="pl-3 pr-3">
        <?php $this->load->view("admin/".$body); ?>
    </div>
</body>
</html>
<?php $this->load->view("template/footer"); ?>
<?php
if(!empty($jsadd)){
    $this->load->view("js/".$jsadd);
}
?>