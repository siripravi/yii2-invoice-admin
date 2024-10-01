<?php

use app\components\Param;
use app\helpers\UserHelper;
use app\widgets\Box;
use app\widgets\ItemList;
use yii\bootstrap5\Tabs;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $model app\forms\user\Profile */
/** @var $tab string current active tab */

$this->title = Yii::t('app', 'User Profile');
if (Yii::$app->user->can('viewAnyUser')) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    
    <div class="col-md-3">
        <?php Box::begin([
            'box' => Box::BOX_PRIMARY,
            'bodyOptions' => ['class' => 'box-profile'],
        ]) ?>
            <?= Html::img(Param::value('User.noAvatarImage'), ['class' => 'profile-user-img img-responsive img-circle']) ?>
            <h3 class="profile-username text-center">
                <?= Html::encode($model->name) ?>
            </h3>
            <p class="text-muted text-center">
                <?= Yii::t('app', 'Member since {date}', ['date' => Yii::$app->formatter->asDate(Yii::$app->user->identity->created_at)]) ?>
            </p>
            <?= ItemList::widget([
                'items' => [
                    [
                        'title' => 'ID',
                        'value' => $model->getUser()->id,
                    ],
                    [
                        'title' => Yii::t('app', 'Status'),
                        'value' => UserHelper::status($model->getUser()),
                    ],
                    [
                        'title' => Yii::t('app', 'Last login'),
                        'value' => Yii::$app->formatter->asRelativeTime($model->getUser()->logged_at),
                    ],
                ],
            ]) ?>
        <?php Box::end() ?>
    </div>
    
    <div class="col-md-9">
        <div class="nav-tabs-custom profile-tabs">
            <?= Tabs::widget([
                'items' => [
                    [
                        'label' => Yii::t('app', 'Account'),
                        'content' => $this->render('_profile_account', ['model' => $model]),
                        'active' => $tab == 'account',
                    ],
                    [
                        'label' => Yii::t('app', 'Administer'),
                        'content' => $this->render('_profile_admin', ['model' => $model]),
                        'visible' => Yii::$app->user->can('updateAnyUser'),
                        'active' => $tab == 'admin',
                    ],
                ],
            ]) ?>
        </div>
    </div>
    
</div>

