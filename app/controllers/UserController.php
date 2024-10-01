<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\controllers;

use app\base\actions\user;
use app\base\Controller;
use app\forms\user\Register;
use app\models\User as UserModel;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * User management.
 *
 * Allows to list, create or modify users.
 * 
 * @author skoro
 */
class UserController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'profile' => [
                'class' => user\Profile::className(),
                'layout' => '//main',
            ],
            'login' => [
                'class' => user\Login::className(),
                'layout' => '//main-login',
            ],
            'logout' => [
                'class' => user\Logout::className(),
            ],
            'register' => [
                'class' => user\Register::className(),
                'layout' => '//main-login',
            ],
            'password-request' => [
                'class' => user\PasswordRequest::className(),
                'layout' => '//main-login',
            ],
            'password-reset' => [
                'class' => user\PasswordReset::className(),
                'layout' => '//main-login',
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['viewAnyUser'],
                    ],
                    [
                        'actions' => ['delete', 'bulk-delete'],
                        'allow' => true,
                        'roles' => ['deleteAnyUser'],
                    ],
                    [
                        'actions' => ['profile', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'register', 'password-request',
                            'password-reset'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Bulk actions for index grid.
     * @param string $action Bulk action name (enable, disable, delete)
     */
    public function bulkActionIndex($action)
    {
        $users = $this->getBulkModels(UserModel::className());
        
        switch ($action) {
            case 'enable':
            case 'disable':
                if (Yii::$app->user->can('updateAnyUser')) {
                    foreach ($users as $user) {
                        $user->status = $action === 'enable' ? UserModel::STATUS_ENABLED : UserModel::STATUS_DISABLED;
                        $isChanged = $user->getDirtyAttributes(['status']);
                        if ($isChanged && $user->save()) {
                            $this->addFlash(self::FLASH_INFO, Yii::t('app', 'User <strong>{name}</strong> is {status}.', [
                                'name' => $user->name,
                                'status' => Yii::t('app', $action === 'enable' ? 'enabled' : 'disabled'),
                            ]));
                        }
                    }
                }
                break;

            case 'delete':
                $ids = ArrayHelper::getColumn($users, 'id');
                Yii::$app->session->set('bulk_users', $ids);
                $this->redirect(['bulk-delete']);
                return false;
        }
    }
    
    /**
     * Users list.
     * @since 0.2
     */
    public function actionIndex()
    {
        $userProvider = new ActiveDataProvider([
            'query' => UserModel::find(),
        ]);
        
        $register = new Register();
        $register->setScenario('admin');
        
        $request = Yii::$app->request;
        if (Yii::$app->user->can('createUser') && $request->isPost && 
                $register->load($request->post())) {
            if ($request->isAjax && !$request->isPjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($register);
            }
            if ($user = $register->register()) {
                $this->addFlash(self::FLASH_SUCCESS, Yii::t('app', 'User <b>{name}</b> created.', [
                    'name' => Html::encode($user->name),
                ]));
                $register = new Register();
            }
        }
        
        return $this->render('index', [
            'userProvider' => $userProvider,
            'register' => $register,
        ]);
    }
    
    /**
     * Delete user action.
     * @param integer $id user id
     * @since 0.2
     */
    public function actionDelete($id)
    {
        /** @var $user UserModel */
        $user = $this->findModel(UserModel::className(), $id);
        if ($user->delete()) {
            $this->addFlash(self::FLASH_SUCCESS, Yii::t('app', 'User <b>{name}</b> deleted.', [
                'name' => Html::encode($user->name),
            ]));
        }
        
        return $this->redirect(['index']);
    }
    
    /**
     * User bulk delete.
     * 
     * User IDs passed in a POST request. Page refresh leads to exception.
     * 
     * @throws \yii\web\BadRequestHttpException When action invoked directly.
     * @since 0.3
     */
    public function actionBulkDelete()
    {
        // Get user IDs from POST request or session.
        if (Yii::$app->request->isPost) {
            $ids = Yii::$app->request->post('id');
            foreach ($ids as $id) {
                $user = $this->findModel(UserModel::className(), $id);
                if ($user && $user->delete()) {
                    Yii::$app->session->addFlash(static::FLASH_INFO, Yii::t('app', 'User <strong>{name}</strong> deleted.', [
                        'name' => $user->name,
                    ]));
                }
            }
            return $this->redirect(['index']);
        } elseif (Yii::$app->session->has('bulk_users')) {
            $ids = Yii::$app->session->remove('bulk_users');
        } else {
            throw new \yii\web\BadRequestHttpException('Bad bulk request.');
        }
        
        $users = $this->getBulkModels(UserModel::className(), $ids);
        
        return $this->createConfirmation([
            'title' => Yii::t('app', 'Are you sure to delete users ?'),
            'message' => Yii::t('app', 'These users <strong>{users}</strong> will be deleted. This operation cannot be undo!', [
                'users' => implode(', ', array_map(function ($name) {
                    return Html::encode($name);
                }, ArrayHelper::getColumn($users, 'name'))),
            ]),
            'encodeMessage' => false,
            'button' => [
                'label' => Yii::t('app', 'Delete'),
                'options' => ['class' => 'btn btn-danger btn-flat'],
            ],
            'params' => [
                'id' => ArrayHelper::getColumn($users, 'id'),
            ],
            'icon' => 'fa fa-trash',
        ]);
    }
}
