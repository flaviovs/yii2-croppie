<?php

namespace fv\yii\croppie;

class CroppieAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/croppie';

    public $js = ['croppie.js'];

    public $css = ['croppie.css'];

    public $publishOptions = [
        'only' => ['croppie.js', 'croppie.css'],
    ];

    public $depends = [
        \yii\web\JqueryAsset::class,
    ];
}
