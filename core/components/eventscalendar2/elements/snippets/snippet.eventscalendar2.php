<?php
/**
 * The base eventsCalendar2 snippet.
 *
 * @package eventscalendar2
 */
$c['id'] = !empty($id) ? $id : $modx->resourceIdentifier;

if (!empty($_REQUEST['month'])) {$c['month'] = (int) $_REQUEST['month'];}
else if (!empty($month)) {$c['month'] = $month;} 
else {$c['month'] = date('n');}

if (!empty($_REQUEST['year'])) {$c['year'] = (int) $_REQUEST['year'];}
else if (!empty($year)) {$c['year'] = $year;} 
else {$c['year'] = date('Y');}

$c['events'] = !empty($events) ? $events : ''; // Готовая json строка с массивом страниц для вывода событий

$c['dateSource'] = !empty($dateSource) ? $dateSource : 'createdon';
$c['dateFormat'] = !empty($dateFormat) ? $dateFormat : '%d %b %Y %H:%M';

$c['tplEvent'] = !empty($tplEvent) ? $tplEvent : 'tplEvent2';
$c['tplMain'] = !empty($tplMain) ? $tplMain : 'tplCalendar2';

$c['calendar_id'] = !empty($calendar_id) ? $calendar_id : 'calendar_id';

$c['class_calendar'] = !empty($class_calendar) ? $class_calendar : 'calendar';
$c['class_dow'] = !empty($class_dow) ? $class_dow : 'dow';
$c['class_month'] = !empty($class_month) ? $class_month : 'month';
$c['class_workday'] = !empty($class_workday) ? $class_workday : 'workday';
$c['class_weekend'] = !empty($class_weekend) ? $class_weekend : 'weekend';
$c['class_today'] = !empty($class_today) ? $class_today : 'today';
$c['class_event'] = !empty($class_event) ? $class_event : 'event';
$c['class_isevent'] = !empty($class_isevent) ? $class_isevent : 'isevent';
$c['class_noevent'] = !empty($class_noevent) ? $class_noevent : 'noevent';
$c['class_date'] = !empty($class_date) ? $class_date : 'date';
$c['class_emptyday'] = !empty($class_emptyday) ? $class_emptyday : 'emptyday';
$c['class_prev'] = !empty($class_prev) ? $class_prev : 'prev';
$c['class_next'] = !empty($class_next) ? $class_next : 'next';

$c['btn_prev'] = !empty($btn_prev) ? $btn_prev : '&laquo;';
$c['btn_next'] = !empty($btn_next) ? $btn_next : '&raquo;';

$c['first_day'] = isset($first_day) ? $first_day : 1;
$c['show_errors'] = ($show_errors == '0') ? false : true;
$c['time_shift'] = !empty($time_shift) ? $time_shift : 0;
$c['parents'] = !empty($parents) ? $parents : 0;

$EC2 = $modx->getService('eventscalendar2','eventsCalendar2',$modx->getOption('eventscalendar2.core_path',null,$modx->getOption('core_path').'components/eventscalendar2/').'model/eventscalendar2/', $c);
if (!($EC2 instanceof eventsCalendar2)) return '';

//  Если идет запрос через ajax - останавливаем работу
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['action'] == 'refreshCalendar') {
	echo $EC2->generateCalendar();
	die;
}
else {
	echo $EC2->output();
}

?>