<?php
/**
 * Created by PhpStorm.
 * User: ShadoWoARM
 * Date: 7/2/2565
 * Time: 20:03
 */

namespace RJHCR\assets;

use yii\web\AssetBundle;

class MintyAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/minty.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}
{

}