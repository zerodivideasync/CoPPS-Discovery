<?php

class HtmlHelper {
	
	/**
	 * Returns a string with an "a" element.
	 * @param $href the href attribute of the link to be created
	 * @param $name the name that will be displayed
	 * @param $classes a list of classes. Defaults to empty string
	 * @return string
	 */
	static function link( $href, $name, $classes="", $escape=true){
		if($escape){
			$name = htmlspecialchars($name);
		}
		return sprintf('<a href="%s" class="%s">%s</a>',$href,$classes,$name);
	}
	
	/**
	 * Returns a string with an "a" element with href="mailto:..."
	 * @param unknown $email the email address
	 * @param unknown $name the name to display
	 * @param string $classes the classes of the <a> element
	 * @param string $escape boolean variable. Defaults to true
	 * @return unknown
	 */
	static function email($email, $name, $classes="", $escape=true){
		if($escape){
			$name = htmlspecialchars($name);
		}
		return sprintf('<a href="mailto:%s" class="%s">%s</a>',$email,$classes,$name);
	}
        
        /**
	 * Returns a string with an "a" element.
	 * @param $href the href attribute of the link to be created
	 * @param $name the name that will be displayed
	 * @param $classes a list of classes. Defaults to empty string
	 * @return string
	 */
	static function linkTarget( $href, $name, $classes="", $target="_blank", $escape=true){
		if($escape){
			$name = htmlspecialchars($name);
		}
		return sprintf('<a href="%s" class="%s" target="%s">%s</a>',$href,$classes,$target,$name);
	}
	
	/**
	 * Create a "form link", namely a link which is capable of submitting data with methods other than GET.
	 * This function works by creating an hidden form with the given data and by submitting it onclick
	 * @param unknown $action the url of the action to submit the form to
	 * @param unknown $parameters an associative array of parameters to be submitted with 'name'=>'value'
	 * @param unknown $text the text to be displayed in the <a> element
	 * @param string $method the method to use on the form. Defaults to "POST"
	 * @param string $classes <a> element's classes. Defaults to empty string.
	 * @param string $target <a> element's target attribute. Defaults to "_self"
	 * @param string $escapeText logic variable. if true, text is escaped with htmlspecialchars. Otherwise it is not. Defaults to true.
	 * @return string a string containing html code for the form and the link
	 */
	static function formLink($action, $parameters, $text, $method="POST", $classes="", $target="_self", $escapeText=true){
		if($escapeText){
			$text = htmlspecialchars($text);
		}
		$html = sprintf('<form method="%s" action="%s">',$method,$action);
		foreach($parameters as $name => $value){
			$html = $html . sprintf('<input type="hidden" name="%s" value="%s">', $name, $value);
		}
		$html = $html . '</form>';
		//$html = $html . sprintf('<a href="#" onclick="$(this).prev().submit(); return false;" class="formlink %s" target="%s">%s</a>',$classes,$target,$text); //jQuery version
		$html = $html . sprintf('<a href="#" onclick="this.previousElementSibling.submit(); return false;" class="formlink %s" target="%s">%s</a>',$classes,$target,$text); //pure javascript
		return $html;
	}
	
	/**
	 * Returns a string with a fontawesome icon.
	 * @param unknown $icon The icon to show. Only the last part of the name. For example, for the icon fa-heart use only "heart"
	 * @param string $options options such as fa-fw fa-2x fa-3x fa-spin etc..
	 * @return string string representing the "i" element for the fontAwesome icon.
	 */
	static function faIcon($icon, $options=""){
		$icon = htmlspecialchars($icon);
		$options = htmlentities($options);
		return sprintf('<i class="fa fa-%s %s"></i>',$icon, $options);
	}
	
	/**
	 * Builds a nice Bootstrap alert.
	 * 
	 * @param string $type The type of the alert. As of default Bootstrap, it can be a string in "success,danger,warning,info".
	 * @param unknown $title The title of the alert
	 * @param unknown $message The message to display
	 * @param unknown $icon A font-awesome icon name
	 * @param string $dismissible If true, the alert will be dismissibile
	 * @param string $escape If true, $message will be escaped.
	 * @return string containing the div.alert element.
	 */
	static function alert($type="danger", $title=NULL, $message=NULL, $icon=NULL, $dismissible=true, $escape=true){
		$type=htmlentities($type);
		$alert  = sprintf('<div class="alert alert-%s %s fade show">',$type, ($dismissible ? 'alert-dismissible' : ''));
		if($dismissible){
			$alert .= '<a href="#" class="close" data-dismiss="alert">&times;</a>';
		}
		if($icon){
			$alert.= self::faIcon($icon,'fa-fw');
		}
		if($title){
			$alert .= sprintf('<strong>%s</strong>',$escape ? htmlentities($title) : $title);
		}
		if($message){
			$message = rtrim($message,'.'); //remove final dot if present. We'll add it later anyway.
			$alert .= sprintf(' %s.',$escape ? htmlentities($message) : $message);
		}
		$alert .= '</div>';
		return $alert;
	}
	
	/**
	 * Same as alert() but with parameters as an associative array.
	 * @see HtmlHelper::alert()
	 * @param array $options associative array with keys: type, title, message, icon, dismissible, escape.
	 * @return string
	 */
	static function alertArray(array $options){
		$defaultOptions = ['type'=>'danger', 'title'=>NULL, 'message'=>NULL, 'icon'=>NULL, 'dismissible'=>true, 'escape'=>true];
		$options += $defaultOptions;
		return self::alert($options['type'],$options['title'],$options['message'],$options['icon'],$options['dismissible'],$options['escape']);
	}
	
	/**
	 * Prints to standard output a date selection form element composed by different selects for each element (day, month, year)
	 * @param array $options an associative array of options. All options are optional and default values will be used if no options are provided. Accepted keys are:
	 * <ul>
	 * 		<li>"months": an associative array with "number"=>"monthName"
	 * 		<li>"allowEmpty": boolean value: if true, the form element will allow not selecting a date, otherwise a date will always be selected. Defaults to true.
	 * 		<li>"last_year": integer value, the last selectable year. Defaults to current year.
	 * 		<li>"first_year": integer value, the first selectable year. Defaults to current year -10. 
	 * 		<li>"format": array of strings. Each string is in {"d","m","y"}. The order of the strings will be the order of the input components. Defaults to "d","m","y" italian format.
	 * 		<li>"day_name": the name for the day select input. Defaults to "giorno".
	 * 		<li>"month_name": the name for the month select input. Defaults to "mese".
	 * 		<li>"year_name": the name for the year select input. Defaults to "anno".
	 * 		<li>"label": string. the label for the form components. Defaults to "Data".
	 *      <li>"default_day": integer. The day that will be selected by default. Defaults to current day
	 *      <li>"default_month": integer. The month that will be selected by default. Defaults to current month
	 *      <li>"default_year": integer. The year that will be selected by default. Defaults to current year
	 * </ul>
	 */
	static function dateInput($options = []){
		$defaultOptions = [
				'months'=>[1=>'Gennaio',2=>'Febbraio',3=>'Marzo',4=>'Aprile',5=>'Maggio',6=>'Giugno',7=>'Luglio',8=>'Agosto',9=>'Settembre',10=>'Ottobre',11=>'Novembre',12=>'Dicembre'],
				'allowEmpty'=>true,
				'last_year'=>date('Y'),
				'first_year'=>date('Y')-10,
				'format'=>['d','m','y'],
				'day_name'=>'giorno',
				'month_name'=>'mese',
				'year_name'=>'anno',
				'label'=>'Data',
				'default_day'=>date('d'),
				'default_month'=>date('m'),
				'default_year'=>date('Y'),
		];
		
		$options += $defaultOptions; //merge $options and $defaultOptions (the first prevails if a key is present in both)
		extract($options); //creates local variable from associative array
		?>
		<div class="form-group">
			<label for="data_input"><?php echo $label?></label>
			<div class="row">
				<?php foreach($format as $f): //foreach letter in format (d,m,y)?>
					<?php switch($f): 
						case 'd': //print the day selector ?>
							<div class="col-md-4">
								<select name="<?php echo $day_name?>" class="form-control">
									<?php if($allowEmpty):?>
										<option value="">Seleziona un giorno</option>
									<?php endif;?>
									<?php for($i=1;$i<=31;$i++):?>
										<option value="<?php echo $i;?>" <?php echo ($i==$default_day)? 'selected' : ''?>><?php echo $i;?></option>
									<?php endfor; ?>
								</select>
							</div>
						<?php break;?>
						<?php case 'm': ?>
							<div class="col-md-4">
								<select name="<?php echo $month_name?>" class="form-control">
									<?php if($allowEmpty):?>
										<option value="">Seleziona un mese</option>
									<?php endif;?>
									<?php foreach($months as $value=>$monthName):?>
										<option value="<?php echo $value;?>" <?php echo ($value==$default_month)? 'selected' : ''?>><?php echo $monthName;?></option>
									<?php endforeach; ?>
								</select>
							</div>
						<?php break; ?>
						<?php case 'y': ?>
							<div class="col-md-4">
								<select name="<?php echo $year_name?>" class="form-control">
									<?php if($allowEmpty):?>
										<option value="">Seleziona un anno</option>
									<?php endif;?>
									<?php for($i=$last_year; $i>=$first_year; $i--):?>
										<option value="<?php echo $i;?>" <?php echo ($i==$default_year)? 'selected' : ''?>><?php echo $i;?></option>
									<?php endfor; ?>
								</select>
							</div>
						<?php break; ?>
						<?php default: ?>
							<p>Invalid format <?php echo $f?>. Valid formats are: m,d,y.</p>
					<?php endswitch; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php 
	}
	
	
	/**
	 * Prints to standard output a datetime selection form element composed by different selects for each element (day, month, year) and inputs for hour and minutes.
	 * @param array $options an associative array of options. All options are optional and default values will be used if no options are provided. Accepted keys are:
	 * <ul>
	 * 		<li>"months": an associative array with "number"=>"monthName"
	 * 		<li>"allowEmpty": boolean value: if true, the form element will allow not selecting a date, otherwise a date will always be selected. Defaults to true.
	 * 		<li>"last_year": integer value, the last selectable year. Defaults to current year.
	 * 		<li>"first_year": integer value, the first selectable year. Defaults to current year -10.
	 * 		<li>"format": array of strings. Each string is in {"d","m","y","hi"}. The order of the strings will be the order of the input components. Defaults to "d","m","y","hi" italian format.
	 * 		<li>"day_name": the name for the day select input. Defaults to "giorno".
	 * 		<li>"month_name": the name for the month select input. Defaults to "mese".
	 * 		<li>"year_name": the name for the year select input. Defaults to "anno".
	 * 		<li>"hour_name": the name for the year select input. Defaults to "ora".
	 * 		<li>"minutes_name": the name for the year select input. Defaults to "minuti".
	 * 		<li>"label": string. the label for the form components. Defaults to "Orario".
	 *      <li>"default_day": integer. The day that will be selected by default. Defaults to current day
	 *      <li>"default_month": integer. The month that will be selected by default. Defaults to current month
	 *      <li>"default_year": integer. The year that will be selected by default. Defaults to current year
	 *      <li>"default_hour": integer. The hour that will be selected by default. Defaults to empty
	 *      <li>"default_minutes": integer. The minutes that will be selected by default. Defaults to empty
	 * </ul>
	 */
	static function datetimeInput($options = []){
		$defaultOptions = [
				'months'=>[1=>'Gennaio',2=>'Febbraio',3=>'Marzo',4=>'Aprile',5=>'Maggio',6=>'Giugno',7=>'Luglio',8=>'Agosto',9=>'Settembre',10=>'Ottobre',11=>'Novembre',12=>'Dicembre'],
				'allowEmpty'=>true,
				'last_year'=>date('Y'),
				'first_year'=>date('Y')-10,
				'format'=>['d','m','y','hi'],
				'day_name'=>'giorno',
				'month_name'=>'mese',
				'year_name'=>'anno',
				'hour_name'=>'ora',
				'minutes_name'=>'minuti',
				'label'=>'Orario',
				'default_day'=>date('d'),
				'default_month'=>date('n'),
				'default_year'=>date('Y'),
				'default_hour'=>'', //date('h'),
				'default_minutes'=>'',//date('i'),
		];
		
		$options += $defaultOptions; //merge $options and $defaultOptions (the first prevails if a key is present in both)
		extract($options); //creates local variable from associative array
		?>
		<div class="form-group">
			<label for="data_input"><?php echo $label?></label>
			<div class="row">
				<?php foreach($format as $f): //foreach letter in format (d,m,y)?>
					<?php switch($f): 
						case 'd': //print the day selector ?>
							<div class="col-md-2">
								<div class="form-group">
									<label class="sagili-inner-label">Giorno</label>
									<select name="<?php echo $day_name?>" class="form-control">
										<?php if($allowEmpty):?>
											<option value="">Seleziona un giorno</option>
										<?php endif;?>
										<?php for($i=1;$i<=31;$i++):?>
											<option value="<?php echo $i;?>" <?php echo ($i==$default_day)? 'selected' : ''?>><?php echo $i;?></option>
										<?php endfor; ?>
									</select>
								</div>
							</div>
						<?php break;?>
						<?php case 'm': ?>
							<div class="col-md-4">
								<div class="form-group">
									<label class="sagili-inner-label">Mese</label>
									<select name="<?php echo $month_name?>" class="form-control">
										<?php if($allowEmpty):?>
											<option value="">Seleziona un mese</option>
										<?php endif;?>
										<?php foreach($months as $value=>$monthName):?>
											<option value="<?php echo $value;?>" <?php echo ($value==$default_month)? 'selected' : ''?>><?php echo $monthName;?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						<?php break; ?>
						<?php case 'y': ?>
							<div class="col-md-3">
								<div class="form-group">
									<label class="sagili-inner-label">Anno</label>
									<select name="<?php echo $year_name?>" class="form-control">
										<?php if($allowEmpty):?>
											<option value="">Seleziona un anno</option>
										<?php endif;?>
										<?php for($i=$last_year; $i>=$first_year; $i--):?>
											<option value="<?php echo $i;?>" <?php echo ($i==$default_year)? 'selected' : ''?>><?php echo $i;?></option>
										<?php endfor; ?>
									</select>
								</div>
							</div>
						<?php break; ?>
						<?php case 'hi': ?>
							<div class="col-md-3">
								<div class="row">
									<div class="col-xs-6">
										<div class="form-group">
											<label class="sagili-inner-label">Ora</label>
											<input class="form-control" type="number" min="0" max="23" placeholder="Ora" name="<?php echo $hour_name ?>" value="<?php echo $default_hour?>" <?php echo !$allowEmpty ? 'required' : ''?>/>
										</div>
									</div>
									<div class="col-xs-6">
										<div class="form-group">
											<label class="sagili-inner-label">Minuti</label>
											<input class="form-control" type="number" min="0" max="59" placeholder="Minuti" name="<?php echo $minutes_name ?>" value="<?php echo $default_minutes?>" <?php echo !$allowEmpty ? 'required' : ''?>/>
										</div>
									</div>
								</div>
							</div>
						<?php break; ?>
						<?php default: ?>
							<p>Invalid format <?php echo $f?>. Valid formats are: m,d,y,hi.</p>
					<?php endswitch; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php 
	}
}