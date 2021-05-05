<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\LayoutAsset;

LayoutAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <div>
        <div id="bar"></div>
        <div id="top">
        <div id="wrapper">

            <!-- Header -->
            <div id="header">
            <h1><a href="/">TTKV Harburg-Land e.V.</a></h1>
            <p class="title">Tischtennis in der südlichen Metropolregion von Hamburg</p>
            </div> <!-- Header end -->

            <!-- Menu -->
            <div id="menu-box" class="cleaning-box">
                <ul id="menu">
                    <li class="">
                        <a href="" target="" class="first">Voting</a>
                    </li>
                </ul>
              <?php /* echo Yii::$app->runAction('menu/index', []); */ ?>
            </div> <!-- Menu end -->

            <hr class="noscreen" />
            <div id="skip-menu"></div>

            <div id="content"> 

                <div id="column-1">
                    <?= $content ?>
                </div> <!-- Column 1 end -->

                <?php /*
                <div id="column-2">
                    <?= $this->render('_relatedMenu'); ?>
                </div> <!-- Column 2 end -->
                 * 
                 */
                ?>
                <div class="cleaner">&nbsp;</div>
            </div>
            <!-- Content of the site end -->


            <div id="footer">
                <div id="footer-in">
                    <ul>
                        <li><a href="<?= Url::toRoute(['content/impressum']) ?>" class=" first active">Impressum</a> |</li>
                        <li><a href="<?= Url::toRoute(['content/datenschutz']) ?>">Datenschutz</a> |</li>
                    </ul>

                </div>
            </div> <!-- Footer end -->

        </div> <!-- Wrapper end -->
        </div> <!-- top -->
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
