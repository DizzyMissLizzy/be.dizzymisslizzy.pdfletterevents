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
  return $event['values'][$eventID];

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
  public static function replaceEntityTokens($entity, $entityArray, $str, $knownTokens = array(), $escapeSmarty = FALSE) {

    if (!$knownTokens || !CRM_Utils_Array::value($entity, $knownTokens)) {
      return $str;
    }

    $fn = 'get' . ucFirst($entity) . 'tokenReplacement';
    //since we already know the tokens lets just use them & do str_replace which is faster & simpler than preg_replace
    foreach ($knownTokens[$entity] as $token) {
      $replaceMent = CRM_Utils_EventToken::$fn($token,  $entityArray, $escapeSmarty);
      $str = str_replace('{' . $entity . '.' . $token . '}', $replaceMent, $str);
    }
    $str = preg_replace('/\\\\|\{(\s*)?\}/', ' ', $str);
    return $str;
  }


  /**
   * store event tokens on the static _tokens array
   */

  protected static function _buildEventTokens() {
    $key = 'event';
    if (!isset(self::$_tokens[$key]) || self::$_tokens[$key] == NULL) {
      $eventTokens = array();
      $tokens = CRM_Core_SelectValues::eventTokens();
      foreach ($tokens as $token => $dontCare) {
        $eventTokens[] = substr($token, (strpos($token, '.') + 1), -1);
      }
      $customtokens = CRM_CORE_BAO_CustomField::getFields('Event');
      foreach ($customtokens as $tokenkey => $tokenvalue) {
        $eventTokens[] = 'custom_' . $tokenkey;
    }
      self::$_tokens[$key] = $eventTokens;
    }

  }

    /**
   * Get replacement strings for any event tokens (only a small number of tokens are implemnted in the first instance
   * - this is used by the pdfLetter task from zvznt search
   * @param string $token
   * @param array $event an api result array for a single event
   * @param boolean $escapeSmarty
   * @return string token replacement
   */

  public static function getEventTokenReplacement($token, $event, $escapeSmarty = FALSE) {
    $entity = 'event';
    self::_buildEventTokens();
      $params = array('entity_id' => $event['id'], 'entity_table' => 'civicrm_event');
      $location = CRM_Core_BAO_Location::getValues($params, TRUE);

   switch ($token) {
     case 'title' :
       $value = $event['event_title'];
       break;
     case 'type':
     $params = array(
  'option_group_id' => 14,
  'value' => $event['event_type_id'],
  'return' => 'label',
);
        $value = civicrm_api3('OptionValue', 'getvalue', $params);
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
       $value = CIVICRM_UF_BASEURL . 'civicrm/event/info?reset=1&id=' . $event['id'];
       break;
    case 'registration_url':
       $value = CIVICRM_UF_BASEURL . 'civicrm/event/register?reset=1&id=' . $event['id'];
       break;
    case 'fee_amount':
       $priceSetId = CRM_Price_BAO_PriceSet::getFor('civicrm_event', $event['id']);
       $result = civicrm_api3('PriceFieldValue', 'get', array('price_field_id' => $priceSetId));
       $config = CRM_Core_Config::singleton();
       $currency = $config->defaultCurrency;
       foreach($result['values'] as $pricefield) {
        $value .= $pricefield['label'] . ": " . $pricefield['amount'] . " " . $currency . "<br>";
       }
       break;
     default:
     if (in_array($token, self::$_tokens[$entity])) {
         $value = $event[$token];
       }
       else {
       $value = "{$entity}.{$token}";}
       break;
    }

    if ($escapeSmarty) {
      $value = self::tokenEscapeSmarty($value);
    }
    return $value;
  }

  /**
   * store participant tokens on the static _tokens array
   */

  protected static function _buildParticipantTokens() {
    $key = 'participant';
    if (!isset(self::$_tokens[$key]) || self::$_tokens[$key] == NULL) {
      $participantTokens = array();
      $tokens = CRM_Core_SelectValues::participantTokens();
      foreach ($tokens as $token => $dontCare) {
        $participantTokens[] = substr($token, (strpos($token, '.') + 1), -1);
      }

      self::$_tokens[$key] = $participantTokens;
    }

  }


    /**
   * Get replacement strings for any participant tokens (only a small number of tokens are implemnted in the first instance
   * - this is used by the pdfLetter task from participant search
   * @param string $token
   * @param array $participant an api result array for a single participant
   * @param boolean $escapeSmarty
   * @return string token replacement
   */

  public static function getParticipantTokenReplacement($token, $participant, $escapeSmarty = FALSE) {
    $entity = 'participant';
    self::_buildParticipantTokens();

   switch ($token) {
     case 'participant_fee_level':
       $value =  $participant[$token][0];
       break;
     case 'participant_role':
        $value = CRM_Event_PseudoConstant::participantRole($participant['participant_role_id']);
       break;
       default:
     if (in_array($token, self::$_tokens[$entity])) {
         $value = $participant[$token];
       }
       else {
       $value = "{$entity}.{$token}";}
       break;
    }

    if ($escapeSmarty) {
      $value = self::tokenEscapeSmarty($value);
    }

    return $value;
  }

}

