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
// import {get_string as getString} from "core/str";

import Ajax from "core/ajax";
import Templates from "core/templates";
import MyModal from 'local_wb_faq/custommodal';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';

/**
 * Gets called from mustache template.
 *
 */
export function init() {

  // eslint-disable-next-line no-console
  console.log('init navbar called');

  addEvents();
}

/**
 * Adds Evente to menu button
 */
function addEvents() {
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
  var modal = null;

  // eslint-disable-next-line no-console
  console.log('found button', button);

  button.addEventListener('click', async e => {

    // eslint-disable-next-line no-console
    console.log('found e', e);

    let data = {
      tabs: [
        {
            "name": "x",
            "active": true,
            "success": true
        },
        {
          "name": "y",
          "active": false,
          "success": false
        }
      ],
      body: {
        text: 'my js text',
      }
    };

    Ajax.call([
      {
        methodname: "local_wb_faq_get_faq_data",
        args: {},
        done: async function (faqdata) {

          data.json = JSON.parse(faqdata.json);
          data.root = faqdata.root;
          data.uid = faqdata.uid;
          data.canedit = false;
          data.allowedit = false;

          if (!modal) {
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
          }

          modal.show();

          modal.getRoot().on(ModalEvents.hidden, (e) => {
            // eslint-disable-next-line no-console
            console.log('modal dismissed', e);
          });

          modal.getRoot().on(ModalEvents.destroyed, (e) => {
            // eslint-disable-next-line no-console
            console.log('modal destroyed', e);
          });
        },
        fail: function (ex) {
          // eslint-disable-next-line no-console
          console.log(ex);
        },
      },
    ]);
  });
}