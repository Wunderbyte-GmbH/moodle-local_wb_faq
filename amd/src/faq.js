
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


import Templates from 'core/templates';
import {get_string as getString} from 'core/str';
var clicks = 0;

/**
 * Gets called from mustache template.
 *
 * @param {*} data
 * @param {integer} root
 * @param {string} uid
 */
export const init = (data, root, uid) => {
    clicks = 0;
    // eslint-disable-next-line no-console
    console.log('faq.js ', uid);
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
    let select = document.querySelector('.local_wb_faq-' + uid);
    if (select) {
        select.addEventListener('click', (e) => {
            let button = e.target;
            if (button.dataset.toggle == "faqcollapse") {
                clicks++;
                // eslint-disable-next-line no-console
                console.log(clicks);
                if (button.classList.contains('collapsed')) {
                    button.classList.remove('collapsed');
                    document.querySelector(button.dataset.target).classList.add('show');
                    document.querySelector(button.dataset.target).classList.remove('hide');
                } else {
                    button.classList.add('collapsed');
                    document.querySelector(button.dataset.target).classList.add('hide');
                    document.querySelector(button.dataset.target).classList.remove('show');
                }
            }
            if (e.target.dataset.action == "goto") {
                render(e.target.dataset.targetid, data, uid);
            }
        });
    }

    let searchbox = document.querySelector('.wb_faq_searchbox-' + uid);

    // eslint-disable-next-line no-console
    console.log('searchbox', searchbox);

    if (searchbox) {
        searchbox.addEventListener('click', (e) => {
            let button = e.target;
            // eslint-disable-next-line no-console
            if (button.dataset.toggle == "faqcollapse") {
                clicks++;
                // eslint-disable-next-line no-console
                console.log(clicks);
                if (button.classList.contains('collapsed')) {
                    button.classList.remove('collapsed');
                    document.querySelector(button.dataset.target).classList.add('show');
                    document.querySelector(button.dataset.target).classList.remove('hide');
                } else {
                    button.classList.add('collapsed');
                    document.querySelector(button.dataset.target).classList.add('hide');
                    document.querySelector(button.dataset.target).classList.remove('show');
                }
            }
            if (e.target.dataset.action == "goto") {
                render(e.target.dataset.targetid, data, uid);
                document.querySelector('.local_wb_faq-' + uid).scrollIntoView({block: "start", behavior: "smooth"});
            }
        });
    }

    let category = document.querySelector('#id_categorypicker');

    if (category) {
        category.addEventListener('change', () => {
            let faqid = category.value;
            if (category.value == '') {
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

    // Load the specific category data
    console.log(data, id);
    let json = JSON.parse(data);
    let templatedata = json[id];

    // eslint-disable-next-line no-console
    console.log(templatedata);

    if (!templatedata || !templatedata.hasOwnProperty('parentid')) {

        return;
    }

    if (json[templatedata.parentid].title) {
        templatedata.parenttitle = json[templatedata.parentid].title;
    }
    if (templatedata.parentid == '') {
        templatedata.parenttitle = getString('faq', 'local_wb_faq');
    }


    // eslint-disable-next-line no-console
    console.log(templatedata);

    // Select Container
    let container = document.querySelector('.local_wb_faq-' + uid);
    // Empty Container
    container.innerHTML = "";
    // Render
    Templates.renderForPromise('local_wb_faq/faq', templatedata).then(({html}) => {

        container.insertAdjacentHTML('afterbegin', html);
        return;
    }).catch(e => {
        // eslint-disable-next-line no-console
        console.log(e);
    });
}
