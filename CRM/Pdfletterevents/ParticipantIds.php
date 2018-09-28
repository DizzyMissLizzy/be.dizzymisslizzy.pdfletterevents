<?php

/**
 * Interface for access to participant related ID's.
 */
interface CRM_Pdfletterevents_ParticipantIds
{
  /**
   * @return int
   */
  public function getContactId();

  /**
   * @return int
   */
  public function getEventId();

  /**
   * @return int
   */
  public function getParticipantId();
}
