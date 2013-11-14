<?php

class CRM_Utils_EventToken extends CRM_Utils_Token {


/**
   * Get Participant Token Details
   * @param array $participantIDs array of participant IDS
   */
  static function getParticipantTokenDetails($participantIDs) {

    foreach ($participantIDs as $participantid) {
        $result = civicrm_api3('participant', 'get', array('participant_id' => $participantid));
        $participants[$participantid] = $result['values'][$participantid];
      }
    return $participants;
  }

/**
   * Get Participant Token Details
   * @param array $participantIDs array of participant IDS
   */
  static function getEventTokenDetails($eventID) {

  $event = civicrm_api3('event', 'get', array('id' => $eventID));
  return $event['values'];

  }

/**
   * Replace tokens for an entity
   * @param string $entity
   * @param array $entityArray (e.g. in format from api)
   * @param string $str string to replace in
   * @param array $knownTokens array of tokens present
   * @param boolean $escapeSmarty
   * @return string string with replacements made
   */
  public static function replaceEntityTokens($entity, $participantArray, $eventArray, $str, $knownTokens = array(), $escapeSmarty = FALSE) {

    if (!$knownTokens || !CRM_Utils_Array::value($entity, $knownTokens)) {
      return $str;
    }

    //CRM_CORE_ERROR::debug('Entity', $knownTokens, $log = true, $html = true);


    $fn = 'get' . ucFirst($entity) . 'tokenReplacement';
    //since we already know the tokens lets just use them & do str_replace which is faster & simpler than preg_replace
    foreach ($knownTokens[$entity] as $token) {
      $replaceMent = CRM_Utils_EventToken::$fn($token, $participantArray, $eventArray, $escapeSmarty);
      $str = str_replace('{' . $entity . '.' . $token . '}', $replaceMent, $str);
    }
    $str = preg_replace('/\\\\|\{(\s*)?\}/', ' ', $str);
    return $str;
  }

  /**
   * Get replacement strings for any membership tokens (only a small number of tokens are implemnted in the first instance
   * - this is used by the pdfLetter task from membership search
   * @param string $token
   * @param array $membership an api result array for a single membership
   * @param boolean $escapeSmarty
   * @return string token replacement
   */
  /**
   * store membership tokens on the static _tokens array
   */

  protected static function _buildEventTokens() {
    $key = 'event';
    if (!isset(self::$_tokens[$key]) || self::$_tokens[$key] == NULL) {
      $eventTokens = array();
      $tokens = CRM_Core_SelectValues::eventTokens();
      foreach ($tokens as $token => $dontCare) {
        $eventTokens[] = substr($token, (strpos($token, '.') + 1), -1);
      }
      self::$_tokens[$key] = $eventTokens;
    }
  }

  public static function getEventTokenReplacement($token, $participant, $event, $escapeSmarty = FALSE) {
    $entity = 'event';
    self::_buildEventTokens();
    //CRM_CORE_ERROR::debug('Participant', $participant, $log = true, $html = true);
    //CRM_CORE_ERROR::debug('Event', $event, $log = true, $html = true);

   switch ($token) {
     case 'event_id':
     case 'start_date':
     case 'end_date':
     case 'event_type':
       $value =  $participant[$token];
       break;
     case 'title' :
       $value = $participant['event_title'];
       break;
     case 'summary':
     case 'description':
        $value = $event[$participant['event_id']][$token];
       break;
     case 'location':
       try{
        $street = civicrm_api3('Address', 'getvalue', array('id' => $event[$participant['event_id']]['loc_block_id'], 'return' => 'street_address'));
        $postcode = civicrm_api3('Address', 'getvalue', array('id' => $event[$participant['event_id']]['loc_block_id'], 'return' => 'postal_code'));
        $city = civicrm_api3('Address', 'getvalue', array('id' => $event[$participant['event_id']]['loc_block_id'], 'return' => 'postal_code'));

         $value = $street . ', ' . $postcode . ' ' . $city;
       }
       catch (CiviCRM_API3_Exception $e) {
         // we can anticipate we will get an error if the minimum fee is set to 'NULL' because of the way the
         // api handles NULL (4.4)
         $value = 0;
       }
       break;
     default:
       $value = "{$entity}.{$token}";
       break;
    }

    if ($escapeSmarty) {
      $value = self::tokenEscapeSmarty($value);
    }
    return $value;
  }

}
