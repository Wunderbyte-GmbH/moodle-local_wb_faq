// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/*
 * @package    local_wunderbyte_table
 * @copyright  Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//
import {get_strings as getstrings} from "core/str";
import Config from "core/config";
import Ajax from "core/ajax";
import Templates from "core/templates";
import MyModal from 'local_wb_faq/custommodal';
import ModalFactory from 'core/modal_factory';
// Import ModalEvents from 'core/modal_events'.

var modal = null;
var counterlimit = 0; // Can be overridden.
var counter = 0;

var SELECTORS = {
  TABS: 'faq-page-navbar-tab a.nav-link',
  FAQTAB: '#faq-nav-pillsone-tab',
  MESSAGETAB: '#faq-nav-pillstwo-tab',
  THANKYOUTAB: '#faq-nav-pillsthree-tab',
};

/**
 * Gets called from mustache template.
 *
 */
export function init() {

  // eslint-disable-next-line no-console
  console.log('init navbar called');
  // eslint-disable-next-line no-console
  console.log(Config);

  addEvents();
}

/**
 * Function to increase counter. Also enables message tab, if counter is high enough.
 */
/**
 *
 * @param {boolean} max
 */
export function increaseCounter(max = false) {

  // If max is true, we enable message.
  if (max) {
    counter = counterlimit;
  }

  counter++;

  // eslint-disable-next-line no-console
  console.log('counter', counter);

  if (counter >= counterlimit) {
    const tabs = document.querySelectorAll(SELECTORS.MESSAGETAB);

    tabs.forEach(tab => {
      tab.classList.remove('disabled');
    });
  }
}

/**
 * Adds Evente to menu button
 */
async function addEvents() {
  let button = document.querySelector("[data-id='wb-faq-navbar-open-modal']");

  if (!button) {

    // eslint-disable-next-line no-console
    console.log('couldnt find button');

    return;
  }

  if (button.initialized) {
    return;
  }

  button.initialized = true;

  // eslint-disable-next-line no-console
  console.log('found button', button);

  button.addEventListener('click', async e => {

    // eslint-disable-next-line no-console
    console.log('found e', e);

    Ajax.call([
      {
        methodname: "local_wb_faq_get_faq_data",
        args: {},
        done: async function(faqdata) {

          if (button.dataset.supportthankyou.length > 0) {
            faqdata.thankyou = button.dataset.supportthankyou;
          }
          const data = await returnDataForModal(faqdata);

          if (!modal) {
            createModal(data, modal);
          } else {
            modal.show();
          }

        },
        fail: function(ex) {
          // eslint-disable-next-line no-console
          console.log(ex);
        },
      },
    ]);
  });
}

/**
 * Function to create modal from data.
 * @param {*} data
 */
async function createModal(data) {

  modal = await ModalFactory.create({
    type: MyModal.TYPE,
    large: true,
    body: Templates.render('local_wb_faq/navbar/body', data),
    footer: '',
  }).then(modal => {
    modal.setRemoveOnClose(false);

    return modal;
  }).catch(e => {
    // eslint-disable-next-line no-console
    console.error(e);
  });

  modal.show();
}

/**
 * Function do create the data structure for the modal.
 * @param {*} faqdata
 * @returns {object}
 */
async function returnDataForModal(faqdata) {

  const loadstrings = [
    {
      key: 'searchfaqs',
      component: 'local_wb_faq',
    },
    {
      key: 'writemessage',
      component: 'local_wb_faq',
    },
  ];

  const strings = await getstrings(loadstrings);

  let data = {
    tabs: [
      {
          "name": strings[0],
          "id": 'one',
          "active": true,
          "success": true
      },
      {
        "name": strings[1],
        "id": 'two',
        "active": false,
        "success": false
      },
    ],
    body: {
      thankyou: faqdata.thankyou ?? false
    }
  };

  data.json = JSON.parse(faqdata.json);
  data.root = faqdata.root;
  data.uid = faqdata.uid;
  data.canedit = false;
  data.allowedit = false;

  return data;
}