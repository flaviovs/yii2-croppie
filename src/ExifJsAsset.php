<?php

namespace fv\yii\croppie;

class ExifJsAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/exif-js';

    public $js = ['exif.js'];

    public $publishOptions = [
        'only' => ['exif.js'],
    ];
}
