<?php
// ===========================================================================================
//
// File: CWYSIWYGEditor_Plain.php
//
// Description: Class CWYSIWYGEditor_Plain
//
// A plain textarea editor, just for showing how to implement the abstract base class 
// for WYSIWYGEditor.
//
// Author: Mikael Roos, mos@bth.se
//


class CWYSIWYGEditor_Plain extends CWYSIWYGEditor {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct($aTextareaId='', $aTextareaClass='', $aSubmitId='', $aSubmitClass='') {
		parent::__construct($aTextareaId, $aTextareaClass, $aSubmitId, $aSubmitClass);
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }

	

} // End of Of Class


?>