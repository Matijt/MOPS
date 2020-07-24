
<?php

use yii\helpers\Html;
use backend\models\Color;
use backend\models\Compartment;
use edofre\fullcalendarscheduler\FullcalendarScheduler;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Events');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .fc-event {
        font-size: 7px;            /* EDIT HERE */
        cursor: default;
    }
</style>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="container-">
        <div class="row">
            <div class="col-lg-4">
                <h4>Select the compartments to show:</h4>

                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#comps').multiselect({
                            enableClickableOptGroups: true,
                            enableCollapsibleOptGroups: true,
                            enableFiltering: true,
                            maxHeight: 300,
                        });
                    });

                </script>
                <select id="comps" multiple="multiple" name="comps">
                    <optgroup label="Compartment">
                        <?php

                        $compartments = Compartment::find()
                            ->all();

                        foreach ($compartments AS $compartment){
                            echo '<option value="'.$compartment->compNum.'" selected="selected">'.$compartment->compNum.'</option>';
                        }
                        ?>
                    </optgroup>
                </select>
            </div>
            <div class="col-lg-4">
                <h4>Select the activities to show:</h4>

                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#acts').multiselect({
                            enableClickableOptGroups: true,
                            enableCollapsibleOptGroups: true,
                            enableFiltering: true,
                            maxHeight: 300,
                        });
                    });
                </script>
                <select id="acts" multiple="multiple" name="acts">
                    <optgroup label="Activities">
                        <?php

                        $events = Color::find()
                            ->all();

                        foreach ($events AS $event){
                            echo '<option value="'.$event->idcolor.'" selected="selected">'.$event->proceso.'</option>';
                        }
                        ?>
                    </optgroup>
                </select>
            </div>
            <div class="col-lg-2">
            </div>
            <div class="col-lg-2">

                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#filtrar').on('click', function(){
                            $.post("index.php?r=event/calendarg&comps="+($("#comps").val())+"&acts="+($("#acts").val()), function( data ){
                                $("#calendarG").html(data);
                            });
                        });
                    });
                </script>
                <button id="filtrar" class="btn btn-success">Full calendar</button>
                <script>
                </script>
            </div>
<!--            <div class="col-lg-2">

                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#filtrarc').on('click', function(){
                            $.post("index.php?r=event/calendargc&comps="+($("#comps").val())+"&acts="+($("#acts").val()), function( data ){
                                $("#calendarG").html(data);
                            });
                        });
                    });
                </script>
                <button id="filtrarc" class="btn btn-success">Current calendar</button>
                <script>
                </script>
            </div>
        </div>-->
<!--        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-1">
                <?php
    /*            $colors = Color::find()->all();

                foreach ($colors AS $color){
                    echo "
                        <div style='padding: 15% 2%; background: ".$color->color."; color: black; text-align: center; height: 90px; width: 100px;' >"
                        .$color->proceso.
                        "</div>
                    ";
                }
      */          ?>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-9" id="2">
                <?
              /*  echo yii2fullcalendar::widget(array(
                    'header' => [
                        'left' => 'prev,next,today',
                        'center' => 'title',
                        'right' => 'listYear,month,agendaWeek,agendaDay',
                    ],
                    'defaultView' => "listYear",
                    'events'=> $events,
                ));
                */?>
            </div>
        </div>
    </div>
    -->
    <div class="col-lg-12">
    </div>
    <BR>
    <BR>
    <BR>
    <div class="row-">
        <div class="col-lg-12">                <?php
            $colors = Color::find()->all();

            foreach ($colors AS $color){
                if($color->idcolor == 6){
                    echo "
                        <div style='display: inline-block; padding: 2% 1%; background: " . $color->color . "; color: black; text-align: center; height: 90px; width: 9%;' >"
                        . $color->proceso .
                        "</div>
                    ";
                }elseif($color->idcolor == 7){
                    echo "
                        <div style='display: inline-block; padding: 2% 1%; background: " . $color->color . "; color: black; text-align: center; height: 90px; width: 9%;' >"
                        . $color->proceso .
                        "</div>
                    ";
                }else {
                    echo "
                        <div style='display: inline-block; padding: 2% 1%; background: " . $color->color . "; color: black; text-align: center; height: 90px; width: 10.5%;' >"
                        . $color->proceso .
                        "</div>
                    ";
                }
            }
            ?></div>
    </div>


    <div class="row-" style="margin-top: 120px;">
        <div class="col-lg-12" id="calendarG">

            <?= FullcalendarScheduler::widget(array(
                'header' => [
                    'left' => 'prev,next,today',
                    'center' => 'title',
                    'right' => 'listYear,month,agendaWeek,agendaDay,timelineYear',
                ],
                'clientOptions' => [
                    'defaultView' => "timelineYear",
                    'schedulerLicenseKey' => '0185618251-fcs-1508340158',
                    'fixedWeekCount' => false,
                    'weekNumbers' => true,
                    'resourceAreaWidth' => '100px',
/*                 'eventMouseover' => new \yii\web\JsExpression('function(event, element) {
                  console.log("Hover "+event.title);
                  alert(event.className);
}'),
                    'eventMouseout' => new \yii\web\JsExpression('function(event) {
                  console.log("Out "+event.title);
                    $(\'#\'+event.id).remove();
}'),*/
                ],
                'events'=> $eventsfull,
                'resources' => [
                    ['id' => 111, 'title' => "111"],
                    ['id' => 112, 'title' => "112"],
                    ['id' => 113, 'title' => "113"],
                    ['id' => 114, 'title' => "114"],
                    ['id' => 115, 'title' => "115"],
                    ['id' => 116, 'title' => "116"],
                    ['id' => 121, 'title' => "121"],
                    ['id' => 122, 'title' => "122"],
                    ['id' => 123, 'title' => "123"],
                    ['id' => 124, 'title' => "124"],
                    ['id' => 125, 'title' => "125"],
                    ['id' => 126, 'title' => "126"],
                    ['id' => 131, 'title' => "131"],
                    ['id' => 132, 'title' => "132"],
                    ['id' => 133, 'title' => "133"],
                    ['id' => 134, 'title' => "134"],
                    ['id' => 135, 'title' => "135"],
                    ['id' => 136, 'title' => "136"],
                ],
            ));
            ?>
        </div>
        <div class="col-lg-2 align-text-bottom" id="pi" ></div>
    </div>
</div>
