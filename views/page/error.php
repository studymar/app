<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">
    <div slot="column-1">
      <!--<h3>Ups, da ist etwas schief gegangen!</h3>-->
      <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
      </div>
    </div>

</div>
