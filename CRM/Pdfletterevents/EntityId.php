<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

/**
 * Interface for access to participant related ID's.
 */
interface CRM_Pdfletterevents_EntityId
{
  /**
   * @return int
   */
  public function getValue();
}
