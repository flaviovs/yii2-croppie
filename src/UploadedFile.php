<?php

namespace fv\yii\croppie;


class UploadedFile extends \yii\web\UploadedFile
{

    public static function getInstance($model, $attribute)
    {
        $form_name = $model->formName();
        $data = \Yii::$app->request->post();

        if (!isset($data[$form_name])) {
            \Yii::warning("Form $form_name not found in POST", __METHOD__);
            return NULL;
        }

        if (!isset($data[$form_name][$attribute])) {
            \Yii::error("Image data not found in POST data", __METHOD__);
            return NULL;
        }

        $value = $data[$form_name][$attribute];

        if (
            !preg_match(
                '~^data:(image/(png|jpeg));base64,(.+)~',
                $value,
                $matches
            )
        ) {
            // Invalid.
            \Yii::error("Invalid image data: $value", __METHOD__);
            return NULL;
        }

        $image_data = base64_decode($matches[3], TRUE);
        if (!$image_data) {
            \Yii::error("Invalid base64", __METHOD__);
            return NULL;
        }

        $tempName = tempnam(sys_get_temp_dir(), 'croppie');

        $size = file_put_contents($tempName, $image_data);
        if ($size === FALSE) {
            \Yii::error("Failed to write", __METHOD__);
            return NULL;
        }

        return new static([
            'name' => mb_strtolower(
                \Yii::$app->security->generateRandomString(12)
                . '.'
                . $matches[2]
            ),
            'tempName' => $tempName,
            'type' => $matches[1],
            'size' => $size,
            'error' => UPLOAD_ERR_OK,
        ]);
    }


    public function saveAs($file, $deleteTempFile = TRUE)
    {
        \Yii::trace("Saving \"$this->tempName\" to \"$file\"", __METHOD__);
        return $deleteTempFile
            ? rename($this->tempName, $file)
            : copy($this->tempName, $file);
    }


    public function __destruct()
    {
        try {
            unlink($this->tempName);
        } catch (\yii\base\ErrorException $ex) {
            // *NOTHING*
        }
    }
}
