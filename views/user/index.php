<?php

use app\models\User;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

          
  <div class="d-flex align-items-between justify-content-between ">
      
      <?= Html::beginForm(['user/index'], 'POST', ['class' => '  mw-100 navbar-search align-items-end']) ?>
    
          <div class="input-group">
              <?= Html::textInput('search', $search,[

                  'class' => 'form-control bg-light small rounded-full',
                  'placeholder' => 'Search for...',
                  'aria-label' => 'Search',
                  'aria-describedby' => 'basic-addon2'
              ])?>
            
              <div class="input-group-append">
                  <?= Html::submitButton('<i class="fas fa-search fa-sm"></i>',['class' => 'btn btn-primary', 'type'=> 'button'])?>
                  
              </div>
          </div>
      <?= Html::endForm() ?>

      <p>
          <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
      </p>
    
  </div>
    <div class="row">
        <table class="table table-hover">
            <tr class="align-middle text-center">
                <th>#</th>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
            <tbody>
                <?php if(empty($users))
                  {
                    echo '<tr><td colspan="6" class="text-center">No bookings found.</td></tr>';
                  }
                ?>
                <?php foreach ($users as $key =>$model): ?>
                    <tr class="align-middle  text-center">
                        <td><?= Html::encode($key+1) ?></td>
                        <td><?= Html::encode($model->id) ?></td>
                        <td><?= Html::encode($model->name) ?></td>
                        <td><?= Html::encode($model->username) ?></td>
                        <td><?= Html::encode($model->role->name) ?></td>
                        <td>
                          <a href="<?=  Url::to(['user/view', 'id' => $model->id])?>" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                          <a href="<?=  Url::to(['user/update', 'id' => $model->id])?>" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                            <?= Html::a('<i class="fa fa-trash"></i>', ['user/delete', 'id' => $model->id], [
                              'class' => 'btn btn-danger',
                              'data' => [
                                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                              ],
                            ]) ?>
                        </td>
                        
                    </tr>

                    
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
          <?php 
              echo LinkPager::widget([
                'pagination' => $pagination,
                'options' => ['class' => 'page-item d-flex justify-content-center'],
                'linkOptions' => ['class' => 'page-link'],
                'disabledPageCssClass' => 'disabled',
                'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
                'maxButtonCount' => 5,
                'prevPageCssClass' => 'page-item',
                'nextPageCssClass' => 'page-item',
                'prevPageLabel' => 'Previous',
                'nextPageLabel' => 'Next',
                'pageCssClass' => 'page-item',
                'activePageCssClass' => 'active',


              ]);
          ?>

        </div>

    </div>




</div>
