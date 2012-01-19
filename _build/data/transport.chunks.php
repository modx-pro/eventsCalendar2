<?php
/**
 * eventsCalendar2
 *
 * Copyright 2010 by Shaun McCormick <shaun+eventscalendar2@modx.com>
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
 * Add chunks to build
 * 
 * @package eventscalendar2
 * @subpackage build
 */
$snippets = array();

$chunks[0]= $modx->newObject('modChunk');
$chunks[0]->fromArray(array(
    'id' => 0,
    'name' => 'tplEvent2',
    'description' => 'Template for Events.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/chunks/tplEvent2.tpl'),
),'',true,true);

$chunks[1]= $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 0,
    'name' => 'tplCalendar2',
    'description' => 'Template for Calendar.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/chunks/tplCalendar2.tpl'),
),'',true,true);
//$properties = include $sources['build'].'properties/properties.eventscalendar2.php';
//$chunks[0]->setProperties($properties);
//unset($properties);

return $chunks;
