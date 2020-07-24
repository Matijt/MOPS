<?= \edofre\fullcalendarscheduler\FullcalendarScheduler::widget(array(
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
    ],
    'events'=> $eventsfull,
    'resources' => $compartments,
));
?>

