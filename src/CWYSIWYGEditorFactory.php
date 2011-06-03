<?php
// ===========================================================================================
//
// File: CWYSIWYGEditorFactory.php
//
// Description: Class CWYSIWYGEditorFactory
//
// Implements factory method pattern to create an instance a subclasses from CWYSIWYGEditor.
// http://en.wikipedia.org/wiki/Factory_method_pattern
//
// This minimizes code in the pagecontroller that have flexible definition of
// JavaScript editors replacing a <textarea> tag.
//
// Author: Mikael Roos, mos@bth.se
//


class CWYSIWYGEditorFactory {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() { ; }
	

	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }


	// ------------------------------------------------------------------------------------
	//
	// Factory method to create objects of subclasses of CWYSIWYGEditor.
	//
	public function CreateObject($aType = 'plain') { 
	
		$jseditor_submit = "";

		switch($aType) {

			case 'markItUp': 	{ return new CWYSIWYGEditor_markItUp('text', 'text'); } break;

			case 'NicEdit':		{ return new CWYSIWYGEditor_NicEdit('text', 'size98percentx300'); } break;

			case 'WYMeditor': { return new CWYSIWYGEditor_WYMeditor('text', 'text', '', 'wymupdate');	} break;

			case 'plain': 
			default: { 					return new CWYSIWYGEditor_Plain('text', 'size98percentx300'); } break;
		}
	}
	

} // End of Of Class

?>