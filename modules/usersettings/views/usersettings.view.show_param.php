<?php
/**
 * Шаблон дополнительных в настройках аккаунта
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

if (! defined('DIAFAN'))
{
    $path = __FILE__;
	while(! file_exists($path.'/includes/404.php'))
	{
		$parent = dirname($path);
		if($parent == $path) exit;
		$path = $parent;
	}
	include $path.'/includes/404.php';
}



$name = $result["name"];
$prefix = $result["prefix"];

if(! empty($result[$name]))
{
	foreach ($result[$name] as $row)
	{
		echo '<div class="field-d param'.$prefix.$row["id"];
		if(! empty($result["param_role_rels"][$row["id"]]))
		{
		    $rels = implode(' param_role_', $result["param_role_rels"][$row["id"]]);
		    echo ' js_param_role_rels js_param_role_'.$rels.' param_role_rels param_role_'.$rels;
		}
		echo '">';

		switch ($row["type"])
		{
			case 'title':
				echo '<div class="field-d__title">'.$row["name"].':</div>';
				break;

			case 'text':
				echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
				<input type="text" name="'.$prefix.'p'.$row["id"].'" value="'.str_replace('"', '&quot;', $row['value']).'">';
				break;

			case "email":
				echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
				<input type="email" name="'.$prefix.'p'.$row["id"].'" value="'.str_replace('"', '&quot;', $row['value']).'">';
				break;

			case "phone":
				echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
				<input type="tel" name="'.$prefix.'p'.$row["id"].'" value="'.str_replace('"', '&quot;', $row['value']).'">';
				break;

			case 'textarea':
				echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
				<textarea name="'.$prefix.'p'.$row["id"].'" rows="10" cols="30">'.str_replace(array ('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $row['value']).'</textarea>';
				break;

			case 'date':
			case 'datetime':
				$timecalendar  = true;
				echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<input type="text" name="'.$prefix.'p'.$row["id"].'" value="'.$row['value'].'" class="timecalendar" showTime="'
					.($row["type"] == 'datetime'? 'true' : 'false').'">';
				break;

			case 'numtext':
				echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
				<input type="number" name="'.$prefix.'p'.$row["id"].'" value="'.$row['value'].'">';
				break;

			case 'checkbox':
				echo '<input name="'.$prefix.'p'.$row["id"].'" id="usersettings_p'.$prefix.'p'.$row["id"].'" value="1" type="checkbox" value="1"><label for="usersettings_p'.$prefix.'p'.$row["id"].'">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').'</label>';
				break;

			case 'select':
				echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
				<select name="'.$prefix.'p'.$row["id"].'" class="inpselect">
					<option value="">-</option>';
				foreach ($row["select_array"] as $select)
				{
					echo '<option value="'.$select["id"].'"'.($row['value'] == $select["id"] ? ' selected' : '').'>'.$select["name"].'</option>';
				}
				echo '</select>';
				break;

			case 'multiple':
				echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
				<div class="field-d__list">';
					foreach ($row["select_array"] as $select)
					{
						echo
						'<div class="field-d__item">
							<input name="'.$prefix.'p'.$row["id"].'[]" id="usersettings_p_'.$prefix.'p'.$row["id"].'_'.$select["id"].'[]" value="'.$select["id"].'" type="checkbox" class="inpcheckbox"'.($row['value'] && in_array($select["id"], $row['value']) ? ' checked' : '').'>
							<label for="usersettings_p_'.$prefix.'p'.$row["id"].'_'.$select["id"].'[]">'.$select["name"].'</label>
						</div>';
					}
				echo '</div>';
				break;

			case "attachments":
				echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>';
				if(! empty($result[$prefix.'attachments'][$row["id"]]))
				{
					echo $this->get('attachments', 'usersettings', array("rows" => $result[$prefix.'attachments'][$row["id"]], "prefix" => $prefix, "param_id" => $row["id"], "use_animation" => $row["use_animation"]));
				}
				if(empty($result[$prefix.'attachments'][$row["id"]]) || count($result[$prefix.'attachments'][$row["id"]]) < $row["max_count_attachments"])
				{
					echo
					'<label class="inpattachment">
						<input type="file" name="'.$prefix.'attachments'.$row["id"].'[]" class="inpfiles" max="'.$row["max_count_attachments"].'">
						<label>'.$this->diafan->_('Прикрепить файл').'</label>
					</label>';
				}
				echo
				'<label class="inpattachment" style="display:none">
					<input type="file" name="hide_'.$prefix.'attachments'.$row["id"].'[]" class="inpfiles" max="'.$row["max_count_attachments"].'">
					<label>'.$this->diafan->_('Прикрепить файл').'</label>
				</label>';
				if ($row["attachment_extensions"])
				{
					echo '<div class="field-d__text attachment_extensions">('.$this->diafan->_('Доступные типы файлов').': '.$row["attachment_extensions"].')</div>';
				}
				break;

			case "images":
				echo
				'<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
				<div class="inpimage">
					<div class="images">';
					if(! empty($result[$prefix.'images'][$row["id"]]))
					{
						echo $this->get('images', 'usersettings', $result[$prefix.'images'][$row["id"]]);
					}
					echo '</div>
					<input type="file" name="'.$prefix.'images'.$row["id"].'" prefix="'.$prefix.'" param_id="'.$row["id"].'" class="inpimages">
					<label>'.$this->diafan->_('Прикрепить изображение').'</label>
				</div>';
				break;
		}
		if(! empty($row["text"]))
		{
			echo '<div class="field-d__text usersettings_form_param_text">'.$row["text"].'</div>';
		}
		echo '<div class="errors error_'.$prefix.'p'.$row["id"].'"'.($result["error_".$prefix."p".$row["id"]] ? '>'.$result["error_".$prefix."p".$row["id"]] : ' style="display:none">').'</div>
		</div>';
	}
}