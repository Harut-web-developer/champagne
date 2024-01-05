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
//        '/js/apexcharts.js',
        '/js/main.js',
//        '/js/dashboards-analytics.js',
//        '/js/jquery.js',
        '/js/script.js?v=15132',
        'https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.2.1/exceljs.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
        '/js/select2.min.js',
//        '/js/charts.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}
