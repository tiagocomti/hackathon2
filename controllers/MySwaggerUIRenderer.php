<?php
namespace app\controllers;
use yii2mod\swagger\SwaggerUIRenderer;

class MySwaggerUIRenderer extends SwaggerUIRenderer
{
    /**
     * @var string the rest url configuration
     */
    public $restUrl;

    /**
     * @var string base swagger template
     */
    public $view = '@app/views/site/index';

    /**
     * @var null|string|false the name of the layout to be applied to this controller's views.
     * This property mainly affects the behavior of [[render()]].
     * Defaults to null, meaning the actual layout value should inherit that from [[module]]'s layout value.
     * If false, no layout will be applied.
     */
    public $layout = false;
    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->controller->layout = $this->layout;

        return $this->controller->render($this->view, [
            'restUrl' => $this->restUrl,
        ]);
    }
}