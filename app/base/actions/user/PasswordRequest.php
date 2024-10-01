<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base\actions\user;

use Yii;
use app\base\Action;
use app\base\Controller;

/**
 * Request user password.
 *
 * @author skoro
 */
class PasswordRequest extends Action
{
    
    /**
     * @var string class name for Password Request form.
     */
    public $modelClass = 'app\forms\user\PasswordRequest';
    
    /**
     * @var string
     */
    public $view = 'passwordRequest';
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $model = new $this->modelClass;
        
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->sendEmail()) {
                    $this->controller->addFlash(Controller::FLASH_INFO, Yii::t('app', 'Check your email for further instructions.'));
                    return $this->controller->redirect(['user/login']);
                } else {
                    $this->controller->addFlash(Controller::FLASH_ERROR, Yii::t('app', 'Sorry, we are unable to reset password for provided email.'));
                }
            }
        }
        
        return $this->render([
            'model' => $model,
        ]);
    }
    
}
