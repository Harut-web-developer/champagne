<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];

    public $js = [
        '/js/popper.js',
        '/js/bootstrap.js',
        '/js/perfect-scrollbar.js',
        '/js/menu.js',
        '/js/main.js',
        '/js/script.js',
        '/js/plugins/html5-3.6-respond-1.1.0.min.js',
        '/js/plugins/jQuery.print.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.2.1/exceljs.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
        '/js/select2.min.js',
     ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}
