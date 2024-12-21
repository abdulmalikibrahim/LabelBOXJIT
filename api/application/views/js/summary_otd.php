<script>
    function checkNull() {
        var otd_adjust = <?php echo $otd_adjust; ?>; // Ambil nilai dari PHP

        for (var i = 0; i <= otd_adjust; i++) {
            $('.cell-' + i).addClass('highlight-green');
        }

        $('.td-row').each(function() {
            if ($(this).text().trim() === '0') {
                $(this).text('');
            }
            
            if ($(this).text().trim() === '0%') {
                $(this).text('');
            }
        });
    }
    checkNull();

    $("#adjust, #month, #year").change(function() {
        $("#form-adjust").submit();
        $("#month").attr("disabled",true);
        $("#year").attr("disabled",true);
        $("#adjust").attr("disabled",true);
    });
</script>