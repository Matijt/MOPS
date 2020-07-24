
                <?= \yii2fullcalendar\yii2fullcalendar::widget(array(
                    'header' => [
                        'left' => 'prev,next,today',
                        'center' => 'title',
                        'right' => 'listYear,month,agendaWeek,agendaDay',
                    ],
                    'defaultView' => "listYear",
                    'events'=> $events,
                ));
                ?>

