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

import Templates from "core/templates";

import {increaseCounter} from "local_wb_faq/faqnavbar";
import {get_string as getString} from "core/str";
import Ajax from "core/ajax";
import Notification from 'core/notification';

var clicks = 0;

const faqs = {};

const SELECTORS = {
  SUPPORTMESSAGE_MODULE: 'div[data-id="supportmessage-form"] input[name="module"][type="hidden"]',
  SUPPORTMESSAGE_SUPPLEMENT: 'div[data-id="supportmessage-form"] input[name="group"][type="hidden"]',
};

/**
 * Gets called from mustache template.
 *
 * @param {*} data
 * @param {integer} root
 * @param {string} uid
 */
export const init = (data, root, uid) => {
  clicks = 0;

  // Make sure we never run the same init js twice.
  if (faqs[uid]) {
    return;
  }

  faqs[uid] = true;

  // eslint-disable-next-line no-console
  console.log("faq.js ", uid, root, data);
  render(root, data, uid);
  addEvents(data, root, uid);
};

/**
 * Adds Evente to toggle switch
 * @param {*} data
 * @param {integer} root
 * @param {string} uid
 */
function addEvents(data, root, uid) {
  let select = document.querySelector(".local_wb_faq_container-" + uid);

  if (!select) {
    return;
  }

  if (select.listener) {
    select.removeEventListener("click", select.listener);
  }
  if (select) {
    select.addEventListener(
      "click",
      (select.listener = (e) => {

        setValuesToSelect(e);

        let button = e.target;
        if (button.dataset.toggle == "faqcollapse") {
          clicks++;
          // eslint-disable-next-line no-console
          console.log(clicks);
          if (button.classList.contains("collapsed")) {
            button.classList.remove("collapsed");
            document.querySelector(button.dataset.target).classList.add("show");
            document
              .querySelector(button.dataset.target)
              .classList.remove("hide");
          } else {
            button.classList.add("collapsed");
            document.querySelector(button.dataset.target).classList.add("hide");
            document
              .querySelector(button.dataset.target)
              .classList.remove("show");
          }
        }
        if (e.target.dataset.action == "goto") {
          render(e.target.dataset.targetid, data, uid);
        }
      })
    );
  }

  let searchbox = document.querySelector(".wb_faq_searchbox-" + uid);

  if (searchbox) {
    searchbox.addEventListener("click", (e) => {
      let button = e.target;
      // eslint-disable-next-line no-console
      if (button.dataset.toggle == "faqcollapse") {
        clicks++;

        if (button.classList.contains("collapsed")) {
          button.classList.remove("collapsed");
          document.querySelector(button.dataset.target).classList.add("show");
          document
            .querySelector(button.dataset.target)
            .classList.remove("hide");
        } else {
          button.classList.add("collapsed");
          document.querySelector(button.dataset.target).classList.add("hide");
          document
            .querySelector(button.dataset.target)
            .classList.remove("show");
        }
      }
      if (e.target.dataset.action == "goto") {
        render(e.target.dataset.targetid, data, uid);
        document
          .querySelector(".local_wb_faq-" + uid)
          .scrollIntoView({block: "start", behavior: "smooth"});
      }
    });
  }

  let category = document.querySelector("#id_categorypicker");

  if (category) {
    category.addEventListener("change", () => {
      let faqid = category.value;
      if (category.value == "") {
        faqid = root;
      }

      render(faqid, data);
    });
  }
}

/**
 * Renders template from jsonobject for the categoryid
 * @param {integer} id
 * @param {*} data
 * @param {string} uid
 */
function render(id, data, uid) {

  let json = data;
  if (typeof data === 'string') {
    json = JSON.parse(data);
  }

  let templatedata = json[id] ?? null;

  if (!templatedata) {
    return;
  }

  templatedata.root = id;
  templatedata.uid = uid;

  if (!templatedata || !templatedata.hasOwnProperty("parentid")) {
      getString('entrydeleted', 'local_wb_faq').then(message => {

        Notification.addNotification({
            message,
            type: "warning"
        });
        return;
    }).catch(e => {
        // eslint-disable-next-line no-console
        console.log(e);
    });
    return;
  }

  if (templatedata.parentid == "") {
    templatedata.parenttitle = getString("faq", "local_wb_faq");
  }

  // Select Container
  let container = document.querySelector(".local_wb_faq-" + uid);

  if (!container) {
    return;
  }

  // Empty Container
  container.innerHTML = "";
  // Render
  Templates.renderForPromise("local_wb_faq/faq", templatedata)
    .then(({html, js}) => {

      Templates.replaceNodeContents(".local_wb_faq-" + uid, html, js);

      const breadcrumbs = container.querySelectorAll('.wb-breadcrumb div');
      const last = breadcrumbs[breadcrumbs.length - 1];
      // eslint-disable-next-line no-console
      console.log(last);

      last.classList.remove(['btn-primary']);
      last.classList.add(['btn-nolabel']);
      last.removeAttribute('data-action');

      return;
    })
    .catch((e) => {
      // eslint-disable-next-line no-console
      console.log(e);
    });
}

/**
 * Calls WS Function to get new data after new entries are added
 * @param {string} uid
 * @param {integer} parentid
 */
export const reloadData = (uid, parentid) => {
  loadData(uid, parentid);
};

export const loadData = (uid, parentid) =>
  Ajax.call([
    {
      methodname: "local_wb_faq_get_faq_data",
      args: {},
      done: function(data) {
        let newdata = JSON.parse(data.json);

        if (!uid) {
          return newdata;
        }

        addEvents(newdata, parentid, uid);
        render(parentid, newdata, uid);
        return '';
      },
      fail: function(ex) {
        // eslint-disable-next-line no-console
        console.log(ex);
      },
    },
  ]);

/**
 *
 * @param {Event} e
 */
function setValuesToSelect(e) {

  // With every click, we increase the counter.
  increaseCounter();

  const moduleElement = document.querySelector(SELECTORS.SUPPORTMESSAGE_MODULE);
  const supplementElement = document.querySelector(SELECTORS.SUPPORTMESSAGE_SUPPLEMENT);

  if (moduleElement) {
    // eslint-disable-next-line no-console
    console.log(e.target.dataset.module);
    moduleElement.value = e.target.dataset.module;
  }
  if (supplementElement) {
    // eslint-disable-next-line no-console
    console.log(e.target.dataset.supplement);
    supplementElement.value = e.target.dataset.supplement;
  }
}