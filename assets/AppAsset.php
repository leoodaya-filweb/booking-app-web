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
        'css/sb-admin-2.min.css',
        'css/sb-admin-2.css',
        'css/aos.css'
    ];
    public $js = [
        'vendor/jquery/jquery.min.js',
        'vendor/bootstrap/js/bootstrap.bundle.min.js',

        'js/sb-admin-2.js',
        'js/sb-admin-2.min.js',
        'js/aos.js'
        // 'vendor/chart.js/Chart.min.js',
        // 'vendor/datatables/jquery.dataTables.min.js',
        // 'vendor/datatables/dataTables.bootstrap5.min.js',
        // 'vendor/datatables/dataTables.bootstrap5.min.css',
        // 'vendor/datatables/dataTables.bootstrap5.css',
        // 'vendor/datatables/jquery.dataTables.min.css',
        // 'vendor/datatables/jquery.dataTables.css',
        

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}
