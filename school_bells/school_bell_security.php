<?php

/**
 * Validate values before placing them in a FreeSWITCH originate command.
 * These deliberately accept a narrow subset of the values supported by the UI.
 */
function school_bell_valid_destination($value) {
	return is_string($value)
		&& strlen($value) <= 80
		&& preg_match('/^[A-Za-z0-9*#._+\-]+$/D', $value) === 1;
}

function school_bell_valid_context($value) {
	return is_string($value)
		&& strlen($value) <= 253
		&& preg_match('/^[A-Za-z0-9.-]+$/D', $value) === 1;
}

function school_bell_valid_recording_filename($value) {
	return is_string($value)
		&& strlen($value) <= 255
		&& $value !== ''
		&& strpos($value, '..') === false
		&& $value[0] !== '/'
		&& strpos($value, "\\") === false
		&& preg_match('/^[A-Za-z0-9._\/-]+$/D', $value) === 1;
}

function school_bell_valid_recording_path($value) {
	return is_string($value)
		&& strlen($value) <= 1024
		&& strpos($value, '..') === false
		&& preg_match('~^/[A-Za-z0-9._/+:-]+$~D', $value) === 1;
}
