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
    // TODO: rewrite using contribution token and one letter by contribution
    $this->setContactIDs();
    $skipOnHold = isset($this->skipOnHold) ? $this->skipOnHold : FALSE;
    $skipDeceased = isset($this->skipDeceased) ? $this->skipDeceased : TRUE;
    CRM_Pdfletterevents_Form_Task_PDFLetterCommon::postProcessEvents($this, $this->_participantIds, $skipOnHold, $skipDeceased, $this->_contactIds);
  }

  /**
   * list available tokens, at time of writing these were
   * {event.event_id} => Event ID'
   * {event.title} => Event Title
   * {event.start_date} => Event Start Date
   * {event.end_date} => Event End Date
   * {event.event_type} => Event Type
   * {event.summary} => Event Summary
   * {event.description} => Event Description
   * {event.contact_email} => Event Contact Email
   * {event.contact_phone} => Event Contact Phone
   * {event.location} => Event Location
   * {event.description} => Event Description
   * {event.location} => Event Location
   * {event.fee_amount} => Event Fees
   * {event.info_url} => Event Info URL
   * {event.registration_url} => Event Registration URL
   * @return Ambigous <NULL, multitype:string Ambigous <string, string> >
   */
  public function listTokens() {


    $eventtokens = CRM_Core_SelectValues::eventTokens();

    $participanttokens = CRM_Core_SelectValues::participantTokens();
    $tokens = $eventtokens + $participanttokens;

     $customtokens = CRM_CORE_BAO_CustomField::getFields('Event');
     //CRM_CORE_Error::debug('customtokens', $customtokens);
    foreach ($customtokens as $tokenkey => $tokenvalue) {
$tokens["{event.custom_$tokenkey}"] = $tokenvalue['label'] . '::' . $tokenvalue['groupTitle'];
    }
     //CRM_CORE_Error::debug('tokens', $tokens);
    return $tokens;
    //r//eturn CRM_Core_SelectValues::eventTokens();
  }
}


