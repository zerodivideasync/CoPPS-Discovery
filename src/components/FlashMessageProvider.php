<?php

require_once APP_ROOT_ABS . '/components/Session.php';
require_once APP_ROOT_ABS . '/templates/helpers/HtmlHelper.php';

class FlashMessageProvider {

	public static $sessionKey = 'FlashMessages';

	static function success(array $options) {
		$messages = Session::read(self::$sessionKey);
		if (!is_array($messages)) {
			$messages = [];
		}
		$options['type'] = 'success';
		array_push($messages, $options);
		Session::write(self::$sessionKey, $messages);
	}

	static function error(array $options) {
		$messages = Session::read(self::$sessionKey);
		if (!is_array($messages)) {
			$messages = [];
		}
		$options['type'] = 'danger';
		array_push($messages, $options);
		Session::write(self::$sessionKey, $messages);
	}

	static function warning(array $options) {
		$messages = Session::read(self::$sessionKey);
		if (!is_array($messages)) {
			$messages = [];
		}
		$options['type'] = 'warning';
		array_push($messages, $options);
		Session::write(self::$sessionKey, $messages);
	}

	static function show() {
		$messages = Session::read(self::$sessionKey);
		if (is_array($messages)) {
			foreach ($messages as $message) {
				echo HtmlHelper::alertArray($message);
			}
		}
		$messages = [];
		Session::write(self::$sessionKey, $messages);
	}

}
