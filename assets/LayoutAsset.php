<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LayoutAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css',
        'https://fonts.googleapis.com/icon?family=Material+Icons',
        'css/layout.scss',
        'css/menu.scss',
        'css/customlayout.scss',
    ];
    public $js = [
        //'https://code.jquery.com/jquery-3.3.1.slim.min.js',
        //'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js',
        //'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js',
        //['https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js','position' => \yii\web\View::POS_HEAD],
        //['https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js','position' => \yii\web\View::POS_HEAD],
        'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js',
        'js/menu.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
        //'yii\bootstrap4\BootstrapAsset',        
    ];
}
