<?php

/**
 * This class provides the common functionality for creating PDF letter for
 * members
 */
class CRM_PdfLetterEvents_Form_Task_PDFLetterCommon extends CRM_Contact_Form_Task_PDFLetterCommon {

  /**
   * process the form after the input has been submitted and validated.
   *
   * @todo this is horrible copy & paste code because there is so much risk of breakage
   * in fixing the existing pdfLetter classes to be suitably generic
   * @access public
   *
   * @param CRM_Core_Form $form
   * @param int[] $participantIDs
   * @param bool $skipOnHold
   * @param bool $skipDeceased
   * @param int[] $contactIDs
   * @return void
   * @throws CRM_Core_Exception
   */
  static function postProcessEvents(
    CRM_Core_Form &$form,
    array $participantIDs,
    $skipOnHold,
    $skipDeceased,
    array $contactIDs
  ) {
    $formValues = $form->controller->exportValues($form->getName());
    list($formValues, $categories, $html_message, $messageToken, $returnProperties) = self::processMessageTemplate($formValues);

    $html = self::generateHTML($participantIDs, $returnProperties, $skipOnHold, $skipDeceased, $messageToken, $html_message, $categories);
    self::createActivities(
      $form,
      $html_message,
      $contactIDs,
      $formValues['subject'],
      CRM_Utils_Array::value('campaign_id', $formValues)
    );

    CRM_Utils_PDF_Utils::html2pdf($html, "CiviLetter.pdf", FALSE, $formValues);

    $form->postProcessHook();

    CRM_Utils_System::civiExit(1);
  }

  /**
   * generate html for pdf letters.
   *
   * @param int[] $participantIDs
   * @param int[] $returnProperties
   * @param bool $skipOnHold
   * @param bool $skipDeceased
   * @param array $messageToken
   * @param string $html_message
   * @param string[] $categories
   * @return string[]
   */
  static function generateHTML(array $participantIDs, array $returnProperties, $skipOnHold, $skipDeceased, array $messageToken, $html_message, array $categories) {

    $participants = CRM_Utils_EventToken::getParticipantTokenDetails($participantIDs);
    $html = [];

    foreach ($participantIDs as $participantID) {

      $participant = $participants[$participantID];
      $eventId = $participant['event_id'];
      $event = CRM_Utils_EventToken::getEventTokenDetails($eventId);

      // get contact information
      $contactId = $participant['contact_id'];
      $params = array('contact_id' => $contactId);

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
      $tokenHtml = CRM_Utils_EventToken::replaceEntityTokens('event', $event, $tokenHtml, $messageToken);
      $tokenHtml = CRM_Utils_EventToken::replaceEntityTokens('participant', $participant, $tokenHtml, $messageToken);
      $tokenHtml = CRM_Utils_Token::replaceHookTokens($tokenHtml, $contacts[$contactId], $categories, TRUE);

      $html[] = $tokenHtml;

    }

    return $html;
  }
}

