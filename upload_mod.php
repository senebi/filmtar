<?php
$up_id = uniqid(); 
?>

<div>
  <form action="upload_result_mod.php" method="post" target="upload_frame_mod" enctype="multipart/form-data" name="form1_mod" id="ulf_mod" class="ulf">
    Kép választása:
  <!--APC hidden field-->
    <input type="hidden" name="APC_UPLOAD_PROGRESS" id="progress_key_mod" value="<?php echo $up_id; ?>"/>
    <input name="modkepul" type="file" id="modkepul" size="30"/>
    <br />

    <!--<input name="toltes_mod" type="submit" id="toltes_mod" value="Feltöltés" />-->
  </form>
  </div>
  
  <div id="folyamat_doboz_mod">
	<img src="img/process.gif" alt="folyamatban" /><br />
	feltöltés folyamatban<span id="pottyok_mod">.</span>
  </div>
  
  <iframe id="upload_frame_mod" name="upload_frame_mod" frameborder="0" border="0" src="upload_result_mod.php" scrolling="yes" scrollbar="yes"></iframe>