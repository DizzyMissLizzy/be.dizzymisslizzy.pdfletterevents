<?php

use Civi\Token\Event\TokenValueEvent;

require_once 'pdfletterevents.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function pdfletterevents_civicrm_config(&$config) {
  _pdfletterevents_civix_civicrm_config($config);

  // I copied this trick with Civi::$statics from the documentation:
  // https://docs.civicrm.org/dev/en/latest/hooks/setup/symfony/
  if (isset(Civi::$statics[__FUNCTION__])) { return; }
  Civi::$statics[__FUNCTION__] = 1;

  // Not sure if I can configure dependency injection, so that I can
  // get the subscriber via the container.
  $subscriber = new CRM_Pdfletterevents_ParticipantTokenSubscriber(
    new CRM_Pdfletterevents_SelectValuesParticipantTokenSet()
  );

  Civi::dispatcher()->addListener(
    'civi.token.eval',
    // [new CRM_Pdfletterevents_Tokens(), 'evaluateTokens']
    function (TokenValueEvent $e) use ($subscriber) {
      $subscriber->evaluateTokens($e);
    }
  );

}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function pdfletterevents_civicrm_xmlMenu(&$files) {
  _pdfletterevents_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function pdfletterevents_civicrm_install() {
  return _pdfletterevents_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function pdfletterevents_civicrm_uninstall() {
  return _pdfletterevents_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function pdfletterevents_civicrm_enable() {
  return _pdfletterevents_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function pdfletterevents_civicrm_disable() {
  return _pdfletterevents_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function pdfletterevents_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _pdfletterevents_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function pdfletterevents_civicrm_managed(&$entities) {
  return _pdfletterevents_civix_civicrm_managed($entities);
}

function pdfletterevents_civicrm_searchTasks( $objectName, &$tasks ) {

    $task_found = false;
    if ($objectName == 'event') {
       $tmp_label  = 'PDF Letter for Participants'; 
       foreach($tasks as $task){
   	        	$tmp_cur_title = $task['title'];
   			if(strcmp( $tmp_cur_title , $tmp_label) == 0 ){
   				$task_found = true;
   				// extra task already exists, do not add it again. 
   			
   			}
   			 
   		}
   		
    		
   		 if( $task_found == false){
   		 
   		    $tasks[] = array(
		        'title'  =>  $tmp_label  ,
		        'class'  => 'CRM_Pdfletterevents_Form_Task_PDF',
		        'result' => FALSE,
		      );
   		 
   		 }
    
    
  }
}


