<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\widgets;

use app\assets\AnimateAsset;
use app\assets\BootstrapNotifyAsset;
use Yii;
use yii\base\Widget;
use yii\helpers\Json;

/**
 * Bootstrap notify widget.
 *
 * @link http://bootstrap-notify.remabledesigns.com
 * @author skoro
 */
class Notify extends Widget
{
    
    /**
     * @var array
     */
    public $animate = [
        'enter' => 'animated fadeInRight',
        'exit' => 'animated fadeOutRight',
    ];
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $session = Yii::$app->getSession();
        $flashes = $session->getAllFlashes(true);
        
        if (empty($flashes)) {
            return;
        }
        
        $view = $this->getView();
        BootstrapNotifyAsset::register($view);
        if ($this->animate) {
            // FIXME: in pjax responses animate.css does not included in body.
            AnimateAsset::register($view);
        }
        
        foreach ($flashes as $type => $messages) {
            $settings = [];
            switch ($type) {
                case 'success': case 'info':
                case 'warning':
                case 'danger': case 'error':
                    $settings['type'] = $type;
                    break;
            }
            
            if ($this->animate) {
                $settings['animate'] = $this->animate;
            }
            
            foreach ($messages as $message) {
                $settings = Json::encode($settings);
                $view->registerJs("\$.notify('$message', $settings);");
            }
        }
    }
}
