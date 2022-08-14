<?php
include('controllers/WaveFormController.php');
include('models/WaveParse.php'); ?>
<p> <?= 'This is return json from WaveFormController'?> </p>

<?php
$waveForm = new WaveFormController();
$result =  $waveForm->Wave();
echo $result;
?>