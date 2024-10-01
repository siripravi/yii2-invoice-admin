<?php
use yii\helpers\Html;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
<!--        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <?php if (!Yii::$app->user->isGuest): ?>
            <div class="pull-left info">
                <p><?= Html::encode(Yii::$app->user->identity->name) ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
            <?php endif ?>
        </div>-->

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget([
            'options' => [
                'class' => 'sidebar-menu tree',
                'data-widget' => 'tree',
            ],
            'items' => Yii::$app->menu->getMenu('main-nav'),
        ]) ?>

    </section>

</aside>
