<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base\actions\user;

use app\base\Action;
use app\base\Controller;
use app\components\Param;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * User login action.
 *
 * @todo enable/disable social links.
 * @author skoro
 */
class Login extends Action
{
    
    /**
     * @var string class name for Login form.
     */
    public $modelClass = 'app\forms\user\Login';
    
    /**
     * @var string
     */
    public $view = 'login';
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->controller->goBack();
        }
        
        $model = new $this->modelClass;
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->controller->goBack();
            } else {
                $this->controller->addFlash(Controller::FLASH_ERROR, Yii::t('app', 'Login to your account failed.'));
                $model->password = '';
            }
        }
        
        if (!Yii::$app->request->isPjax && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        return $this->render([
            'model' => $model,
            'disableUserRegister' => Param::value('User.disableUserRegister'),
        ]);
    }
    
}
