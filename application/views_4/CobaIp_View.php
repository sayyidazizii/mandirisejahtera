<font color="orange">
<?php echo $this->session->flashdata('hasil'); ?>
</font>
<table border="1">
    <tr><th>ID</th><th>NAMA</th><th>NOMOR</th><th></th></tr>
    <?php
   $datappob = json_decode($this->curl->simple_get($this->API));
    
    ?>
</table>