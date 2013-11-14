<?php

class CRM_Utils_EventToken extends CRM_Utils_Token {


/**
   * Get Participant Token Details
   * @param array $participantIDs array of participant IDS
   */
  static function getParticipantTokenDetails($participantIDs) {
    $participants =  array();
    foreach ($participantIDs as $participantid) {
        $result = civicrm_api3('participant', 'get', array('participant_id' => $participantid));
        array_push($participants , $result['values'] );
      }
    return $participants;
  }

}
