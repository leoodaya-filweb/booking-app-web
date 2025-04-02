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
   

    <div class=" mb-2 d-flex justify-content-end align-items-end">
        <div class="row">
            <div class="col-md-5">
                <label for="start_date">Start Date</label>
                <input type="datetime-local" id="start_date" class="form-control" value="<?= Yii::$app->request->get('start_date') ?>">
            </div>
            <div class="col-md-5">
                <label for="end_date">End Date</label>
                <input type="datetime-local" id="end_date" class="form-control" value="<?= Yii::$app->request->get('end_date') ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button id="filterBtn" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </div>

    <div class="row mx-3">
        <table class="table table-hover">
            <thead>
                <tr class="align-middle text-center">
                    <th>#</th>
                    <th>Station Name</th>
                    <th>DateTime</th>
                    <th>Temperature (°C)</th>
                    <th>Humidity (%)</th>
                    <th>Weather</th> 
                </tr>
            </thead>
            <tbody>
                <?php if(empty($weatherData)): ?>
                    <tr><td colspan="6" class="text-center">No data found.</td></tr>
                <?php else: ?>
                    <?php foreach ($weatherData as $key => $item): ?>
                        <tr class="align-middle text-center">
                            <td><?= $pagination->offset + $key + 1 ?></td>
                            <td><?= Html::encode($item->station_name) ?></td>
                            <td><?= Html::encode(convertToLocalTime($item->datatime)) ?></td>
                            <td><?= Html::encode($item->temperature) ?></td>
                            <td><?= Html::encode($item->humidity) ?></td>
                            <td><?= Html::encode(ucfirst($item->weather)) ?></td> <!-- Display weather data -->
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
                    'firstPageLabel' => 'First', 
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

        // Format datetime labels to "Mar 23, 2025 - 12:52 AM"
        var formattedLabels = chartData.labels.map(function(datetime) {
            var date = new Date(datetime);
            return date.toLocaleString('en-US', {
                month: 'short',
                day: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        });

        // Create a gradient fill for Temperature
        var tempGradient = ctx.createLinearGradient(0, 0, 0, 400);
        tempGradient.addColorStop(0, "rgba(255, 99, 132, 0.5)");
        tempGradient.addColorStop(1, "rgba(255, 99, 132, 0.1)");

        // Create a gradient fill for Humidity
        var humiGradient = ctx.createLinearGradient(0, 0, 0, 400);
        humiGradient.addColorStop(0, "rgba(54, 162, 235, 0.5)");
        humiGradient.addColorStop(1, "rgba(54, 162, 235, 0.1)");

        var tempHumiChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedLabels,
                datasets: [
                    {
                        label: 'Temperature (°C)',
                        data: chartData.temperature,
                        borderColor: '#FF4D4D',
                        backgroundColor: tempGradient,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#FF4D4D',
                        pointBorderColor: 'white',
                        borderWidth: 3,
                        yAxisID: 'temperatureAxis' // Assign to left Y-axis
                    },
                    {
                        label: 'Humidity (%)',
                        data: chartData.humidity,
                        borderColor: '#36A2EB',
                        backgroundColor: humiGradient,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#36A2EB',
                        pointBorderColor: 'white',
                        borderWidth: 3,
                        yAxisID: 'humidityAxis' // Assign to right Y-axis
                    }
                ]
            },
            
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false // Hide X-axis grid lines
                        },
                        ticks: {
                            maxTicksLimit: 20, // Control how many labels show
                            fontColor: "#6c757d",
                            fontSize: 12
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Date & Time',
                            fontSize: 14,
                            fontColor: "#6c757d",
                            padding: 12,
                            fontStyle: 'bold'
                        }
                    }],
                    yAxes: [
                        {
                            id: 'temperatureAxis',
                            position: 'left',
                            scaleLabel: {
                                display: true,
                                labelString: 'Temperature (°C)',
                                fontSize: 14,
                                fontColor: '#FF4D4D',
                                fontStyle: 'bold' 
                            },
                            ticks: {
                                fontColor: "#FF4D4D",
                                fontSize: 12
                            },
                            gridLines: {
                                display: true,
                                color: "rgba(200, 200, 200, 0.3)",
                                drawBorder: false
                            }
                        },
                        {
                            id: 'humidityAxis',
                            position: 'right',
                            scaleLabel: {
                                display: true,
                                labelString: 'Humidity (%)',
                                fontSize: 14,
                                fontColor: '#36A2EB',
                                fontStyle: 'bold' 
                            },
                            ticks: {
                                fontColor: "#36A2EB",
                                fontSize: 12
                            },
                            gridLines: {
                                display: false // No extra grid on right side
                            }
                        }
                    ]
                },
                legend: {
                    position: 'top',
                    labels: {
                        fontSize: 14,
                        fontColor: "#333",
                        padding: 20
                    }
                },
                title: {
                    display: true,
                    text: 'Temperature & Humidity Graph',
                    fontSize: 16,
                    fontColor: "#333",
                    padding: 10
                },
                tooltips: {
                    backgroundColor: "rgba(0,0,0,0.8)",
                    titleFontSize: 14,
                    titleFontColor: "#fff",
                    bodyFontSize: 13,
                    bodyFontColor: "#ddd",
                    xPadding: 12,
                    yPadding: 12,
                    displayColors: false,
                    mode: 'index',
                    intersect: false
                }
            }
        });

        window.addEventListener('resize', function () {
            tempHumiChart.resize();
        });
    });

    document.getElementById('filterBtn').addEventListener('click', function () {
        let startDate = document.getElementById('start_date').value;
        let endDate = document.getElementById('end_date').value;

        if (startDate && endDate) {
            let url = new URL(window.location.href);
            url.searchParams.set('start_date', startDate);
            url.searchParams.set('end_date', endDate);
            window.location.href = url.toString(); // Redirect with new filters
        } else {
            alert("Please select both start and end dates.");
        }
    });

</script>

