<?php

/**
 * This class provides the common functionality for creating PDF letter for
 * members
 */
class CRM_PdfLetterEvents_Form_Task_PDFLetterCommon extends CRM_Contact_Form_Task_PDFLetterCommon {

  /**
   * process the form after the input has been submitted and validated
   * @todo this is horrible copy & paste code because there is so much risk of breakage
   * in fixing the existing pdfLetter classes to be suitably generic
   * @access public
   *
   * @return None
   */
  static function postProcessEvents(&$form, $participantIDs, $skipOnHold, $skipDeceased, $contactIDs) {

    list($formValues, $categories, $html_message, $messageToken, $returnProperties) = self::processMessageTemplate($form);

    $html = self::generateHTML($participantIDs, $returnProperties, $skipOnHold, $skipDeceased, $messageToken, $html_message, $categories);
    self::createActivities($form, $html_message, $contactIDs);

    CRM_Utils_PDF_Utils::html2pdf($html, "CiviLetter.pdf", FALSE, $formValues);

    $form->postProcessHook();

    CRM_Utils_System::civiExit(1);
  }
  //end of function

  /**
   * generate htmlfor pdf letters
   * @param unknown_type $membershipIDs
   * @param unknown_type $returnProperties
   * @param unknown_type $skipOnHold
   * @param unknown_type $skipDeceased
   * @param unknown_type $messageToken
   * @return unknown
   */
  static function generateHTML($participantIDs, $returnProperties, $skipOnHold, $skipDeceased, $messageToken, $html_message, $categories) {
    $participants = CRM_Utils_EventToken::getParticipantTokenDetails($participantIDs);

    foreach ($participantIDs as $participantID) {

      $participant = $participants[$participantID];
      $eventid = $participant['event_id'];
      $event = CRM_Utils_EventToken::getEventTokenDetails($eventid);

      // get contact information
      $contactId = $participant['contact_id'];
      $params = array('contact_id' => $contactId);
      //getTokenDetails is much like calling the api contact.get function - but - with some minor
      // special handlings. It preceeds the existance of the api
      list($contacts) = CRM_Utils_Token::getTokenDetails(
        $params,
        $returnProperties,
        $skipOnHold,
        $skipDeceased,
        NULL,
        $messageToken,
        'CRM_Contribution_Form_Task_PDFLetterCommon'
      );

      $tokenHtml = CRM_Utils_Token::replaceContactTokens($html_message, $contacts[$contactId], TRUE, $messageToken);
      $tokenHtml = CRM_Utils_EventToken::replaceEntityEventTokens('event', $participant, $event, $tokenHtml, $messageToken);
      $tokenHtml = CRM_Utils_Token::replaceHookTokens($tokenHtml, $contacts[$contactId], $categories, TRUE);

      $html[] = $tokenHtml;

    }

    return $html;
  }
}

