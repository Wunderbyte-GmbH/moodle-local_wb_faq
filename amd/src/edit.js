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
 * The edit controls of a FAQ instance.
 *
 * @module     local_wb_faq/edit
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import ModalForm from 'core_form/modalform';
import Notification from 'core/notification';
import {add as addToast} from 'core/toast';
import {get_string as getString} from 'core/str';
import {reload} from 'local_wb_faq/faq';

const FORMS = {
    category: {
        formClass: 'local_wb_faq\\form\\edit_category_form',
        addTitle: 'addcategory',
        editTitle: 'editcategory',
    },
    question: {
        formClass: 'local_wb_faq\\form\\edit_question_form',
        addTitle: 'addquestion',
        editTitle: 'editquestion',
    },
};

/**
 * Tells the user about the success and shows the changed data.
 *
 * @param {string} uid
 * @param {string} stringkey
 * @returns {Promise}
 */
const finish = async(uid, stringkey) => {
    await reload(uid);
    await addToast(getString(stringkey, 'local_wb_faq'));
};

/**
 * Opens the form to add or edit a record.
 *
 * @param {string} uid
 * @param {HTMLElement} control
 */
const openForm = (uid, control) => {
    const form = FORMS[control.dataset.type];
    const id = Number(control.dataset.id ?? 0);
    const region = control.closest('[data-region="wb-faq"]');
    const parentid = Number(region.querySelector('[data-region="faq-current"]').dataset.categoryid);

    const modalForm = new ModalForm({
        formClass: form.formClass,
        args: {id, parentid},
        modalConfig: {title: getString(id ? form.editTitle : form.addTitle, 'local_wb_faq'), large: true},
        returnFocus: control,
    });
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, event => {
        event.preventDefault();
        modalForm.modal.hide();
        finish(uid, 'savesuccess').catch(Notification.exception);
    });
    modalForm.show().catch(Notification.exception);
};

/**
 * Asks for confirmation and then calls a webservice.
 *
 * @param {string} uid
 * @param {HTMLElement} control
 * @param {string} methodname
 * @param {string} stringprefix
 * @param {string} successkey
 * @returns {Promise}
 */
const confirmAndCall = (uid, control, methodname, stringprefix, successkey) => Notification.saveCancelPromise(
    getString(`${stringprefix}title`, 'local_wb_faq'),
    getString(`${stringprefix}body`, 'local_wb_faq'),
    getString(stringprefix, 'local_wb_faq'),
).then(() => Ajax.call([{methodname, args: {id: Number(control.dataset.id)}}])[0])
.then(() => finish(uid, successkey));

/**
 * Starts the edit controls of a FAQ instance.
 *
 * @param {string} uid
 */
export const init = uid => {
    const region = document.querySelector(`[data-region="wb-faq"][data-uid="${uid}"]`);

    region.addEventListener('click', e => {
        const control = e.target.closest('[data-edit-action]');
        if (!control) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();

        // The user cancelling a confirmation is a rejection too, which needs no message.
        const ignoreCancel = error => {
            if (error && error.message !== undefined) {
                Notification.exception(error);
            }
        };

        switch (control.dataset.editAction) {
            case 'form':
                openForm(uid, control);
                break;
            case 'delete':
                confirmAndCall(uid, control, 'local_wb_faq_delete_entry', 'confirmdeleteentry', 'entrydeleted')
                    .catch(ignoreCancel);
                break;
            case 'toggle':
                confirmAndCall(uid, control, 'local_wb_faq_toggle_entry_visibility', 'confirmtogglevisibility', 'savesuccess')
                    .catch(ignoreCancel);
                break;
        }
    });
};
