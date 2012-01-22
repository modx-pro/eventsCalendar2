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

$c['includeContent'] = !empty($includeContent) ? true : false; // Включить ТВ параметры?
$c['includeTVs'] = !empty($includeTVs) ? true : false; // Включить ТВ параметры?
$c['includeTVList'] = !empty($includeTVList) ? explode(',', $includeTVList) : array(); // Список ТВ для выборки
$c['processTVs'] = !empty($processTVs) ? true : false; // Отрендерить ТВ?
$c['processTVList'] = !empty($processTVList) ? explode(',', $processTVList) : array(); // Отрендерить ТВ?

$c['plPrefix'] = isset($plPrefix) ? $plPrefix : 'ec.'; // Префикс для плейсхолдеров
$c['regCss'] = isset($regCss) ? $regCss : true; // Включить собственные стили?
$c['regJs'] = isset($regJs) ? $regJs : true; // Включить собственные js скрипты?

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
	$html = $EC2->generateCalendar();
	// Парсим плейсхолдеры
	$maxIterations= (integer) $modx->getOption('parser_max_iterations', null, 10);
	$modx->getParser()->processElementTags('', $html, false, false, '[[', ']]', array(), $maxIterations);
	$modx->getParser()->processElementTags('', $html, true, true, '[[', ']]', array(), $maxIterations);
	die($html);
}
else {
	echo $EC2->output();
}

?>