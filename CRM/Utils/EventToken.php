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
  public static function replaceEntityEventTokens($entity, $participantArray, $eventArray, $str, $knownTokens = array(), $escapeSmarty = FALSE) {

    if (!$knownTokens || !CRM_Utils_Array::value($entity, $knownTokens)) {
      return $str;
    }

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

    /**
   * Get replacement strings for any membership tokens (only a small number of tokens are implemnted in the first instance
   * - this is used by the pdfLetter task from membership search
   * @param string $token
   * @param array $membership an api result array for a single membership
   * @param boolean $escapeSmarty
   * @return string token replacement
   */

  public static function getEventTokenReplacement($token, $participant, $event, $escapeSmarty = FALSE) {
    $entity = 'event';
    self::_buildEventTokens();
      $params = array('entity_id' => $participant['event_id'], 'entity_table' => 'civicrm_event');
      $location = CRM_Core_BAO_Location::getValues($params, TRUE);

   switch ($token) {
     case 'event_id':
     case 'event_type':
       $value =  $participant[$token];
       break;
     case 'title' :
       $value = $participant['event_title'];
       break;
     case 'summary':
     case 'description':
     case 'start_date':
     case 'end_date':
        $value = $event[$participant['event_id']][$token];
       break;
     case 'location':
      foreach($location['address'] as $address) {
        $value = $address['display_text'];
      }
       break;
     case 'contact_email':
     foreach($location['email'] as $email) {
        $value .= $email['email'] ."<br/>";
      }
      break;
     case 'contact_phone' :
     foreach($location['phone'] as $phone) {
        $value .= $phone['phone'] ."<br/>";
      }
       break;
    case 'info_url':
       $value = CIVICRM_UF_BASEURL . 'civicrm/event/info?reset=1&id=' . $participant['event_id'];
       break;
    case 'registration_url':
       $value = CIVICRM_UF_BASEURL . 'civicrm/event/register?reset=1&id=' . $participant['event_id'];
       break;
    case 'fee_amount':
       $priceSetId = CRM_Price_BAO_PriceSet::getFor('civicrm_event', $participant['event_id']);
       $result = civicrm_api3('PriceFieldValue', 'get', array('price_field_id' => $priceSetId));
       $config = CRM_Core_Config::singleton();
       $currency = $config->defaultCurrency;
       foreach($result['values'] as $pricefield) {
        $value .= $pricefield['label'] . ": " . $pricefield['amount'] . " " . $currency . "<br>";
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
