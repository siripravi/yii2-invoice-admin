<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base\actions\user;

use app\base\Action;
use app\components\Param;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * User register action.
 *
 * To disable user registration, put to the app's config
 * parameter `disableUserRegister => true`.
 *
 * @author skoro
 */
class Register extends Action
{
    
    /**
     * @var string class name for Register form.
     */
    public $modelClass = 'app\forms\user\Register';
    
    /**
     * @var string
     */
    public $view = 'register';
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Param::value('User.disableUserRegister', false)) {
            throw new NotFoundHttpException();
        }
        
        if (!Yii::$app->user->isGuest) {
            return $this->controller->goBack();
        }
        
        $model = new $this->modelClass;
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->register()) {
                $this->controller->addFlash('info',
                    Yii::t('app', 'Registration successful. Now you can <a href="{login}">login</a>.', [
                        // FIXME: use app's login route.
                        'login' => Url::to(['user/login'])
                    ])
                );
                return $this->controller->goHome();
            }
        }
        
        if (!Yii::$app->request->isPjax && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        return $this->render([
            'model' => $model,
        ]);
    }

}
