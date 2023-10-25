<?php

namespace fv\yii\croppie;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;


class Widget extends \yii\widgets\InputWidget
{
    public $format;

    public $containerOptions = [];

    public $clientOptions = [];

    public $onClientUpdate;

    public $uploadButtonOptions = [];

    public $rotateButtonOptions = [];

    public $rotateCcwLabel;

    public $rotateCwLabel;

    public $layout = "{canvas}\n{upload}\n{rotate-cw}\n{rotate-ccw}";

    public $size = 'viewport';

    public $url;
    
    public $zoom;

    public function init()
    {
        parent::init();

        Html::addCssClass($this->containerOptions, ['croppie-widget']);
        Html::addCssClass($this->options, ['croppie-widget__data']);

        Html::addCssClass(
            $this->uploadButtonOptions,
            ['croppie-widget__upload btn btn-default']
        );

        Html::addCssClass(
            $this->rotateButtonOptions,
            ['croppie-widget__rotate btn btn-default']
        );

        if ($this->format === NULL) {
            $this->format = 'png';
        }

        if (!isset($this->clientOptions['enableExif'])) {
            $this->clientOptions['enableExif'] = true;
        }

        if (!isset($this->clientOptions['enableOrientation'])) {
            $this->clientOptions['enableOrientation'] = true;
        }

        if (!isset($this->clientOptions['enableExif'])) {
            $this->clientOptions['enableExif'] = true;
        }

        if (!isset($this->clientOptions['viewport'])) {
            $this->clientOptions['viewport'] = [
                'width' => 200,
                'height' => 200,
            ];
        }

        if (!isset($this->clientOptions['boundary'])) {
            $this->clientOptions['boundary'] = $this->clientOptions['viewport'];
        }

        if ($this->rotateCcwLabel === NULL) {
            $this->rotateCcwLabel = \Yii::t('app', 'Rotate counter-clockwise');
        }

        if ($this->rotateCwLabel === NULL) {
            $this->rotateCwLabel = \Yii::t('app', 'Rotate clockwise');
        }

    }


    protected function renderCanvas()
    {
        $id = $this->id . '__canvas';

        $js_options = $this->clientOptions
            ? Json::encode($this->clientOptions)
            : '';

        $format = Json::encode($this->format);
        $size = Json::encode($this->size);

        if ($this->onClientUpdate) {
            $callback = 'function($widget, $input) {'
                . $this->onClientUpdate
                . ';}';
        } else {
            $callback = 'null';
        }

        $this->view->registerJs(<<<EOJS
jQuery('#{$id}')
    .on('update.croppie',
        function(ev, data) {
			croppieWidget.updateData(ev, data, $format, $size, $callback);
        })
    .croppie($js_options);
EOJS
        );

        if ($this->url) {
            $bindOptions = [
                'url' => $this->url,
            ];
            
            if ($this->zoom !== NULL) {
                $bindOptions['zoom'] = $this->zoom;
            }
            
            $this->view->registerJs(
                "jQuery('#{$id}').croppie('bind', " . Json::encode($bindOptions) . ")"
            );
        }


        return Html::tag('div', '', [
            'id' => $id,
            'class' => ['croppie-widget__canvas'],
        ]);
    }


    protected function renderRotateButton($label, $degrees)
    {
        return Html::button(
            $label,
            $this->rotateButtonOptions + ['data-croppie-rotate-deg' => $degrees]
        );
    }


    protected function renderRotateCcwButton()
    {
        return $this->renderRotateButton($this->rotateCcwLabel, 90);
    }


    protected function renderRotateCwButton()
    {
        return $this->renderRotateButton($this->rotateCwLabel, -90);
    }


    protected function renderUploadButton()
    {
        $label = ArrayHelper::remove(
            $this->uploadButtonOptions,
            'label',
            \Yii::t('app', 'Upload')
        );

        $this->view->registerJs(<<<EOJS
jQuery('#{$this->id}-upload').on('change', croppieWidget.onUploadChange);
EOJS
        );

        return Html::tag(
            'label',
            $label . ' ' . Html::fileInput(
                $this->id . '__upload',
                $label,
                $this->uploadButtonOptions +
                [
                    'id' => $this->id . '-upload',
                    'accept' => 'image/*',
                ]
            ),
            ['class' => 'btn btn-primary btn-file']
        );
    }


    protected function renderElement($el)
    {
        $el = $el[0];

        if ($el === '{canvas}') {
            return $this->renderCanvas();
        } elseif ($el === '{upload}') {
            return $this->renderUploadButton();
        } elseif ($el === '{rotate-cw}') {
            return $this->renderRotateCwButton();
        } elseif ($el === '{rotate-ccw}') {
            return $this->renderRotateCcwButton();
        }

        \Yii::warning("Unknown layout element: $el", __METHOD__);

        return $el;
    }


    public function run()
    {
        Asset::register($this->view);
        if (!empty($this->clientOptions['enableExif'])) {
            ExifJsAsset::register($this->view);
        }

        $tag = ArrayHelper::remove($this->options, 'tag', 'div');

        $out = preg_replace_callback(
            '/{[a-z-]+}/',
            [$this, 'renderElement'],
            $this->layout
        );

        return Html::tag(
            $tag,
            $out . parent::renderInputHtml('hidden'),
            $this->containerOptions
        );
    }
}
