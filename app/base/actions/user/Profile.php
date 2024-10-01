<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base\actions\user;

use app\base\Action;
use app\base\Controller;
use app\models\User;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * User profile.
 *
 * @author skoro
 */
class Profile extends Action
{
    
    /**
     * @var string class name for Register form.
     */
    public $modelClass = 'app\forms\user\Profile';
    
    /**
     * @var string
     */
    public $view = 'profile';
    
    /**
     * @inheritdoc
     */
    public function run($id = null, $tab = 'account')
    {
        if (Yii::$app->user->isGuest) {
            return $this->controller->redirect(['user/login']);
        }
        
        if ($id === null) {
            $user = Yii::$app->user->getIdentity();
        } elseif (Yii::$app->user->can('updateAnyUser')) {
            $user = $this->controller->findModel(User::className(), $id);
        } else {
            throw new \yii\web\ForbiddenHttpException();
        }
        
        $model = new $this->modelClass($user);
        
        if (Yii::$app->request->isPost) {
            if (Yii::$app->user->can('updateAnyUser')) {
                $model->setScenario('admin');
            }
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $this->controller->addFlash(Controller::FLASH_INFO, Yii::t('app', 'Changes has saved.'));
                $model->reset();
            }
        }
        
        if (!Yii::$app->request->isPjax && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        return $this->render([
            'model' => $model,
            'tab' => $tab,
        ]);
    }
    
}
