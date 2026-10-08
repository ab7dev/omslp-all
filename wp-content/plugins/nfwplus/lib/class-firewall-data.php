<?php
/*
 +=====================================================================+
 |    _   _ _        _       _____ _                        _ _        |
 |   | \ | (_)_ __  (_) __ _|  ___(_)_ __ _____      ____ _| | |       |
 |   |  \| | | '_ \ | |/ _` | |_  | | '__/ _ \ \ /\ / / _` | | |       |
 |   | |\  | | | | || | (_| |  _| | | | |  __/\ V  V / (_| | | |       |
 |   |_| \_|_|_| |_|/ |\__,_|_|   |_|_|  \___| \_/\_/ \__,_|_|_|       |
 |                |__/                                                 |
 |  (c) NinTechNet Limited ~ https://nintechnet.com/                   |
 +=====================================================================+
*/

if ( class_exists('NinjaFirewall_data') ) {
	return;
}


class NinjaFirewall_data {


	/**
	 * Transform a user input depending on the firewall rule requesting it (SQL, JS or path).
	 */
	static public function transform( $string, $where ) {

		if (! $string ) {
			return;
		}
		/**
		 * SQL
		 */
		if ( $where == 1 ) {
			/**
			 * Remove MySQL comments, as well we some unwanted characters,
			 * trim the output and convert it to lower cases.
			 */
			$norm = trim( preg_replace_callback(
				'((^([^a-z/&|#]*)|([\'"])(?:\\\\.|[^\n\3\\\\])*?\3|(?:[0-9a-z_$]+)|.)'.
				'(?:\s|--[^\n]*+\n|/\*(?:[^*!]|\*(?!/))*+\*/)*'.
				'(?:(?:\#|--(?:[\x00-\x20\x7f]|$)|/\*$)[^\n]*+\n|/\*!(?:\d{5})?|\*/|/\*'.
				'(?:[^*!]|\*(?!/))*+\*/)*)si',
				[ __CLASS__, 'delete_sql_comments'],  "$string\n"
			) );
			$norm = preg_replace('/[\'"]\x20*\+?\x20*[\'"]/', '', $norm);
			$norm = strtolower( str_replace(	['+', "'", '"', "(", ')', '`', ',', ';'], ' ', $norm) );
		/**
		 * JS
		 */
		} elseif ( $where == 2 ) {
			/**
			 * Same as above but for JS comments.
			 * Note:	-It should be used ONLY with pure JS (sub)string,
			 * 		otherwise it could be bypassed easily.
			 *  		-JS being case-sensitive, we don't change the case.
			 */
			$norm = trim( preg_replace_callback(
				'((^|([\'"])(?:\\\\.|[^\n\2\\\\])*?\2|(?:[0-9a-z_$]+)|.)'.
				'(?://[^\n]*+\n|/\*(?:[^*]|\*(?!/))*+\*/)*)si',
				[ __CLASS__, 'delete_js_comments'], "$string\n"
			) );
			/**
			 * Remove/replace spaces first, then comments left and obfuscated string.
			 */
			$norm = preg_replace(
				['/[\n\r\t\f\v]/', '`/\*\s*\*/`', '/[\'"`]\x20*[+.]?\x20*[\'"`]/'], ['', ' ', ''],
				$norm
			);
		/**
		 * Path
		 */
		} elseif ( $where == 3 ) {
			$norm = preg_replace( ['`([\\\"\'^]|\$\w+)`', '`([,;]|\s+)`'], ['', ' '], $string );
			$norm = preg_replace(
				['`/(\./)+`','`/{2,}`', '`/(.+?)/\.\./\1\b`', '`\n`', '`\\\`'], ['/', '/', '/\1', '', ''],
				$norm
			);
		}
		return $norm;
	}


	/**
	 * Remove comment in a SQL query.
	 */
	static private function delete_sql_comments( $match ) {

		if (! empty($match[2]) ) {
			return ' ';
		}
		if ( $match[0] != $match[1] ) {
			return "{$match[1]} ";
		}
		return $match[1];
	}


	/**
	 * Remove comment in JS code.
	 */
	static private function delete_js_comments( $match ) {

		if ( $match[0] != $match[1] ) {
			return "{$match[1]} ";
		}
		return $match[1];
	}


	/**
	 * Normalize a user input.
	 */
	static public function normalize( $string, $nfw_rules ) {

		if ( empty( $string ) ) {
			return;
		}

		$norm = rawurldecode( $string );
		if ( strpos( $norm, '%') !== false ) {
			$norm = rawurldecode( $norm );
		}
		if (! $norm ) {
			return $string;
		}

		if ( preg_match(
			'/&(?:#x(?:00)*[0-9a-f]{2}|#0*[12]?[0-9]{2}|amp|[lg]t|nbsp|quot)(?!;|\d)/i', $norm
		) ) {

			$norm = preg_replace(
				'/&(#x(?:00)*[0-9a-f]{2}|#0*[12]?[0-9]{2}|amp|[lg]t|nbsp|quot)(?!;|\d)/i', '&\1;',
				$norm
			);
			if (! $norm ) {
				return $string;
			}
		}

		if ( preg_match('/\\\(?:0?[4-9][0-9]|1[0-7][0-9])/', $norm ) ) {
			$norm = preg_replace_callback(
				'/\\\(0?[4-9][0-9]|1[0-7][0-9])/', [ __CLASS__, 'oct2ascii'], $norm
			);
			if (! $norm ) {
				return $string;
			}
		}

		if ( preg_match('/\\\x[a-f0-9]{2}/i', $norm) ) {
			$norm = preg_replace_callback('/\\\x([a-f0-9]{2})/i', [ __CLASS__, 'hex2ascii'], $norm );
			if (! $norm ) {
				return $string;
			}
		}

		$norm = self::html_decode( $norm );
		if (! $norm ) {
			return $string;
		}

		if ( preg_match('/&#x?[0-9a-f]+;/i', $norm ) ) {
			$norm = preg_replace('/(&#x?[0-9a-f]+;)/i', '', $norm );
			if (! $norm ) {
				return $string;
			}
		}

		if ( preg_match( '/(?:%|\\\)u(?:[0-9a-f]{4}|\{0*[0-9a-f]{2}\})/i', $norm ) ) {
			$norm = preg_replace_callback(
				'/(?:%|\\\)u(?:([0-9a-f]{4})|\{0*([0-9a-f]{2})\})/i', [ __CLASS__, 'udecode'], $norm
			);
			if (! $norm ) {
				return $string;
			}
		}

		if ( empty( $nfw_rules[2]['ena'] ) ) {
			$norm = preg_replace('/\x0|%00/', '', $norm );
			if (! $norm ) {
				return $string;
			}
		}

		return $norm;
	}


	/**
	 * Octal to ASCII.
	 */
	private static function oct2ascii( $match ) {

		return chr( octdec( $match[1] ) );
	}


	/**
	 * Hexadecimal to ASCII.
	 */
	private static function hex2ascii( $match ) {

		return chr( hexdec( $match[1] ) );
	}


	/**
	 * Unicode decoding.
	 */
	private static function udecode( $match ) {

		if ( isset( $match[2] ) ) {
			return json_decode('"\\u00'. $match[2] .'"');
		}
		return json_decode('"\\u' .$match[1] .'"');
	}


	/**
	 * HTML decode.
	 */
	private static function html_decode( $norm ) {
		// We don't use html_entity_decode with ENT_HTML5 because
		// it does not decode some entities that could be used to evade WAF filters.
		$in = [
			'&Tab;',					//		&#x00009;	&#9;
			'&NewLine;',			//		&#x0000A;	&#10;
			'&excl;',				//		&#x00021;	&#33;
			'&quot;',				//	" 	&#x00022;	&#34;
			'&QUOT;',
			'&num;',					//	#	&#x00023;	&#35;
			'&dollar;',				//	$	&#x00024;	&#36;
			'&percnt;',				//	%	&#x00025;	&#37;
			'&amp;',					//	&	&#x00026;	&#38;
			'&AMP;',
			'&apos;',				//	'	&#x00027;	&#39;
			'&lpar;',				//	(	&#x00028;	&#40;
			'&rpar;',				//	)	&#x00029;	&#41;
			'&ast;',					//	*	&#x0002A;	&#42;
			'&midast;',
			'&plus;',				//	+	&#x0002B;	&#43;
			'&comma;',				//	,	&#x0002C;	&#44;
			'&period;',				//	.	&#x0002E;	&#46;
			'&sol;',					//	/	&#x0002F;	&#47;
			'&colon;',				//	:	&#x0003A;	&#58;
			'&semi;',				//	;	&#x0003B;	&#59;
			'&lt;',					//	<	&#x0003C;	&#60;
			'&LT;',
			'&equals;',				//	=	&#x0003D;	&#61;
			'&gt;',					//	>	&#x0003E;	&#62;
			'&GT;',
			'&quest;',				//	?	&#x0003F;	&#63;
			'&commat;',				//	@	&#x00040;	&#64;
			'&lsqb;',				//	[	&#x0005B;	&#91;
			'&lbrack;',
			'&bsol;',				//	\	&#x0005C;	&#92;
			'&rsqb;',				//	]	&#x0005D;	&#93;
			'&rbrack;',
			'&Hat;',					//	^	&#x0005E;	&#94;
			'&lowbar;',				//	_	&#x0005F;	&#95;
			'&grave;',				//	`	&#x00060;	&#96;
			'&DiacriticalGrave;',
			'&lcub;',				//	{	&#x0007B;	&#123;
			'&lbrace;',
			'&verbar;',				//	|	&#x0007C;	&#124;
			'&vert;',
			'&VerticalLine;',
			'&rcub;',				//	}	&#x0007D;	&#125;
			'&rbrace;',
			'&nbsp;',				//	' ' &#x000A0;	&#160;
			'&NonBreakingSpace;',
			// While we are here, we modify these ones too:
			'&nvlt;',
			'&nvgt;',
			"\xa0"
		];

		$out = [
			'',		//	&Tab;
			'',		//	&NewLine;
			'!',		//	&excl;
			'"',		//	&quot;
			'"',		// &QUOT;
			'#',		//	&num;
			'$',		//	&dollar;
			'%',		//	&percnt;
			'&',		//	&amp;
			'&',		//	&AMP;
			"'",		//	&apos;
			'(',		//	&lpar;
			')',		//	&rpar;
			'*',		//	&ast;
			'*',		//	&midast;
			'+',		//	&plus;
			',',		//	&comma;
			'.',		//	&period;
			'/',		//	&sol;
			':',		//	&colon;
			';',		//	&semi;
			'<',		//	&lt;
			'<',		//	&LT;
			'=',		//	&equals;
			'>',		//	&gt;
			'>',		//	&GT;
			'?',		//	&quest;
			'@',		//	&commat;
			'[',		//	&lsqb;
			'[',		//	&lbrack;
			'\\',		//	&bsol;
			']',		//	&rsqb;
			']',		//	&rbrack;
			'^',		//	&Hat;
			'_',		//	&lowbar;
			'`',		//	&grave;
			'`',		//	&DiacriticalGrave;
			'{',		//	&lcub;
			'{',		//	&lbrace;
			'|',		//	&verbar;
			'|',		//	&vert;
			'|',		//	&VerticalLine;
			'}',		//	&rcub;
			'}',		//	&rbrace;
			' ',		//	&nbsp;
			' ',		//	&NonBreakingSpace;'
			'',		// &nvlt;
			'',		// &nvgt;
			' '		// NBSP
		];

		$normout = str_replace( $in, $out, $norm );
		$normout = html_entity_decode( $normout, ENT_QUOTES, 'UTF-8');

		return $normout;
	}


	/**
	 * Compress a string.
	 */
	public static function compress( $string, $where = null ) {

		if (! $string ) {
			return;
		}

		/**
		 * SQL input.
		 */
		if ( $where == 1 ) {
			$replace = ' ';

		/**
		 * Anything else.
		 */
		} else {
			$replace = '';
		}

		$string = str_replace( ["\x09", "\x0a","\x0b", "\x0c", "\x0d"], $replace, $string );
		$string = trim ( preg_replace('/\x20{2,}/', ' ', $string ) );
		return $string;
	}


	/**
	 * Sanitize a user input.
	 */
	public static function sanitise( $str, $how, $msg, $nfw_, $ac_wl_input = null ) {

		if ( defined('NFW_STATUS') ) {
			return;
		}

		if ( empty( $str ) ) {
			return $str;
		}

		/**
		 * String.
		 */
		if ( is_string( $str ) ) {
			// If we are using shmop, we don't have a SQL connection
			if (! empty( $nfw_['shm_id'] ) ) {
				$how = 2;
			}
			// We sanitise variables **value** either with :
			// -mysql_real_escape_string* to escape [\x00], [\n], [\r], [\],
			//	 ['], ["] and [\x1a]
			//	-str_replace to escape backtick [`] and replace '<', '>' with HTML entities.
			//	Applies to $_GET, $_POST, $_SERVER['HTTP_USER_AGENT']
			//	and $_SERVER['HTTP_REFERER'].
			// -str_replace to escape [*?] in GET requests containing a slash [/]
			//	to block shell evasion attempts such as `/???/??t /???/??ss??`.
			//
			// Or:
			//
			// -str_replace to escape ["], ['], [`], [\] and replace '<', '>' with HTML entities.
			//	-str_replace to replace [\n], [\r], [\x1a] and [\x00] with [-]
			//	Applies to $_SERVER['PATH_INFO'], $_SERVER['PATH_TRANSLATED']
			//	and $_SERVER['PHP_SELF']
			//
			// Or:
			//
			// -str_replace to escape ['], [`] and , [\]
			//	-str_replace to replace [\x1a] and [\x00] with [-]
			//	-str_replace to replace [<] and with [&lt;]
			//	Applies to $_COOKIE only
			//
			if ( $how == 1 ) {
				// Full WAF
				if (! empty( $nfw_['mysqli'] ) ) {
					$str2 = $nfw_['mysqli']->real_escape_string( $str );
				// WP WAF
				} else {
					global $wpdb;
					$str2 = $wpdb->_real_escape( $str );
				}
				$str2 = str_replace(	['`', '<', '>'], ['\\`', '&lt;', '&gt;'],	$str2 );
				if ( $msg == 'GET' && strpos( $str2, '/') !== false ) {
					$str2 = str_replace( ['*', '?'], ['\*', '\?'], $str2 );
				}
			} elseif ( $how == 2 ) {
				$str2 = str_replace(	['\\', "'", '"', "\x0d", "\x0a", "\x00", "\x1a", '`', '<', '>'],
					['\\\\', "\\'", '\\"', '-', '-', '-', '-', '\\`', '&lt;', '&gt;'],	$str );
			} else {
				$str2 = str_replace(	['\\', "'", "\x00", "\x1a", '`', '<'],
					['\\\\', "\\'", '-', '-', '\\`', '&lt;'],	$str );
			}
			// Don't sanitise the string if we are running in Debugging Mode :
			if (! empty( $nfw_['nfw_options']['debug'] ) ) {
				if ( $str2 != $str ) {

					$nfw_['incidentID'] = NinjaFirewall_log::write(
						'Sanitising user input',
						"$msg: $str",
						NFWLOG_DEBUG, 0, $nfw_['nfw_options'], $nfw_['log_dir']
					);
				}
				return $str;
			}
		/**
		 * Log and return the sanitised string.
		 */
		if ( $str2 != $str ) {

			$nfw_['incidentID'] = NinjaFirewall_log::write(
				'Sanitising user input',
				"$msg: $str",
				NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
			);
		}
		return $str2;

		/**
		 * Array.
		 */
		} else if ( is_array( $str ) ) {
			foreach( $str as $key => $value ) {
				/**
				 * Don't sanitise whitelisted user input access control.
				 */
				if ( isset( $ac_wl_input[ $msg ][ $key ] ) ) {
					continue;
				}
				/**
				 * COOKIE.
				 */
				if ( $how == 3 ) {
					$key2 = str_replace(	['\\', "'", "\x00", "\x1a", '`', '<', '>'],
						['\\\\', "\\'", '-', '-', '\\`', '&lt;', '&gt;'],	$key, $count );
				} else {
					// We sanitise variables **name** using :
					// -str_replace to escape [\], ['] and ["]
					// -str_replace to replace [\n], [\r], [\x1a] and [\x00] with [-]
					//	-str_replace to replace [`], [<] and [>] with their HTML entities (&#96; &lt; &gt;)
					$key2 = str_replace(	['\\', "'", '"', "\x0d", "\x0a", "\x00", "\x1a", '`', '<', '>'],
						['\\\\', "\\'", '\\"', '-', '-', '-', '-', '&#96;', '&lt;', '&gt;'],	$key, $count );
				}
				if ( $count ) {
					unset( $str[ $key ] );

					$nfw_['incidentID'] = NinjaFirewall_log::write(
						'Sanitising user input',
						"$msg: $key",
						NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
					);
				}
				/**
				 * Sanitise the value.
				 */
				$str[ $key2 ] = self::sanitise( $value, $how, $msg, $nfw_, $ac_wl_input );
			}
			return $str;
		}
	}


	/**
	 * Sanitize a filename.
	 */
	public static function sanitise_filename( $array, $key, $value ) {

		array_walk_recursive(
			$array, function( &$v, $k ) use ( $key, $value ) {
				if (! empty( $v ) && $v == $key ) {
					$v = $value;
				}
			}
		);
		return $array;
	}


	/**
	 * Sanitize a file extension.
	 */
	public static function sanitise_extensions( $filename, $subs ) {

		$ret          = [];
		$ret['count'] = 0;
		$parts        = explode('.', $filename );
		$ret['name']  = array_shift( $parts );
		$extension    = array_pop( $parts );

		foreach ( $parts as $part ) {
			if (! empty( $part ) ) {
				$ret['name'] .= ".{$part}{$subs}";
				++$ret['count'];
			}
		}
		if ( $extension ) {
			$ret['name'] .= ".{$extension}";
		}
		return $ret;
	}


	/**
	 * Transform a string to use in a filename.
	 */
	public static function sanitise_string_fn( $string ) {

		return preg_replace('/[^-\.\w]/', 'x', $string );
	}

}
// =====================================================================
// EOF
