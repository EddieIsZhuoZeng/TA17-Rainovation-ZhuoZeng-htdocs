<?php

/**
 * Copyright (c) 2011-2012 Dave Olsen, http://dmolsen.com
 * Licensed under the MIT license
 */
class Lana_Downloads_Manager_User_Agent_Parser{

	protected $regexes;

	/**
	 * Start up the parser by importing the json file to $this->regexes
	 */
	public function __construct() {
		$regexes_file  = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'ua-parser-regexes.json';
		$this->regexes = json_decode( file_get_contents( $regexes_file ) );
	}

	/**
	 * Sets up some standard variables as well as starts the user agent parsing process
	 *
	 * @param string $ua user agent string to test, defaults to an empty string
	 *
	 * @return object the result of the user agent parsing
	 */
	public function parse( $ua = '' ) {

		$result = (object) array(
			'ua'             => (object) array(),
			'os'             => (object) array(),
			'device'         => (object) array(),
			'to_full_string' => '',
			'ua_original'    => $ua
		);

		$result->ua     = $this->ua_parse( $ua );
		$result->os     = $this->os_parse( $ua );
		$result->device = $this->device_parse( $ua );

		$result->to_full_string = $this->to_full_string( $result->ua, $result->os );

		return $result;
	}

	/**
	 * Attempts to see if the user agent matches a user_agents_parsers regex from regexes.json
	 *
	 * @param string $ua_string
	 *
	 * @return object
	 */
	public function ua_parse( $ua_string = '' ) {

		/** build the default obj that will be returned */
		$ua = (object) array(
			'family'            => 'Other',
			'major'             => null,
			'minor'             => null,
			'patch'             => null,
			'to_string'         => '',
			'to_version_string' => ''
		);

		$ua_regexes = $this->regexes->user_agent_parsers;
		foreach ( $ua_regexes as $ua_regex ) {

			if ( preg_match( '/' . str_replace( '/', '\/', str_replace( '\/', '/', $ua_regex->regex ) ) . '/i', $ua_string, $matches ) ) {

				if ( ! isset( $matches[1] ) ) {
					$matches[1] = 'Other';
				}
				if ( ! isset( $matches[2] ) ) {
					$matches[2] = null;
				}
				if ( ! isset( $matches[3] ) ) {
					$matches[3] = null;
				}
				if ( ! isset( $matches[4] ) ) {
					$matches[4] = null;
				}

				$ua->family = isset( $ua_regex->family_replacement ) ? str_replace( '$1', $matches[1], $ua_regex->family_replacement ) : $matches[1];

				$ua->major = isset( $ua_regex->v1_replacement ) ? $ua_regex->v1_replacement : $matches[2];
				$ua->minor = isset( $ua_regex->v2_replacement ) ? $ua_regex->v2_replacement : $matches[3];
				$ua->patch = isset( $ua_regex->v3_replacement ) ? $ua_regex->v3_replacement : $matches[4];

				$ua->to_string         = $this->to_string( $ua );
				$ua->to_version_string = $this->to_version_string( $ua );

				return $ua;
			}
		}

		return $ua;
	}

	/**
	 * Attempts to see if the user agent matches an os_parsers regex from regexes.json
	 *
	 * @param string $ua_string user agent string to test
	 *
	 * @return object  the result of the os parsing
	 */
	public function os_parse( $ua_string = '' ) {

		$os = (object) array(
			'family'            => 'Other',
			'major'             => null,
			'minor'             => null,
			'patch'             => null,
			'patch_minor'       => null,
			'to_string'         => '',
			'to_version_string' => ''
		);

		$os_regexes = $this->regexes->os_parsers;
		foreach ( $os_regexes as $os_regex ) {

			if ( preg_match( '/' . str_replace( '/', '\/', str_replace( '\/', '/', $os_regex->regex ) ) . '/i', $ua_string, $matches ) ) {

				if ( ! isset( $matches[1] ) ) {
					$matches[1] = 'Other';
				}
				if ( ! isset( $matches[2] ) ) {
					$matches[2] = null;
				}
				if ( ! isset( $matches[3] ) ) {
					$matches[3] = null;
				}
				if ( ! isset( $matches[4] ) ) {
					$matches[4] = null;
				}
				if ( ! isset( $matches[5] ) ) {
					$matches[5] = null;
				}

				/** os name */
				$os->family = isset( $os_regex->os_replacement ) ? $os_regex->os_replacement : $matches[1];

				/** version properties */
				$os->major       = isset( $os_regex->os_v1_replacement ) ? $os_regex->os_v1_replacement : $matches[2];
				$os->minor       = isset( $os_regex->os_v2_replacement ) ? $os_regex->os_v2_replacement : $matches[3];
				$os->patch       = isset( $os_regex->os_v3_replacement ) ? $os_regex->os_v3_replacement : $matches[4];
				$os->patch_minor = isset( $os_regex->os_v4_replacement ) ? $os_regex->os_v4_replacement : $matches[5];

				/** extra strings */
				$os->to_string         = $this->to_string( $os );
				$os->to_version_string = $this->to_version_string( $os );

				return $os;
			}

		}

		return $os;
	}

	/**
	 * Attempts to see if the user agent matches a device_parsers regex from regexes.json
	 *
	 * @param string $ua_string user agent string to test
	 *
	 * @return object  the result of the device parsing
	 */
	public function device_parse( $ua_string = '' ) {

		$device = (object) array(
			'family' => 'Other'
		);

		$device_regexes = $this->regexes->device_parsers;
		foreach ( $device_regexes as $device_regex ) {

			if ( preg_match( '/' . str_replace( '/', '\/', str_replace( '\/', '/', $device_regex->regex ) ) . '/i', $ua_string, $matches ) ) {

				if ( ! isset( $matches[1] ) ) {
					$matches[1] = 'Other';
				}

				$device->family = isset( $device_regex->device_replacement ) ? str_replace( '$1', str_replace( "_", " ", $matches[1] ), $device_regex->device_replacement ) : str_replace( "_", " ", $matches[1] );

				return $device;
			}
		}

		return $device;
	}

	/**
	 * Returns a string consisting of the family and full version number based on the provided type
	 *
	 * @param object $obj the object (ua or os) to be used
	 *
	 * @return string  the result of combining family and version
	 */
	public function to_string( $obj ) {

		$version_string = $this->to_version_string( $obj );
		$string         = ! empty( $version_string ) ? $obj->family . ' ' . $version_string : $obj->family;

		return $string;
	}

	/**
	 * Returns a string consisting of just the full version number based on the provided type
	 *
	 * @param object $obj the obj that contains version number bits
	 *
	 * @return string  the result of combining the version number bits together
	 */
	public function to_version_string( $obj ) {

		$version_string = isset( $obj->major ) ? $obj->major : '';
		$version_string = isset( $obj->minor ) ? $version_string . '.' . $obj->minor : $version_string;
		$version_string = isset( $obj->patch ) ? $version_string . '.' . $obj->patch : $version_string;
		$version_string = isset( $obj->patch_minor ) ? $version_string . '.' . $obj->patch_minor : $version_string;

		return $version_string;
	}

	/**
	 * Returns a string consistig of the family and full version number for both the browser and os
	 *
	 * @param object $ua the ua object
	 * @param object $os the os object
	 *
	 * @return string  the result of combining family and version
	 */
	public function to_full_string( $ua, $os ) {

		$full_string = $this->to_string( $ua ) . '/' . $this->to_string( $os );

		return $full_string;
	}
}
