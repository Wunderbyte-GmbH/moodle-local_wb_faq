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

/**
 * Opens the FAQ in a modal from the help menu in the navigation bar.
 *
 * @module     local_wb_faq/faqnavbar
 * @copyright  2024 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Modal from 'core/modal';
import Notification from 'core/notification';
import Templates from 'core/templates';
import {get_string as getString} from 'core/str';

const UID = 'navbarmodal';
const SELECTOR = '[data-action="local_wb_faq-open-modal"]';

let modal = null;

/**
 * Creates the modal with the top level of the FAQ.
 *
 * @param {number} minlength
 * @returns {Promise<Modal>}
 */
const createModal = async minlength => {
    const pageurl = M.cfg.wwwroot + '/local/wb_faq/index.php';
    const category = await Ajax.call([{
        methodname: 'local_wb_faq_get_category',
        args: {uid: UID, pageurl},
    }])[0];

    return Modal.create({
        title: getString('faq', 'local_wb_faq'),
        large: true,
        removeOnClose: false,
        body: Templates.render('local_wb_faq/faq_list', {
            uid: UID,
            root: 0,
            categoryid: 0,
            entryid: 0,
            page: 0,
            query: '',
            allowedit: false,
            nologin: false,
            history: false,
            pageurl,
            formaction: pageurl,
            hiddenparams: [],
            minlength,
            showsearch: true,
            category,
        }),
    });
};

/**
 * Starts the menu entry.
 */
export const init = () => {
    const button = document.querySelector(SELECTOR);
    if (!button || button.dataset.initialized) {
        return;
    }
    button.dataset.initialized = '1';

    button.addEventListener('click', async e => {
        e.preventDefault();
        try {
            modal = modal ?? await createModal(Number(button.dataset.minlength));
            modal.show();
        } catch (error) {
            Notification.exception(error);
        }
    });
};
