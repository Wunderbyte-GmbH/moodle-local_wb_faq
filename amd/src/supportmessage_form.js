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
 * @package    local_wb_faq
 * @copyright  Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import DynamicForm from 'core_form/dynamicform';
import {showErrorNotification} from 'local_wb_faq/notifications';

const SELECTORS = {
    SUPPORTMESSAGEFORM: "[data-id='supportmessage-form']",
    CREATETICKETBUTTON: "input.wb-faq-create-ticket-button",
};

/**
 * Initialize the form with event listener that update url params.
 */
export const init = () => {

    const elements = document.querySelectorAll(SELECTORS.SUPPORTMESSAGEFORM);

    elements.forEach(element => {
        listenToSelect(element);
    });
};

/**
 * Set an eventlistener for a select.
 *  @param {*} element
 *
 */
export async function listenToSelect(element) {
        // Initialize the form - pass the container element and the form class name.
        const dynamicForm = new DynamicForm(
            element,
            'local_wb_faq\\form\\supportmessage');

        await dynamicForm.load();

        const button = document.querySelector(SELECTORS.CREATETICKETBUTTON);
        button.classList.remove('btn-primary');
        button.classList.add('btn-outline-primary');

        // If a user selects a context, redirect to a URL that includes the selected
        // context as `contextid` query parameter
        dynamicForm.addEventListener(dynamicForm.events.FORM_SUBMITTED, (e) => {

            e.preventDefault();

            if (e.detail.baseurl && e.detail.token) {
                const url = e.detail.baseurl + "jwtaccess?jwt=" + e.detail.token;
                window.open(url, '_blank');

                setTimeout(() => {
                    window.location.reload();
                }, 400);
            } else {
                // eslint-disable-next-line no-console
                console.error('invalidredirect');
                showErrorNotification();
            }
        });
}
