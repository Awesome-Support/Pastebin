<?php
/**
 * @package   Awesome Support Pastebin
 * @author    ThemeAvenue <web@themeavenue.net>
 * @license   GPL-2.0+
 * @link      http://themeavenue.net
 * @copyright 2015 ThemeAvenue
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function as_pastebin_send_paste( $data = array() ) {

	if ( empty( $data ) && ! empty( $_POST ) ) {
		$data = $_POST;
	}

	// Make sure we have data to paste
	if ( empty( $data ) ) {
		return new WP_Error( 'no_data', esc_html__( 'Not data to send to Pastebin', 'as-pastebin' ) );
	}

	$post_id = isset( $data['post_id'] ) ? (int) $data['post_id'] : '';

	// Make sure we have a post to attach the paste to
	if ( empty( $post_id ) ) {
		return new WP_Error( 'no_post_id', esc_html__( 'Impossible to identify the post to which this paste is related', 'as-pastebin' ) );
	}

	$post = get_post( $post_id );

	// Make sure the post is either a ticket or a ticket reply
	if ( ! is_object( $post ) || ! is_a( $post, 'WP_Post' ) || ! in_array( $post->post_type, array( 'ticket', 'ticket_reply' ) )  ) {
		return new WP_Error( 'no_ticket', esc_html__( 'A paste must be attached to a ticket or a reply only', 'as-pastebin' ) );
	}

	$dev_key = trim( wpas_get_option( 'pastebin_dev_key', '' ) );

	// A developer key is required for using Pastebin API
	if ( empty( $dev_key ) ) {
		return new WP_Error( 'no_dev_key', esc_html__( 'A developer API key is required', 'as-pastebin' ) );
	}

	// Make sure we have some code to paste
	if ( empty( $data['code'] ) ) {
		return new WP_Error( 'no_code', esc_html__( 'There is no code to paste', 'as-pastebin' ) );
	}

	// Get the code format and fallback on default if there is no format in $data
	$format = isset( $data['paste_format'] ) && ! empty( $data['paste_format'] ) ? filter_input( 'string', $data['paste_format'] ) : wpas_get_option( 'pastebin_paste_format', '' );

	// Get the paste name
	$name = isset( $data['paste_name'] ) && ! empty( $data['paste_name'] ) ? filter_input( 'string', $data['paste_name'] ) : "Paste for post ";

	$args = array(
			'body' => array(
					'api_option'            => 'paste',
					'api_paste_private'     => wpas_get_option( 'pastebin_paste_private', '1' ),
					'api_paste_name'        => sanitize_text_field( $name ),
					'api_paste_expire_date' => wpas_get_option( 'pastebin_paste_expire', '10M' ),
					'api_paste_format'      => $format,
					'api_dev_key'           => $dev_key,
					'api_paste_code'        => $data['code'],
			)
	);

	$response = wp_remote_post( esc_url( 'http://pastebin.com/api/api_post.php' ), $args );

	return $response;

}

/**
 * Get the list of languages supported by Pastebin
 *
 * @since 0.1.0
 * @return array
 */
function as_pastebin_get_code_formats() {

	$formats = array(
			'4cs'           => '4CS',
			'6502acme'      => '6502 ACME Cross Assembler',
			'6502kickass'   => '6502 Kick Assembler',
			'6502tasm'      => '6502 TASM/64TASS',
			'abap'          => 'ABAP',
			'actionscript'  => 'ActionScript',
			'actionscript3' => 'ActionScript 3',
			'ada'           => 'Ada',
			'aimms'         => 'AIMMS',
			'algol68'       => 'ALGOL 68',
			'apache'        => 'Apache Log',
			'applescript'   => 'AppleScript',
			'apt_sources'   => 'APT Sources',
			'arm'           => 'ARM',
			'asm'           => 'ASM (NASM)',
			'asp'           => 'ASP',
			'asymptote'     => 'Asymptote',
			'autoconf'      => 'autoconf',
			'autohotkey'    => 'Autohotkey',
			'autoit'        => 'AutoIt',
			'avisynth'      => 'Avisynth',
			'awk'           => 'Awk',
			'bascomavr'     => 'BASCOM AVR',
			'bash'          => 'Bash',
			'basic4gl'      => 'Basic4GL',
			'dos'           => 'Batch',
			'bibtex'        => 'BibTeX',
			'blitzbasic'    => 'Blitz Basic',
			'b3d'           => 'Blitz3D',
			'bmx'           => 'BlitzMax',
			'bnf'           => 'BNF',
			'boo'           => 'BOO',
			'bf'            => 'BrainFuck',
			'c'             => 'C',
			'c_winapi'      => 'C (WinAPI)',
			'c_mac'         => 'C for Macs',
			'cil'           => 'C Intermediate Language',
			'csharp'        => 'C#',
			'cpp'           => 'C++',
			'cpp-winapi'    => 'C++ (WinAPI)',
			'cpp-qt'        => 'C++ (with Qt extensions)',
			'c_loadrunner'  => 'C: Loadrunner',
			'caddcl'        => 'CAD DCL',
			'cadlisp'       => 'CAD Lisp',
			'cfdg'          => 'CFDG',
			'chaiscript'    => 'ChaiScript',
			'chapel'        => 'Chapel',
			'clojure'       => 'Clojure',
			'klonec'        => 'Clone C',
			'klonecpp'      => 'Clone C++',
			'cmake'         => 'CMake',
			'cobol'         => 'COBOL',
			'coffeescript'  => 'CoffeeScript',
			'cfm'           => 'ColdFusion',
			'css'           => 'CSS',
			'cuesheet'      => 'Cuesheet',
			'd'             => 'D',
			'dart'          => 'Dart',
			'dcl'           => 'DCL',
			'dcpu16'        => 'DCPU-16',
			'dcs'           => 'DCS',
			'delphi'        => 'Delphi',
			'oxygene'       => 'Delphi Prism (Oxygene)',
			'diff'          => 'Diff',
			'div'           => 'DIV',
			'dot'           => 'DOT',
			'e'             => 'E',
			'ezt'           => 'Easytrieve',
			'ecmascript'    => 'ECMAScript',
			'eiffel'        => 'Eiffel',
			'email'         => 'Email',
			'epc'           => 'EPC',
			'erlang'        => 'Erlang',
			'fsharp'        => 'F#',
			'falcon'        => 'Falcon',
			'fo'            => 'FO Language',
			'f1'            => 'Formula One',
			'fortran'       => 'Fortran',
			'freebasic'     => 'FreeBasic',
			'freeswitch'    => 'FreeSWITCH',
			'gambas'        => 'GAMBAS',
			'gml'           => 'Game Maker',
			'gdb'           => 'GDB',
			'genero'        => 'Genero',
			'genie'         => 'Genie',
			'gettext'       => 'GetText',
			'go'            => 'Go',
			'groovy'        => 'Groovy',
			'gwbasic'       => 'GwBasic',
			'haskell'       => 'Haskell',
			'haxe'          => 'Haxe',
			'hicest'        => 'HicEst',
			'hq9plus'       => 'HQ9 Plus',
			'html4strict'   => 'HTML',
			'html5'         => 'HTML 5',
			'icon'          => 'Icon',
			'idl'           => 'IDL',
			'ini'           => 'INI file',
			'inno'          => 'Inno Script',
			'intercal'      => 'INTERCAL',
			'io'            => 'IO',
			'ispfpanel'     => 'ISPF Panel Definition',
			'j'             => 'J',
			'java'          => 'Java',
			'java5'         => 'Java 5',
			'javascript'    => 'JavaScript',
			'jcl'           => 'JCL',
			'jquery'        => 'jQuery',
			'json'          => 'JSON',
			'julia'         => 'Julia',
			'kixtart'       => 'KiXtart',
			'latex'         => 'Latex',
			'ldif'          => 'LDIF',
			'lb'            => 'Liberty BASIC',
			'lsl2'          => 'Linden Scripting',
			'lisp'          => 'Lisp',
			'llvm'          => 'LLVM',
			'locobasic'     => 'Loco Basic',
			'logtalk'       => 'Logtalk',
			'lolcode'       => 'LOL Code',
			'lotusformulas' => 'Lotus Formulas',
			'lotusscript'   => 'Lotus Script',
			'lscript'       => 'LScript',
			'lua'           => 'Lua',
			'm68k'          => 'M68000 Assembler',
			'magiksf'       => 'MagikSF',
			'make'          => 'Make',
			'mapbasic'      => 'MapBasic',
			'matlab'        => 'MatLab',
			'mirc'          => 'mIRC',
			'mmix'          => 'MIX Assembler',
			'modula2'       => 'Modula 2',
			'modula3'       => 'Modula 3',
			'68000devpac'   => 'Motorola 68000 HiSoft Dev',
			'mpasm'         => 'MPASM',
			'mxml'          => 'MXML',
			'mysql'         => 'MySQL',
			'nagios'        => 'Nagios',
			'netrexx'       => 'NetRexx',
			'newlisp'       => 'newLISP',
			'nginx'         => 'Nginx',
			'nimrod'        => 'Nimrod',
			'text'          => 'None',
			'nsis'          => 'NullSoft Installer',
			'oberon2'       => 'Oberon 2',
			'objeck'        => 'Objeck Programming Langua',
			'objc'          => 'Objective C',
			'ocaml-brief'   => 'OCalm Brief',
			'ocaml'         => 'OCaml',
			'octave'        => 'Octave',
			'pf'            => 'OpenBSD PACKET FILTER',
			'glsl'          => 'OpenGL Shading',
			'oobas'         => 'Openoffice BASIC',
			'oracle11'      => 'Oracle 11',
			'oracle8'       => 'Oracle 8',
			'oz'            => 'Oz',
			'parasail'      => 'ParaSail',
			'parigp'        => 'PARI/GP',
			'pascal'        => 'Pascal',
			'pawn'          => 'Pawn',
			'pcre'          => 'PCRE',
			'per'           => 'Per',
			'perl'          => 'Perl',
			'perl6'         => 'Perl 6',
			'php'           => 'PHP',
			'php-brief'     => 'PHP Brief',
			'pic16'         => 'Pic 16',
			'pike'          => 'Pike',
			'pixelbender'   => 'Pixel Bender',
			'plsql'         => 'PL/SQL',
			'postgresql'    => 'PostgreSQL',
			'postscript'    => 'PostScript',
			'povray'        => 'POV-Ray',
			'powershell'    => 'Power Shell',
			'powerbuilder'  => 'PowerBuilder',
			'proftpd'       => 'ProFTPd',
			'progress'      => 'Progress',
			'prolog'        => 'Prolog',
			'properties'    => 'Properties',
			'providex'      => 'ProvideX',
			'puppet'        => 'Puppet',
			'purebasic'     => 'PureBasic',
			'pycon'         => 'PyCon',
			'python'        => 'Python',
			'pys60'         => 'Python for S60',
			'q'             => 'q/kdb+',
			'qbasic'        => 'QBasic',
			'qml'           => 'QML',
			'rsplus'        => 'R',
			'racket'        => 'Racket',
			'rails'         => 'Rails',
			'rbs'           => 'RBScript',
			'rebol'         => 'REBOL',
			'reg'           => 'REG',
			'rexx'          => 'Rexx',
			'robots'        => 'Robots',
			'rpmspec'       => 'RPM Spec',
			'ruby'          => 'Ruby',
			'gnuplot'       => 'Ruby Gnuplot',
			'rust'          => 'Rust',
			'sas'           => 'SAS',
			'scala'         => 'Scala',
			'scheme'        => 'Scheme',
			'scilab'        => 'Scilab',
			'scl'           => 'SCL',
			'sdlbasic'      => 'SdlBasic',
			'smalltalk'     => 'Smalltalk',
			'smarty'        => 'Smarty',
			'spark'         => 'SPARK',
			'sparql'        => 'SPARQL',
			'sqf'           => 'SQF',
			'sql'           => 'SQL',
			'standardml'    => 'StandardML',
			'stonescript'   => 'StoneScript',
			'sclang'        => 'SuperCollider',
			'swift'         => 'Swift',
			'systemverilog' => 'SystemVerilog',
			'tsql'          => 'T-SQL',
			'tcl'           => 'TCL',
			'teraterm'      => 'Tera Term',
			'thinbasic'     => 'thinBasic',
			'typoscript'    => 'TypoScript',
			'unicon'        => 'Unicon',
			'uscript'       => 'UnrealScript',
			'ups'           => 'UPC',
			'urbi'          => 'Urbi',
			'vala'          => 'Vala',
			'vbnet'         => 'VB.NET',
			'vbscript'      => 'VBScript',
			'vedit'         => 'Vedit',
			'verilog'       => 'VeriLog',
			'vhdl'          => 'VHDL',
			'vim'           => 'VIM',
			'visualprolog'  => 'Visual Pro Log',
			'vb'            => 'VisualBasic',
			'visualfoxpro'  => 'VisualFoxPro',
			'whitespace'    => 'WhiteSpace',
			'whois'         => 'WHOIS',
			'winbatch'      => 'Winbatch',
			'xbasic'        => 'XBasic',
			'xml'           => 'XML',
			'xorg_conf'     => 'Xorg Config',
			'xpp'           => 'XPP',
			'yaml'          => 'YAML',
			'z80'           => 'Z80 Assembler',
			'zxbasic'       => 'ZXBasic',
	);

	return $formats;
}

/**
 * Get the list of all supported paste lifespans
 *
 * @sine 0.1.0
 * @return array
 */
function as_pastebin_get_available_lifespan() {

	$span = array(
			'N'   => esc_html__( 'Never', 'as-pastebin' ),
			'10M' => esc_html__( '10 Minutes', 'as-pastebin' ),
			'1H'  => esc_html__( '1 Hour', 'as-pastebin' ),
			'1D'  => esc_html__( '1 Day', 'as-pastebin' ),
			'1W'  => esc_html__( '1 Week', 'as-pastebin' ),
			'2W'  => esc_html__( '2 Weeks', 'as-pastebin' ),
			'1M'  => esc_html__( '1 Month', 'as-pastebin' ),
	);

	return $span;

}