<?php

use Civi\Token\TokenRow;

class CRM_Pdfletterevents_Tokens extends \Civi\Token\AbstractTokenSubscriber
{
  public function __construct()
  {
    parent::__construct('participant', ['test' => ts('test')]);
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
   * @return mixed
   */
  public function evaluateToken(TokenRow $row, $entity, $field, $prefetch = NULL)
  {
    // If you use the token {participant.test}, this will die :-)
    die('This is going to work!');
  }
}
