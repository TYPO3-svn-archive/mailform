<?php
		
		// Methods called when in TYPO3 Backend Modus
		if(TYPO3_MODE == "BE") {
		}
		
		$this->ensureStatus("be"); // Function only in BE allowed
			$this->fields[$_POST['mailform_fieldConf_page']][$_POST['mailform_fieldConf_row']][$_POST['mailform_fieldConf_col']]->setConditionActivated($_POST['mailform_fieldConf_activateCondition']);
			$this->saveFields();
		$this->ensureStatus("be"); // Function only in BE allowed
		
		$this->ensureStatus("be"); // Function only in BE allowed
		
		$this->ensureStatus("be"); // Function only in BE allowed
		
		$this->ensureStatus("be"); // Function only in BE allowed
		
		
		foreach($this->fields[$this->currentPage] as $row) {
		$this->ensureStatus("be"); // Function only in BE allowed
		
		$this->ensureStatus("be"); // Function only in BE allowed
		
		
		
		$this->ensureStatus("be"); // Function only in BE allowed
		
		$this->ensureStatus("be"); // Function only in BE allowed
	 * addPage(Char, Integer)
	 *
	 * @param Char $direction
	 * @param Integer $currentPage
	 */	 	
		
			
		
	/**
	 * removePage($page)
	 *
	 * @param Integer $page
	 */
		$this->ensureStatus("be"); // Function only in BE allowed
	 * getPageCount()
	 *
	 * @return unknown
	 */	 	
		$this->ensureStatus("be"); // Function only in BE allowed
	
	/**
	 * Function ensures that in ARG given state is running when calling this function
	 *
	 * @param String $allowed_status
	 */
	private function ensureStatus($allowed_status = "BE") {
		if(strtoupper($allowed_status) != TYPO3_MODE)
			throw new Exception("Current Typo3 Mode (".TYPO3_MODE.") is not allowed.");
	}
	
	/**
	 * To String
	 *
	 * @return String
	 */
	public function __toString() {
		return "class.tableFieldHandler";
	}