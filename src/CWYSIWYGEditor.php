<?php
// ===========================================================================================
//
// File: CWYSIWYGEditor.php
//
// Description: Class CWYSIWYGEditor
//
// Abstract base class WYSIWYG JavaScript editors as a replacement for <textarea>. 
// Each specific editor must inherit this class and implement its methods.
//
// Examples on usage of classes in a pagecontroller:
//  http://dev.phpersia.org/persia/?p=article-edit-all
//
// Why is the class abstract, it does not contain any abstract methods...?
// No good explanation on that. At first it contained abstract methods, but then I saw that 
// I can mix abstract and no-abstract methods and I did not find a true abstract method.
// I still want to have the class as abstract, I might change it when I see how the classes
// evolve.
//
// Author: Mikael Roos, mos@bth.se
//

abstract class CWYSIWYGEditor {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected $iTextareaId;
	protected $iTextareaClass;
	protected $iSubmitId;
	protected $iSubmitClass;
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct($aTextareaId='', $aTextareaClass='', $aSubmitId='', $aSubmitClass='') {
		$this->iTextareaId		= $aTextareaId; 
		$this->iTextareaClass = $aTextareaClass; 
		$this->iSubmitId			= $aSubmitId; 
		$this->iSubmitClass		= $aSubmitClass; 
	}


	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }


	// ------------------------------------------------------------------------------------
	//
	// Does this editor need the jQuery JavaScript library?
	// Subclasses who does, should reimplement this method and return TRUE.
	//
	public function DependsOnjQuery() {
		return FALSE;
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Return the HTML header for the editor, usually stylesheet, js-file and javascript 
	// code to instantiate editor.
	// Subclasses should change this if they need separate stylesheets and javascript 
	// sources.
	//
	public function GetHTMLHead() { 
		return '';
	}


	// ------------------------------------------------------------------------------------
	//
	// Return the id and class attributes, if set, specific for this editor and
	// the textarea.
	// Could be used by all subclasses, or be overridden.
	//
	public function GetTextareaSettings() {

		$id 		= (empty($this->iTextareaId)) 		? '' : "id='{$this->iTextareaId}' "; 
		$class 	= (empty($this->iTextareaClass)) 	? '' : "class='{$this->iTextareaClass}' "; 

		return "{$id}{$class}"; 
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Return the id and class attributes, if set, specific for this editor and
	// the submit button.
	// Could be used by all subclasses, or be overridden.
	//
	public function GetSubmitSettings() { 

		$id 		= (empty($this->iSubmitId)) 		? '' : "id='{$this->iSubmitId}' "; 
		$class 	= (empty($this->iSubmitClass)) 	? '' : "class='{$this->iSubmitClass}' "; 

		return "{$id}{$class}"; 
	}


} // End of Of Class

?>