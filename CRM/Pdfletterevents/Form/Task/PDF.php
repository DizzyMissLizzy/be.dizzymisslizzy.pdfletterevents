<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */

/**
 * This class provides the functionality to create PDF letter for a group of
 * contacts or a single contact.
 */
class CRM_Pdfletterevents_Form_Task_PDF extends CRM_Event_Form_Task {

  /**
   * all the existing templates in the system
   *
   * @var array
   */
  public $_templates = NULL;

  public $_single = NULL;

  public $_cid = NULL;

  /** @var CRM_Pdfletterevents_ParticipantTokenSet */
  private $participantTokenSet;

  /**
   * build all the data structures needed to build the form
   *
   * @return void
   * @access public
   */

  function preProcess() {
    $this->skipOnHold = $this->skipDeceased = FALSE;
    parent::preProcess();
    $this->setContactIDs();
    CRM_Contact_Form_Task_PDFLetterCommon::preProcess($this);

    $this->participantTokenSet = new CRM_Pdfletterevents_ParticipantTokenSet();
  }

  /**
   * Set defaults
   * (non-PHPdoc)
   * @see CRM_Core_Form::setDefaultValues()
   */
  function setDefaultValues() {
    return  CRM_Contact_Form_Task_PDFLetterCommon::setDefaultValues();
  }

  /**
   * Build the form
   *
   * @access public
   *
   * @return void
   */
  public function buildQuickForm() {
    //enable form element
    $this->assign('suppressForm', FALSE);
    CRM_Contact_Form_Task_PDFLetterCommon::buildQuickForm($this);
  }

  /**
   * process the form after the input has been submitted and validated
   *
   * @access public
   *
   * @return None
   */

  public function postProcess() {
    $this->setContactIDs();
    $skipOnHold = isset($this->skipOnHold) ? $this->skipOnHold : FALSE;
    $skipDeceased = isset($this->skipDeceased) ? $this->skipDeceased : TRUE;
    CRM_Pdfletterevents_Form_Task_PDFLetterCommon::postProcessEvents($this, $this->_participantIds, $skipOnHold, $skipDeceased, $this->_contactIds);
  }

  /**
   * list available tokens for event and participants and the tokens for the custom fields of event and participant
   */

  public function listTokens() {

    $eventtokens = CRM_Core_SelectValues::eventTokens();

    $participantTokens = $this->participantTokenSet->getTokens();
    $tokens = $eventtokens + $participantTokens;

    $customtokens = CRM_CORE_BAO_CustomField::getFields('Event');

    foreach ($customtokens as $tokenkey => $tokenvalue) {
      $tokens["{event.custom_$tokenkey}"] = $tokenvalue['label'] . '::' . $tokenvalue['groupTitle'];
    }

    return $tokens;
  }
}


