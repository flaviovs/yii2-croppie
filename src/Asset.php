<?php

namespace fv\yii\croppie;

class Asset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/flaviovs/yii2-croppie/assets';

    public $js = ['script.js'];

    public $css = ['styles.css'];

    public $depends = [CroppieAsset::class];
}
