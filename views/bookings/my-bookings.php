<?php

use app\models\Bookings;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\BookingsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'My Bookings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bookings-index">

<h1><?= Html::encode($this->title) ?></h1>

<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<div class="row">
    <div class="d-flex align-items-between justify-content-between mb-2 ">
        
    <?= Html::beginForm(['bookings/my-bookings'], 'POST', ['class' => '  mw-100 navbar-search align-items-end']) ?>
      
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

      
      
    </div>

    <div class="row">
        <table class="table table-hover">
            <tr class="align-middle text-center">
                <th>#</th>
                <th>Room</th>
                <th>Checkin</th>
                <th>Checkout</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
                
            </tr>
            <tbody>
              <?php if(empty($bookings))
                {
                  echo '<tr><td colspan="7" class="text-center">No bookings found.</td></tr>';
                }
              ?>
              <?php foreach ($bookings as $key => $model): ?>
                  <tr class="align-middle text-center">
                  <td><?= Html::encode($key+1) ?></td>
                      <td><?= Html::encode($model->room->name) ?></td>
                      
                      <td><?= Html::encode(Yii::$app->formatter->asDatetime($model->checkin, 'php:M-d-Y h:i:s A')) ?></td>
                      <td><?= Html::encode(Yii::$app->formatter->asDatetime($model->checkout, 'php:M-d-Y h:i:s A')) ?></td>
                      <td>₱ <?= Html::encode(number_format($model->price,2)) ?></td>
                      <td>                    
                        <p class="card-text ">
                          <span class="<?php 
                            if ($model->status == "completed" ) {
                              echo 'text-success';
                            } elseif ($model->status == "Booked") {
                              echo 'text-info';
                            }
                            elseif ($model->status == "occupied") {
                              echo 'text-warning';
                            }
                             elseif ($model->status == "cancelled") {
                              echo 'text-danger';
                            } else {
                              echo 'text-secondary';
                            }
                          ?>">
                            <?= Html::encode(ucfirst($model->status)) ?>
                          </span>
                        </p></p>
                      </td>
                      <td>
                        <a href="<?= Url::to(['bookings/view', 'id' => $model->id]) ?>" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                        <?php 
                          if(!$model->status == "cancelled"){
                            echo '<a href="'.Url::to(['bookings/cancel', 'id' => $model->id]).'" class="btn btn-danger">Cancel</a>';
                          } 
                        ?>
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


</div>
