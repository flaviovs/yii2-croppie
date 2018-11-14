Yii2 + Croppie integration Extension
====================================

This extension integrates [Croppie] with Yii2 apps. It provides assets
and a widget to facilitate uploads of cropped images.


Usage
-----

Yii2-Croppie provides an widget plus an upload file class that
inherits from
[yii\web\UploadedFile](https://www.yiiframework.com/doc/api/2.0/yii-web-uploadedfile). That
allows you to use native semantics for uploading files with this
extension.


## On your model

```php
class Form extends \yii\base\Model
{
    public $image;

    public function rules()
    {
        return [
            ['image', 'image', 'enableClientValidation' => FALSE],
        ];
    }
}
```

**IMPORTANT**: If you use `image` validation in your model
(recommended), you **must** disable client validation, as shown above.


## In your view

```php
$form->field($model, 'image')
    ->widget(\fv\yii\croppie\Widget::class)
```


## In your controller

```php
if ($app->request->isPost) {
   $form->image = \fv\yii\croppie\UploadedFile::getInstance($form, 'image');

   if ($form->validate())
   {
        $name = '/tmp/' . $form->image->baseName
            . '.' . $form->image->extension;
            
        $form->image->saveAs($name);
        $app->session->setFlash('success',
            'Saved to ' . Html::encode($name));
        return $this->refresh();
    }
}
```


Notes
-----

No actual file upload by the browser happens. Consequently, the
extension never gets to know the name of the file that got sent for
cropping, and for compatibility with `yii\web\UploadedFile`, a random
name is generated.


Issues
------

See http://github.com/flaviovs/yii2-croppie


[Croppie]: https://foliotek.github.io/Croppie/
