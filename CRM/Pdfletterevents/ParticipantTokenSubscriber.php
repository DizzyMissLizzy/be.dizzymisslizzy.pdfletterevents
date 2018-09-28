<?php

use Civi\Token\TokenRow;

final class CRM_Pdfletterevents_ParticipantTokenSubscriber extends \Civi\Token\AbstractTokenSubscriber
{
  /**
   * @var CRM_Pdfletterevents_ParticipantTokenSet
   */
  private $tokenProvider;

  public function __construct(CRM_Pdfletterevents_ParticipantTokenSet $tokenProvider)
  {
    $this->tokenProvider = $tokenProvider;
    parent::__construct(
      'participant',
      $tokenProvider->getParticipantTokenNames()
    );
  }

  /**
   * Evaluate the content of a single token.
   *
   * @param TokenRow $row
   *   The record for which we want token values.
   * @param string $entity
   *   The name of the token entity.
   * @param string $field
   *   The name of the token field.
   * @param mixed $prefetch
   *   Any data that was returned by the prefetch().
   */
  public function evaluateToken(TokenRow $row, $entity, $field, $prefetch = NULL)
  {
    // Convert TokenRow to easier interfaces.
    try {
      $participantIds = CRM_Pdfletterevents_TokenRowParticipantIds::fromTokenRow($row);
    }
    catch (CRM_Pdfletterevents_UnknownParticipant $ex) {
      // Let's ignore the token if the participant was not found.
      return;
    }

    $tokenValueSetter = new CRM_Pdfletterevents_TokenRowParticipantTokenValueSetter($row);

    $this->updateToken($tokenValueSetter, $participantIds, $field);
  }

  /**
   * Retrieves the token value, and stores the result.
   *
   * No value will be stored if the participant is not found.
   *
   * @param CRM_Pdfletterevents_ParticipantTokenValueSetter $tokenValueSetter
   * @param CRM_Pdfletterevents_ParticipantIds $participantIds
   * @param string $field
   */
  public function updateToken(CRM_Pdfletterevents_ParticipantTokenValueSetter $tokenValueSetter, CRM_Pdfletterevents_ParticipantIds $participantIds, $field)
  {
    // Performance can be improved, but for now I just want the code to be clean.
    try {
      $result = civicrm_api3('Participant', 'getsingle', [
        'id' => $participantIds->getParticipantId(),
        'return' => [$field],
      ]);
    }
    catch (CiviCRM_API3_Exception $ex) {
      return;
    }

    $tokenValueSetter->setValue($field, $result[$field]);
  }
}
