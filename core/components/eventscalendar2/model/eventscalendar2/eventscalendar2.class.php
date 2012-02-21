<?php
/**
 * eventsCalendar2
 *
 * Copyright 2012 by Vasliy Naumkin <bezumkin@yandex.ru>
 *
 * eventsCalendar2 is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * eventsCalendar2 is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * eventsCalendar2; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package eventscalendar2
 */
/**
 * The base class for eventsCalendar2.
 *
 * @package eventscalendar2
 */
class eventsCalendar2 {

    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('eventscalendar2.core_path',$config,$this->modx->getOption('core_path').'components/eventscalendar2/');
        $assetsUrl = $this->modx->getOption('eventscalendar2.assets_url',$config,$this->modx->getOption('assets_url').'components/eventscalendar2/');
        $connectorUrl = $assetsUrl.'connector.php';

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl.'css/',
            'jsUrl' => $assetsUrl.'js/',
            'imagesUrl' => $assetsUrl.'images/',

            'connectorUrl' => $connectorUrl,

            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'chunksPath' => $corePath.'elements/chunks/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath.'elements/snippets/',
            'processorsPath' => $corePath.'processors/',
        ),$config);

        //$this->modx->addPackage('eventscalendar2',$this->config['modelPath']);
        $this->modx->lexicon->load('eventscalendar2:default');
    }

    /**
     * Initializes eventsCalendar2 into different contexts.
     *
     * @access public
     * @param string $ctx The context to load. Defaults to web.
     */
    public function initialize($ctx = 'web') {
        switch ($ctx) {
            case 'mgr':
                if (!$this->modx->loadClass('eventscalendar2.request.eventsCalendar2ControllerRequest',$this->config['modelPath'],true,true)) {
                    return 'Could not load controller request handler.';
                }
                $this->request = new eventsCalendar2ControllerRequest($this);
                return $this->request->handleRequest();
            break;
            case 'connector':
                if (!$this->modx->loadClass('eventscalendar2.request.eventsCalendar2ConnectorRequest',$this->config['modelPath'],true,true)) {
                    return 'Could not load connector request handler.';
                }
                $this->request = new eventsCalendar2ConnectorRequest($this);
                return $this->request->handle();
            break;
            default:
                /* if you wanted to do any generic frontend stuff here.
                 * For example, if you have a lot of snippets but common code
                 * in them all at the beginning, you could put it here and just
                 * call $eventscalendar2->initialize($modx->context->get('key'));
                 * which would run this.
                 */
            break;
        }
    }
	
	
	/* Замеряет время выполнения функции
	 * Вызывать в начале и конце скрипта
	 *
	 * */
	function get_execution_time() {
		static $microtime_start = null;
		if($microtime_start === null)
		{
			$microtime_start = microtime(true);
			return 0.0;
		}
		return microtime(true) - $microtime_start;
	}
	

	/* Принимает массив элементов с ключами и выдает 2 массива - плейсхолдеры и значения
	 * Можно использовать префиксы, вида 'lk'
	 *
	 * */
	function makePlaceholders($arr, $prefix = '') {
		if (!is_array($arr)) {return false;}
		//if (!empty($prefix)) {$prefix .= '.';}

		$res = array();
		foreach ($arr as $k => $v) {
			$res['pl'][$prefix.$k] = '[[+'.$prefix.$k.']]';
			$res['vl'][$prefix.$k] = $v;
		}
		return $res;
	}


	/* Вывод ошибок
	 * */
    function error($err) {
		$error = 'eventsCalendar2 error: ' . $this->modx->lexicon($err);
		$this->modx->log(modX::LOG_LEVEL_ERROR, $error);
		if ($this->config['show_errors']) {
			echo $error;
		}
    }

	/* Выводит календарь на заданный месяц и год
	 * */
    function generateCalendar() {
        //Это сильно модефицированный календарь, оригинал - вот тут: http://www.softtime.ru/scripts/calendar.php          
		$month = $this->config['month'];
		$year = $this->config['year'];
        $time = mktime(0,0,0, $month, 1 ,$year);
        $dayofmonth = date('t', $time);     
        $day_count = 1;
            // 1. Первая неделя
            $num = 0;
        
        for ($i = 0; $i < 7; $i++) {
            // Вычисляем номер дня недели для числа
            $dayofweek = date('w', mktime(0, 0, 0, $month, $day_count, $year));
        
            // Приводим к числа к формату 1 - понедельник, ..., 6 - суббота
            if ($this->config['first_day'] == 1) {
            $dayofweek = $dayofweek - 1;
                if($dayofweek == -1) {$dayofweek = 6;}
            }
            
            if($dayofweek == $i) {
                // Если дни недели совпадают, заполняем массив $week числами месяца
                $week[$num][$i] = $day_count;
                $day_count++;
            }
            else {
                $week[$num][$i] = '';
            }
        }
        // 2. Последующие недели месяца
        while(true) {
            $num++;
        
            for($i = 0; $i < 7; $i++) {
              $week[$num][$i] = $day_count;
              $day_count++;
              // Если достигли конца месяца - выходим из цикла
              if($day_count > $dayofmonth) break;
            }
            // Если достигли конца месяца - выходим из цикла
            if($day_count > $dayofmonth) break;
        }
        // 3. Выводим содержимое массива $week в виде календаря
		$next_month = $month + 1; 
		if ($next_month == 13) {$next_month = 1; $next_year = $year + 1;}
		else {$next_year = $year;}
		
		$prev_month = $month - 1;
		if ($prev_month == 0) {$prev_month = 12; $prev_year = $year - 1;}
		else {$prev_year = $year;}
        
		// Если указана json строка с массивом дат - пытаемся исмользовать ее
		if (!empty($this->config['events'])) {
			$events = @json_decode($this->config['events'], true);
			if (!is_array($events)) {
				$this->error('err_decode_events');
			}
			// Сортируем по дате полученные из json события
			else {
				function compare_date($a, $b) {
        				if ($a['date'] == $b['date']) {return 0;}
        				return (strtotime($a['date']) < strtotime($b['date'])) ? -1 : 1;
        			}
        			uasort($events,"compare_date");
			}
		}
		// Если нет - штатно получаем события
		else {
			$events = $this->getEvents($month, $year);
		}
		
		// Украшаем полученные события
		$events = $this->templateEvents($events);
		
		// Рисуем календарь
		$month_name = $this->modx->lexicon('month'.$month);
        $self = $this->modx->makeUrl($this->modx->resource->id);
		
        $table = '<table class="'.$this->config['class_calendar'].'">';
		
		$tpl = $this->modx->getChunk($this->config['tplHead']);
		$thead = array();
		$thead['class_prev'] = $this->config['class_prev'];
		$thead['class_month'] = $this->config['class_month'];
		$thead['class_next'] = $this->config['class_next'];
		$thead['link_prev'] = $self.'?action=refreshCalendar&month='.$prev_month.'&year='.$prev_year;
		$thead['link_next'] = $self.'?action=refreshCalendar&month='.$next_month.'&year='.$next_year;
		$thead['btn_prev'] = $this->config['btn_prev'];
		$thead['btn_next'] = $this->config['btn_next'];
		$thead['month_name'] = $month_name;
		$thead['year'] = $year;
		$pl = $this->makePlaceholders($thead, $this->config['plPrefix']);
		$table .= str_replace($pl['pl'], $pl['vl'], $tpl);
		
        $table .= '<tr>';
		// Обработка первого дня недели
		if ($this->config['first_day'] == '0') {
			 $table .= '<th class="'.$this->config['class_dow'].'">'.$this->modx->lexicon('day7').'</th>';
			 $wend = 6;
		} else {$wend = 7;}
		
		for ($i = 1; $i <= $wend; $i++) {
              $table .= '<th class="'.$this->config['class_dow'].'">'.$this->modx->lexicon('day'.$i).'</th>';
        }
		
		$today = !empty($_SESSION['calendar_date']) ? $_SESSION['calendar_date'] : strftime('%Y-%m-%d', time() + $this->config['time_shift']*60*60);
		
		$tpl =  $this->modx->getChunk($this->config['tplCell']);
		foreach($week as $v) {
			$table .= "</tr><tr>";
			for($i = 0; $i < 7; $i++) {
				if(!empty($v[$i])) {
				   
					if (strlen($v[$i]) == 1) {$day = '0'.$v[$i];} else {$day = $v[$i];}
					$date = $year.'-'.sprintf('%02u',$month).'-'.$day;
					if (($wend == 7 && ($i == 5 || $i == 6)) || ($wend == 6 && ($i == 0 || $i == 6))) {
					   $class = $this->config['class_weekend'];
					}
					else {
					   $class = $this->config['class_workday'];
					}

					if ($date == $today) {$class .= ' '.$this->config['class_today'];}
					if (!empty($events[$date])) {$class .= ' '.$this->config['class_isevent'];}
					else {$class .= ' '.$this->config['class_noevent'];}

					$trow = array();
					$trow['class'] = $class;
					$trow['cell_id'] = $this->config['calendar_id'].'_'.$v[$i];
					$trow['day'] = $v[$i];
					$trow['fulldate'] = "$year-$month-$v[$i]";
					$trow['class_date'] = $this->config['class_date'];
					$trow['class_event'] = $this->config['class_event'];
					$trow['events'] = $events[$date];
					$pl = $this->makePlaceholders($trow, $this->config['plPrefix']);
					$table .= str_replace($pl['pl'], $pl['vl'], $tpl);
			   }
			   else $table .= '<td class="'.$this->config['class_emptyday'].'">&nbsp;</td>';
			}
		} 
		$table .= '</table>';
		return $table;
	}
    
	/* Получение событий из БД, принимает месяц и год
	 * */
    function getEvents($month = '', $year = '') {
        $id = $this->config['id'];
        if (empty($month)) {$month = date('m');}
        if (empty($year)) {$year = date('Y');}

		// Узнаем ID потомков указанного контейнера. Если указаны родители - выбираем по ним
		$tmp = array();
		if ($this->config['parents']) {
			$parents = explode(',', $this->config['parents']);
			foreach ($parents as $v) {
				$v = trim($v);
				if (!empty($v)) {$tmp = array_merge($tmp, $this->modx->getChildIds($v));}
			}
		}
		// Если родители не указаны - выбираем по ID
		else {
			$tmp = $this->modx->getChildIds($id);
		}
		
		$query = $this->modx->newQuery('modResource');
		if (count($tmp) > 0) {
			$query->where(array(
				'published' => true,
				'deleted' => false,
				'id:IN' => $tmp
			));
			// Не показывать скрытые в меню
			if (empty($this->config['showHidden'])) {
				$query->andCondition(array('hidemenu' => 0));
			}
			// Не показывать контейнеры
			if (!empty($this->config['hideContainers'])) {
				$query->andCondition(array('isfolder' => 0));
			}
		}
		else {$this->error('no_result');} // У документа нет потомков - это ошибка
		
		// Если источником события задан TV параметр - работаем по нему
		if (preg_match('/^tv/i', $this->config['dateSource'])) {
			$this->isTV = 1;
			$this->config['dateSource'] = preg_replace('/^tv/i', '', $this->config['dateSource']);
		}
		else {
			// Улучшенная проверка на TV параметр
			$tmp = $this->modx->newObject('modResource')->toArray();
			if (!array_key_exists($this->config['dateSource'], $tmp)) {
				$this->isTV = 1;
			}
			// Если не TV - значит поле контента
			else {
				$query->sortby($this->config['dateSource']);
			}
		}


		// Извлекаем список ТВ, если он указан. Если не указан - извлечем позже
		$templateVars = array();
		if (!empty($this->config['includeTVs']) && !empty($this->config['includeTVList'])) {
			$templateVars = $this->modx->getCollection('modTemplateVar', array('name:IN' => $this->config['includeTVList']));
		}

		
		// Достаем данные из базы
		// Извлекаем или нет поле content?
		$fields = array_keys($this->modx->getFields('modResource'));
		if (empty($this->config['includeContent'])) {
			$fields = array_diff($fields, array('content'));
		}
		$columns = $this->config['includeContent'] ? $this->modx->getSelectColumns('modResource', 'modResource') : $this->modx->getSelectColumns('modResource', 'modResource', '', array('content'), true);
		$query->select($columns);

		$resources = $this->modx->getCollection('modResource', $query, false);
		foreach ($resources as $resource) {
			if ($this->isTV) {
				$tv = $this->modx->getObject('modTemplateVar', array('name' => $this->config['dateSource']));
				if (!empty($tv)) {
					$date = $tv->getValue($resource->get('id'));
				}
				else {
					$this->error('no_tv');
					break;
				}
			}
			else {
				$date = $resource->get($this->config['dateSource']);
			}

			// Если ресурс подходит по дате - достаем (или нет) для него ТВ и сохраняем.
			if (date('Y-n', strtotime($date)) == "$year-$month") {
				$resource->set('date', $date);
				
				// Обработка ТВ, если нужно
				$tvs = array();
				if (!empty($this->config['includeTVs'])) {
					// Если список нужных ТВ не был указан - извлекаем ВСЕ ТВ для ресурса
					if (empty($this->config['includeTVList'])) {
						$templateVars = $resource->getMany('TemplateVars');
					}
					// Обрабатываем ТВ
					foreach ($templateVars as $tvId => $templateVar) {
						if (!empty($this->config['includeTVList']) && !in_array($templateVar->get('name'), $this->config['includeTVList'])) {continue;}
						// Рендерить ли ТВ?
						// Да
						if ($this->config['processTVs'] && (empty($this->config['processTVList']) || in_array($templateVar->get('name'), $this->config['processTVList']))) {
							$tvs[$templateVar->get('name')] = $templateVar->renderOutput($resource->get('id'));
						// Нет
						} else {
							$tvs[$templateVar->get('name')] = $templateVar->getValue($resource->get('id'));
						}
					}
				}
				
				$content[] = array_merge($resource->toArray(), $tvs);
			}
		}
        return $content;
    }
	
	/* Оборачивание событий в чанк
	 * */
	function templateEvents($content) {

        $i = 1;
        foreach ($content as $v) {
            $pl = array();
            //  определяем переменные документа для подстановки в шаблон

            //  Это для номера события, если событие за день не одно
            $date = strftime('%Y-%m-%d', strtotime($v['date']));
            if (isset($date2) && $date2 != $date) {$i = 1;}
            $date2 = $date;

			//	Обязательные плейсхолдеры: урл, номер события и дата
			if (!empty($v['id'])) {
				$pl[$this->config['plPrefix'].'url'] = $this->modx->makeUrl($v['id']);;
			}
			$pl[$this->config['plPrefix'].'num'] = $i;
            $pl[$this->config['plPrefix'].'date'] = strftime($this->config['dateFormat'], strtotime($v['date']));
				unset($v['date']);

            foreach ($v as $k2 => $v2) {
                $pl[$this->config['plPrefix'].$k2] = $v2;
            }
			$dates[$date2] .= $this->modx->getChunk($this->config['tplEvent'], $pl);
			$i++;
        }

		//$dates = preg_replace('/\[\[\+.*?\]\]/', '', $dates);	// Вылезаем пустые плейсхолдеры
		return $dates;
	}

	
    /*  Обычная загрузка календаря при рендере страницы
	 * */
    function output() {
		if (!empty($this->config['regCss'])) {
			if (!empty($this->config['theme'])) {
				$this->modx->regClientCSS('<link type="text/css" rel="stylesheet" href="'.$this->config['cssUrl'].$this->config['theme'].'/theme.css"/>');
			}
			else {
				$this->modx->regClientCSS('<link type="text/css" rel="stylesheet" href="'.$this->config['cssUrl'].'default.css"/>');
			}
			
		}
		if (!empty($this->config['regJs'])) {
			$this->modx->regClientStartupScript('<script type="text/javascript" src="'.$this->config['jsUrl'].'eventscalendar2.js"></script>');
		}
		
        $calendar = $this->generateCalendar();
        return $this->modx->getChunk($this->config['tplMain'], array($this->config['plPrefix'].'Calendar' => $calendar));
    }

}