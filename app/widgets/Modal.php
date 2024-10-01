<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\widgets;

use yii\helpers\Url;

/**
 * Modal widget.
 *
 * ~~~php
 * Modal::begin([
 *     'header' => '<h2>Hello world</h2>',
 *     'toggleButton' => ['label' => 'click me'],
 *     'remote' => ['demo'],
 * ]);
 *
 * echo 'Say hello...';
 *
 * Modal::end();
 *
 * // In controller:
 * public function actionDemo() {
 *     return 'Put content to modal.';
 * }
 * ~~~
 *
 * @author skoro
 */
class Modal extends \yii\bootstrap5\Modal
{
    
    /**
     * @var array|string fetch content from remote source.
     */
    public $remote;
    public $header;
    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();
        if ($this->remote) {
            $id = $this->options['id'];
            $url = Url::to($this->remote);
            $this->getView()->registerJs("Admin.Modal.remote('#$id', '$url');");
        }
    }
}
