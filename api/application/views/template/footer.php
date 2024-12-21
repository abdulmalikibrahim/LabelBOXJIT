<div class="w-100 text-light text-center" style="height:30px; position:absolute; bottom:0; left:0; background:#0077C3;"><label class="m-0" style="font-family:arial; font-size:10px;">Created By Abdul Malik Ibrahim</label></div>
<script src="<?php echo base_url('assets/jquery/jquery.min.js'); ?>"></script>
<script src="https://cdn.datatables.net/v/bs4/dt-2.1.0/datatables.min.js"></script>
<script>
	$("#datatable").DataTable();
</script>
<?php
if(!empty($this->session->flashdata("swal"))){
    echo $this->session->flashdata("swal");
}
?>