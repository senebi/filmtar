<?php
$up_uj_id = uniqid(); 
?>

<div>
  <form action="upload_result_uj.php" method="post" target="upload_frame_uj" enctype="multipart/form-data" name="form1_uj" id="ulf_uj" class="ulf">
	Kép választása:
  <!--APC hidden field-->
	<input type="hidden" name="APC_UPLOAD_PROGRESS" id="progress_key_uj" value="<?php echo $up_uj_id; ?>"/>
	<input name="ujkepul" type="file" id="ujkepul" size="30" />
	<br />
  
	<!--<input name="toltes_uj" type="submit" id="toltes_uj" value="Feltöltés" />-->
  </form>
</div>

<div id="folyamat_doboz_uj">
  <img src="img/process.gif" alt="folyamatban" /><br />
  feltöltés folyamatban<span id="pottyok_uj">.</span>
</div>

<iframe id="upload_frame_uj" name="upload_frame_uj" frameborder="0" border="0" src="upload_result_uj.php" scrolling="yes" scrollbar="yes"></iframe>