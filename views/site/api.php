<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\helpers\Url;

function convertToLocalTime($utcDatetime, $timezone = 'Asia/Manila') {
    $date = new DateTime($utcDatetime, new DateTimeZone('UTC')); // Assume the API provides UTC time
    $date->setTimezone(new DateTimeZone($timezone)); // Convert to local time
    return $date->format('F j, Y - g:i A'); // Format the output
}

$this->title = 'POTEKA Weather';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-API">
    <h1><?= Html::encode($this->title) ?></h1>
   

    <br>
    <div class="row mx-3">
        <table class="table table-hover">
            <thead>
                <tr class="align-middle text-center">
                    <th>#</th>
                    <th>Station Name</th>
                    <th>DateTime</th>
                    <th>Temperature (°C)</th>
                    <th>Humidity (%)</th>
                    <th>Weather</th> <!-- New column for weather -->
                </tr>
            </thead>
            <tbody>
                <?php if(empty($items)): ?>
                    <tr><td colspan="5" class="text-center">No data found.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $key => $item): ?>
                        <tr class="align-middle text-center">
                            <td><?= $pagination->offset + $key + 1 ?></td>
                            <td><?= Html::encode($item['stationName']) ?></td>
                            <td><?= Html::encode(convertToLocalTime($item['datetime'])) ?></td>
                            <td><?= Html::encode($item['temperature']) ?></td>
                            <td><?= Html::encode($item['humidity']) ?></td>
                            <td><?= Html::encode(ucfirst($item['weather'])) ?></td> <!-- Display weather data -->
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination Controls -->
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
                    'firstPageLabel' => 'First',  // First page button
                    'lastPageLabel' => 'Last', 


                ]);
            ?>

        </div>
    </div>

    <div class="col-xl-12 col-lg-12 mt-4">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Temperature & Humidity 24hrs Graph</h6>
                
            </div>
            <!-- Card Body -->
            <div class="card-body ">
                <div class="chart-area"  style="width: 100%; height: 500px;">
                    <canvas id="tempHumiChart"></canvas>

                </div>
            </div>
        </div>
    </div>


   
</div>
<script src="vendor/chart.js/Chart.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var ctx = document.getElementById('tempHumiChart').getContext('2d');

        var chartData = <?= json_encode($chartData) ?>;

         // Format datetime labels to "March 23, 2025 - 12:52 AM"
         var formattedLabels = chartData.labels.map(function(datetime) {
            var date = new Date(datetime);
            return date.toLocaleString('en-US', {
                month: 'long', 
                day: '2-digit', 
                year: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit', 
                hour12: true
            });
        });

        var tempHumiChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedLabels, 
                datasets: [
                    {
                        label: 'Temperature (°C)',
                        data: chartData.temperature,
                        borderColor: 'red',
                        backgroundColor: 'rgba(255, 0, 0, 0.2)',
                        fill: true,
                        tension: 10 
                    },
                    {
                        label: 'Humidity (%)',
                        data: chartData.humidity,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 0, 255, 0.2)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                    scales: {
                        x: {
                            title: { display: true, text: 'Date & Time' },
                            labels: {display: false}
                        },
                        y: {
                            title: { display: true, text: 'Value' }
                        }
                    }
            }
        });

        window.addEventListener('resize', function (){
            tempHumiChart.resize();
        });
    });
</script>
