<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* Attribute Factory Class. Every Entity does manage his attributes with that factory
*
*
* PHP versions 4 and 5
*
* Copyright notice
*
* (c) 2007 Sebastian Winterhalder <sebi@concastic.ch>
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
*
*/
## class removes harmful and/or malicious user input

require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_checkInput.php");


class tx_mailform_processInput
{
	## constants

	const max_allowed_word_lenght = 60;
	
	const short_lenght_left  = 10;
	const short_lenght_right = 10;
	
	private $root_path = "";
	
	private $TAGS;
	
	## initialization
	
	public function __construct($root_path = false)
	{
		if($root_path !== false)
		{
			$this->root_path = $root_path;
		}
	}
	
	## interface
	
	public function email($tmp)
	{
		# removes eventual malicious elements from email
		
		$tmp = strip_tags($tmp);
		
		$tmp = $this->make_query_safe($tmp);
		
		$tmp = $this->remove_whitespaces($tmp);
		$tmp = $this->replace_special_chars($tmp);
		
		return $tmp;
	}
	
	
	public function url($tmp)
	{
		# removes eventual malicious elements from url
		
		$tmp = strip_tags($tmp);
		
		$tmp = $this->make_query_safe($tmp);

		$tmp = preg_replace("#javascript:#i", "javascript&#58;", $tmp);
		
		return $tmp;
	}
	
	
	public function shredder_email($tmp)
	{
		# Remove @
		# Remove dot
		# Return email link with shredderd text
		
		# Is valid e-mail
		
		$CI = new CHECK_INPUT();
		
		if(!$CI->is_valid_email($tmp))
			return array(0,$tmp);
			
		$tmp = $this->email($tmp,80);
		$split = split("@", $tmp);
		$rand = rand(10,99);

			
		$result[0] = "
		<script language=\"javascript\">
		function mail_".$rand."(var1, var2)
		{
			window.location.href = 'mailto:' + var1 + '@' + var2;
		}
		</script>
		";
		
		$dom = str_replace(".", " dot ", $split[1]);
		$result[1] = '<a href="javascript: mail_'.$rand.'(\''.$split[0].'\', \''.$split[1].'\')">'.$split[0].' at '.$dom.'</a>';
		return $result;
	}
	
	
	
	public function digits_only($tmp, $max_lenght = 60)
	{
		# removes all non-digits
		
		$tmp = $this->ensure_not_over_max_lenght($tmp, $max_lenght);
		
		$tmp = preg_replace("#([^\d]+)#", "", $tmp);

		return $tmp;
	}
	
	public function accountname_reduced_html($tmp, $max_length = 60)
	{
		$tmp = $this->accountname($tmp, $max_length);
		
		$tmp = $this->text_with_reduced_html($tmp, $max_length);
		
		$tmp = $this->make_query_safe($tmp);
		
		return $tmp;
	}
	
	public function accountname($tmp, $max_lenght = 60)
	{
		# removes all invalid elements for a nickname
		
		$tmp = strip_tags($tmp);
		
		$tmp = $this->ensure_not_over_max_lenght($tmp, $max_lenght);
		
		// replaces whitespaces with "_"
		$tmp = preg_replace("#[\s]{1,}#", "_", $tmp);
		// remove all invalid characters
		$tmp = preg_replace("#[^\d\w\._-]{1,}#", "", $tmp);
		
		$tmp = $this->replace_special_chars($tmp);
		
		$tmp = $this->make_query_safe($tmp);

		return $tmp;
	}
	
	public function shorten_filename_without_extension($filename, $max_lenght = 25)
	{
		$endrange = 3;
		
		$spaceholder = "...";
		
		if($max_lenght > $endrange)
		{
			$startrange = ($max_lenght - strlen($spaceholder)) - $endrange;
		}
		else
		{
			return "invalid_max_lenght_argument";
		}
	
		# do we have to shorten?
	
		if(strlen($filename) > $max_lenght)
		{
			$pattern = "#^([^ ]{".$startrange."})[^ ]{1,}([^ ]{".$endrange."})$#";
	
			$replace_pattern = "\\1...\\2";
	
			$filename = preg_replace($pattern, $replace_pattern, $filename);
		}
	
		return $filename;
	}
	
	public function short_without_html($tmp, $max_lenght = 60)
	{
		# removes all html-tags for short input values like names
		
		$tmp = strip_tags($tmp);
		
		$tmp = $this->ensure_not_over_max_lenght($tmp, $max_lenght);
		
		$tmp = $this->remove_whitespaces($tmp);
		$tmp = $this->replace_special_chars($tmp);
		
		$tmp = $this->make_query_safe($tmp);

		return $tmp;
	}
	
	public function text_with_reduced_html($tmp, $max_lenght = 2000)
	{
		# removes all html-tags except <b><i><br><p>
		# for text with most basic user-formatting
		
		$tmp = strip_tags($tmp, "<b><i><br><p>");
		
		//$tmp = $this->ensure_not_over_max_lenght($tmp, $max_lenght);
		
		$tmp = $this->nl_to_br($tmp);
		$tmp = $this->replace_special_chars($tmp);
		$tmp = $this->tab_to_spaces($tmp);
		$tmp = $this->remove_more_than_2_br($tmp);
		//$tmp = $this->shorten_too_long_words($tmp);
		
		$tmp = $this->make_query_safe($tmp);
		
		return $tmp;
	}
	
	public function text_with_html($tmp, $max_lenght = 20000)
	{
		# for text with full html support
		
		//$tmp = $this->ensure_not_over_max_lenght($tmp, $max_lenght);
		
		//$tmp = $this->shorten_too_long_words($tmp);
		
		$tmp = $this->make_query_safe($tmp);
		
		return $tmp;
	}
	
	/*
	public function text_with_tags_and_html($tmp, $max_lenght = 20000)
	{
		# for text with full html and tag support
		
		//$tmp = $this->ensure_not_over_max_lenght($tmp, $max_lenght);
		
		$tmp = $this->TAGS->tag_to_html($tmp);
		
		//$tmp = $this->shorten_too_long_words($tmp);
		
		$tmp = $this->make_query_safe($tmp);
		
		return $tmp;
	}
	*/
	/*public function text_with_tags_only($tmp, $max_lenght = 20000)
	{
		# for text with only tag support
		
		$tmp = strip_tags($tmp);
		
		//$tmp = $this->ensure_not_over_max_lenght($tmp, $max_lenght);
		
		$tmp = $this->TAGS->tag_to_html($tmp);
		
		//$tmp = $this->shorten_too_long_words($tmp);
		
		$tmp = $this->make_query_safe($tmp);
		
		return $tmp;
	}
	*/
	
	public function email_as_link($email)
	{
		return "<a href=\"mailto:".$email."\">".$email."</a>";
	}
	
	public function make_query_safe($data)
	{
		$data = str_replace("'", "\'", $data);
		
		return $data;
	}
	
	## implementation
	
	private function nl_to_br($tmp)
	{
		$tmp = preg_replace("#<br>|<br\s*\/>#", "\n"   , $tmp);
		$tmp = preg_replace("#<p[^<>]*>#"     , "\n\n" , $tmp);
		
		$tmp = preg_replace("#\n#"            , "<br>" , $tmp);
		$tmp = preg_replace("#\r#"            , ""     , $tmp);
		
		return $tmp;
	}
	
	private function remove_whitespaces($tmp)
	{
		$tmp = preg_replace("#\n#", ""                  , $tmp);
		$tmp = preg_replace("#\r#", ""                  , $tmp);
		$tmp = preg_replace("#\t#", "&#160;&#160;&#160;", $tmp);
		
		return $tmp;
	}
	
	private function remove_more_than_2_br($tmp)
	{
		$tmp = preg_replace("#(?:<br>){3,}#i", "<br><br>", $tmp);
		
		return $tmp;
	}
	
	private function replace_special_chars($tmp)
	{
		$tmp = str_replace( "&"     , "&#38;" , $tmp);
		$tmp = str_replace( "\""    , "&#34;" , $tmp);
		$tmp = preg_replace("#\|#"  , "&#124;", $tmp);
		$tmp = preg_replace("#\\\$#", "&#036;", $tmp);
		$tmp = str_replace( "!"     , "&#33;" , $tmp);
		$tmp = str_replace( "'"     , "&#39;" , $tmp);
		$tmp = stripslashes($tmp);
		$tmp = preg_replace("#\\\#" , "&#092;", $tmp);
		
		return $tmp;
	}
	
	private function tab_to_spaces($tmp)
	{
		$tmp = preg_replace("#\t#", "&#160;&#160;&#160;", $tmp);
		
		return $tmp;
	}
	
	private function shorten_too_long_words($tmp)
	{
		# shorten words which are longer than max_allowed_word_lenght
		
		# WARNING: can cause problems used with html-text!
		
		$max_middle = self::max_allowed_word_lenght - (self::short_lenght_left + self::short_lenght_right);
		
		$pattern = "#([^ ]{".self::short_lenght_left."})[^ ]{".$max_middle.",}([^ ]{".self::short_lenght_left."})#";
		
		$replace_pattern = "\\1...\\2";
		
		$tmp = preg_replace($pattern, $replace_pattern, $tmp);
		
		return $tmp;
	}
	
	private function ensure_not_over_max_lenght($tmp, $max_lenght)
	{
		# only take first $max_lenght chars, replace rest with '...'
		
		# WARNING: can cause problems used with html-text!
		
		$tmp = preg_replace("#^(.{{$max_lenght}})(.+?)$#s", "\\1...", $tmp);
		
		return $tmp;
	}
	
	public function ensure_not_over_max_lenght_rem_before($tmp, $max_lenght)
	{
		# only take first $max_lenght chars, replace rest with '...'
		
		# WARNING: can cause problems used with html-text!
		
		$tmp = preg_replace("#^.+?(.{{$max_lenght}})$#s", "...\\1", $tmp);
		
		return $tmp;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_processInput.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_processInput.php']);
}

?>