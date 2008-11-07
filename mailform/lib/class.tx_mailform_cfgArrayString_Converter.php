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
class tx_mailform_cfgArrayString_Converter {

                /**
                * Converts the input array to a configuration code string
                *
                * @param        array                Array of form configuration (follows the input structure from the form wizard POST form)
                * @return        string                The array converted into a string with line-based configuration.
                * @see cfgString2CfgArray()
                */
                function cfgArray2CfgString($cfgArr) {

                        // Initialize:
                        $inLines = array();

                        // Traverse the elements of the form wizard and transform the settings into configuration code.
                        foreach($cfgArr as $vv) {
                                if ($vv['comment']) {
                                        // If "content" is found, then just pass it over.
                                        $inLines[] = trim($vv['comment']);
                                } else {
                                        // Begin to put together the single-line configuration code of this field:

                                        // Reset:
                                        $thisLine = array();

                                        // Set Label:
                                        $thisLine[0] = str_replace('|', '', $vv['label']);

                                        // Set Type:
                                        if ($vv['type']) {
                                                $thisLine[1] = ($vv['required']?'*':'').str_replace(',', '', ($vv['fieldname']?$vv['fieldname'].'=':'').$vv['type']);

                                                // Default:
                                                $tArr = array('', '', '', '', '', '');
                                                switch((string)$vv['type']) {
                                                        case 'textarea':
                                                        if (intval($vv['cols'])) $tArr[0] = intval($vv['cols']);
                                                                if (intval($vv['rows'])) $tArr[1] = intval($vv['rows']);
                                                                if (trim($vv['extra'])) $tArr[2] = trim($vv['extra']);
                                                                if (strlen($vv['specialEval'])) {
                                                                $thisLine[2] = '';
                                                                // Preset blank default value so position 3 can get a value...
                                                                $thisLine[3] = $vv['specialEval'];
                                                        }
                                                        break;
                                                        case 'input':
                                                        case 'password':
                                                        if (intval($vv['size'])) $tArr[0] = intval($vv['size']);
                                                                if (intval($vv['max'])) $tArr[1] = intval($vv['max']);
                                                                if (strlen($vv['specialEval'])) {
                                                                $thisLine[2] = '';
                                                                // Preset blank default value so position 3 can get a value...
                                                                $thisLine[3] = $vv['specialEval'];
                                                        }
                                                        break;
                                                        case 'file':
                                                        if (intval($vv['size'])) $tArr[0] = intval($vv['size']);
                                                                break;
                                                        case 'select':
                                                        if (intval($vv['size'])) $tArr[0] = intval($vv['size']);
                                                                if ($vv['autosize']) $tArr[0] = 'auto';
                                                        if ($vv['multiple']) $tArr[1] = 'm';

                                                }
                                                $tArr = $this->cleanT($tArr);
                                                if (count($tArr)) $thisLine[1] .= ','.implode(',', $tArr);

                                                $thisLine[1] = str_replace('|', '', $thisLine[1]);

                                                // Default:
                                                if ($vv['type'] == 'select' || $vv['type'] == 'radio') {
                                                        $thisLine[2] = str_replace(chr(10), ', ', str_replace(',', '', $vv['options']));
                                                } elseif ($vv['type'] == 'checkbox') {
                                                        if ($vv['default']) $thisLine[2] = 1;
                                                } elseif (strcmp(trim($vv['default']), '')) {
                                                        $thisLine[2] = $vv['default'];
                                                }
                                                if (isset($thisLine[2])) $thisLine[2] = str_replace('|', '', $thisLine[2]);
                                                }

                                        // Compile the final line:
                                        $inLines[] = ereg_replace("[\n\r]*", '', implode(' | ', $thisLine));
                                }
                        }
                        // Finally, implode the lines into a string, and return it:
                        return implode(chr(10), $inLines);
                }

                /**
                * Converts the input configuration code string into an array
                *
                * @param        string                Configuration code
                * @return        array                Configuration array
                * @see cfgArray2CfgString()
                */
                function cfgString2CfgArray($cfgStr) {

                        // Traverse the number of form elements:
                        $tLines = explode(chr(10), $cfgStr);
                        foreach($tLines as $k => $v) {

                                // Initialize:
                                $confData = array();
                                $val = trim($v);

                                // Accept a line as configuration if a) it is blank(! - because blank lines indicates new, unconfigured fields) or b) it is NOT a comment.
                                if (!$val || strcspn($val, '#/')) {

                                        // Split:
                                        $parts = t3lib_div::trimExplode('|', $val);

                                        // Label:
                                        $confData['label'] = trim($parts[0]);

                                        // Field:
                                        $fParts = t3lib_div::trimExplode(',', $parts[1]);
                                        $fParts[0] = trim($fParts[0]);
                                        if (substr($fParts[0], 0, 1) == '*') {
                                                $confData['required'] = 1;
                                                $fParts[0] = substr($fParts[0], 1);
                                        }

                                        $typeParts = t3lib_div::trimExplode('=', $fParts[0]);
                                        $confData['type'] = trim(strtolower(end($typeParts)));

                                        if ($confData['type']) {
                                                if (count($typeParts) == 1) {
                                                        $confData['fieldname'] = substr(ereg_replace('[^a-zA-Z0-9_]', '', str_replace(' ', '_', trim($parts[0]))), 0, 30);

                                                        // Attachment names...
                                                        if ($confData['type'] == 'file') {
                                                                $confData['fieldname'] = 'attachment'.$attachmentCounter;
                                                                $attachmentCounter = intval($attachmentCounter)+1;
                                                        }
                                                } else {
                                                        $confData['fieldname'] = str_replace(' ', '_', trim($typeParts[0]));
                                                }

                                                switch((string)$confData['type']) {
                                                        case 'select':
                                                        case 'radio':
                                                        $confData['default'] = implode(chr(10), t3lib_div::trimExplode(',', $parts[2]));
                                                        break;
                                                        default:
                                                        $confData['default'] = trim($parts[2]);
                                                        break;
                                                }

                                                // Field configuration depending on the fields type:
                                                switch((string)$confData['type']) {
                                                        case 'textarea':
                                                        $confData['cols'] = $fParts[1];
                                                        $confData['rows'] = $fParts[2];
                                                        $confData['extra'] = strtoupper($fParts[3]) == 'OFF' ? 'OFF' :
                                                        '';
                                                        $confData['specialEval'] = trim($parts[3]);
                                                        break;
                                                        case 'input':
                                                        case 'password':
                                                        $confData['size'] = $fParts[1];
                                                        $confData['max'] = $fParts[2];
                                                        $confData['specialEval'] = trim($parts[3]);
                                                        break;
                                                        case 'file':
                                                        $confData['size'] = $fParts[1];
                                                        break;
                                                        case 'select':
                                                        $confData['size'] = intval($fParts[1])?$fParts[1]:
                                                        '';
                                                        $confData['autosize'] = strtolower(trim($fParts[1])) == 'auto' ? 1 :
                                                        0;
                                                        $confData['multiple'] = strtolower(trim($fParts[2])) == 'm' ? 1 :
                                                        0;
                                                        break;
                                                }
                                        }
                                } else {
                                        // No configuration, only a comment:
                                        $confData = array(
                                        'comment' => $val );
                                }

                                // Adding config array:
                                $cfgArr[] = $confData;
                        }

                        // Return cfgArr
                        return $cfgArr;
                }
                

                /**
                * Removes any "trailing elements" in the array which consists of whitespace (little like trim() does for strings, so this does for arrays)
                *
                * @param        array                Single dim array
                * @return        array                Processed array
                * @access private
                */
                function cleanT($tArr) {
                        for($a = count($tArr); $a > 0; $a--) {
                                if (strcmp($tArr[$a-1], '')) {
                                        break;
                                } else {
                                        unset($tArr[$a-1]);
                                }
                        }
                        return $tArr;
                }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_cfgArrayString_Converter.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_cfgArrayString_Converter.php']);
}
?>