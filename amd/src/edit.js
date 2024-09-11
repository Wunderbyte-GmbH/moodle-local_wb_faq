
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


import ModalForm from 'core_form/modalform';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import {get_string as getString, get_strings as getStrings} from 'core/str';
import {showSuccessNotification, showErrorNotification} from 'local_wb_faq/notifications';
import {deleteEntry, toggleVisibility} from 'local_wb_faq/admin';
import {reloadData} from 'local_wb_faq/faq';

const SELECTORS = {
    GROUPSUBMIT: '[data-action="groupSubmit"]'
};

/**
 * Gets called from mustache template.
 *
 */
export const init = () => {

    // Find all container.
    const containers = document.querySelectorAll('.local_wb_faq_container');

    containers.forEach(element => {
        if (!element.dataset.initialized) {
            element.addEventListener('click', editModalListener);
            element.dataset.initialized = true;
        }
    });
};

/**
 * Modal listener to open the edit Modals.
 * @param {*} event
 */
const editModalListener = event => {

    let button = event.target;

    if (button.tagName.toLowerCase() === 'i') {
        button = button.parentElement;
    }

    if (button.classList.contains('local_wb_faq_edit_question')) {

        openEditQuestionsModal(event);

    } else if (button.classList.contains('local_wb_faq_delete_question')) {

        confirmDeleteEntry(event);
    } else if (button.classList.contains('local_wb_faq_toggle_entry_visibility')) {

        confirmToggleVisibility(event);
    } else if (button.classList.contains('local_wb_faq_toggle_category_visibility')) {

        confirmToggleVisibility(event);
    } else if (button.classList.contains('local_wb_faq_edit_category')) {

        openEditCategoriesModal(event);

    } else if (button.classList.contains('local_wb_faq_delete_category')) {
        confirmDeleteEntry(event);
    }
};

/**
 * Opens the Modal to edit questions.
 * @param {*} event the click event
 */
 function openEditQuestionsModal(event) {

    let button = event.target;
    let entryid = 0;
    let parentid = 0;
    let uid = 0;

    if (button.tagName.toLowerCase() !== 'a') {
        button = button.parentElement;
    }

    if (button.dataset.id) {
        entryid = button.dataset.id;
    }

    if (button.dataset.faqroot) {
        parentid = button.dataset.faqroot;
    }

    if (button.dataset.uid) {
        uid = button.dataset.uid;
    }

    const modalForm = new ModalForm({

        // Name of the class where form is defined (must extend \core_form\dynamic_form):
        formClass: "local_wb_faq\\form\\editQuestionForm",
        // Add as many arguments as you need, they will be passed to the form:
        args: {
            'id': entryid,
            'type': 1,
            'nobuttons': true,
            'parentid': parentid
        },
        // Pass any configuration settings to the modal dialogue, for example, the title:
        modalConfig: {title: getString('addquestion', 'local_wb_faq')},
        // DOM element that should get the focus after the modal dialogue is closed:
        returnFocus: button
    });

    // Listen to events if you want to execute something on form submit.
    // Event detail will contain everything the process() function returned:
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, (e) => {
        e.preventDefault();
        showSuccessNotification();
        if (uid) {
            reloadData(uid, parentid);
        } else {
            window.location.reload();
        }
        // Properly close the modal after the form is submitted
        modalForm.modal.hide();
    });

    // Show the form.
    modalForm.show().then(() => {

        return;
    }).catch(e => {
        // eslint-disable-next-line no-console
        console.log(e);

        showErrorNotification();
    });
}

/**
 * Opens the Modal to edit questions.
 * @param {*} event the click event
 */
 function openEditCategoriesModal(event) {

    let button = event.target;
    let entryid = 0;
    let parentid = 0;
    let uid = 0;

    if (button.tagName.toLowerCase() !== 'a') {
        button = button.parentElement;
    }

    if (button.dataset.id) {
        entryid = button.dataset.id;
    }

    if (button.dataset.faqroot) {
        parentid = button.dataset.faqroot;
    }

    if (button.dataset.uid) {
        uid = button.dataset.uid;
    }

    const modalForm = new ModalForm({

        // Name of the class where form is defined (must extend \core_form\dynamic_form):
        formClass: "local_wb_faq\\form\\editCategoriesForm",
        // Add as many arguments as you need, they will be passed to the form:
        args: {
            'id': entryid,
            'type': 0,
            'nobuttons': true,
            'parentid': parentid
        },
        // Pass any configuration settings to the modal dialogue, for example, the title:
        modalConfig: {title: getString('addcategory', 'local_wb_faq')},
        // DOM element that should get the focus after the modal dialogue is closed:
        returnFocus: button
    });

    modalForm.addEventListener('change', e => {

        if (e.target.dataset.onChangeAction == "reloadForm") {
            const button = document.querySelector(SELECTORS.GROUPSUBMIT);
            modalForm.processNoSubmitButton(button);
        }
    });

    // Listen to events if you want to execute something on form submit.
    // Event detail will contain everything the process() function returned:
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, (e) => {

        e.preventDefault();

        showSuccessNotification();
        if (uid) {
            reloadData(uid, parentid);
        } else {
            window.location.reload();
        }
        // Properly close the modal after the form is submitted
        modalForm.modal.hide();
    });

    // Show the form.
    modalForm.show().then(() => {

        return;
    }).catch(e => {
        // eslint-disable-next-line no-console
        console.log(e);

        showErrorNotification();
    });
}

/**
 * @param {*} event
 */
 export function confirmDeleteEntry(event) {

    let button = event.target;
    let entryid = 0;

    // We assume we delete a question.
    if (!((button.tagName.toLowerCase() == 'a')
        || (button.tagName.toLowerCase() == 'button'))) {
        button = button.parentElement;
    }
    // No difference at the moment between deleting question or category, but there could be.
    if (button.classList.contains('local_wb_faq_delete_question')) {
        if (button.dataset.id) {
            entryid = button.dataset.id;
        }
    } else if (button.classList.contains('local_wb_faq_delete_category')) {
        if (button.dataset.id) {
            entryid = button.dataset.id;
        }
    } else if (button.dataset
            && button.dataset.action
            && button.dataset.action == 'delete') {

                entryid = button.dataset.targetid;
    } else {
        return;
    }

    getStrings([
        {key: 'confirmdeleteentrytitle', component: 'local_wb_faq'},
        {key: 'confirmdeleteentrybody', component: 'local_wb_faq'},
        {key: 'confirmdeleteentry', component: 'local_wb_faq'}
    ]
    ).then(strings => {

        ModalFactory.create({type: ModalFactory.types.SAVE_CANCEL}).then(modal => {

            modal.setTitle(strings[0]);
                modal.setBody(strings[1]);
                modal.setSaveButtonText(strings[2]);
                modal.getRoot().on(ModalEvents.save, function() {

                    // Looking for the question.
                    let entry = button.closest('li.accordion-item');
                    let elementid = 0;

                    // We need to be looking for the category, not a question.
                    if (!entry) {
                        entry = button.closest('[data-action="goto"]');

                        if (!entry) {
                            // If we still don't find the entry, we are in admin mode.
                            entry = event.target.closest('tr');
                            elementid = button.dataset.targetid;
                        } else {
                            elementid = entry.dataset.targetid;
                        }
                    } else {
                        elementid = entry.dataset.id;
                    }

                    // This is to verify that we've actually found the right dom element.
                    if (elementid == entryid) {
                        entry.remove();
                        // Todo: We should react only on a success response from delete.
                        deleteEntry(entryid);
                        showSuccessNotification();
                    }
                });

                modal.show();
                return modal;
        }).catch(e => {
            // eslint-disable-next-line no-console
            console.log(e);

            showErrorNotification();
        });
        return true;
    }).catch(e => {
        // eslint-disable-next-line no-console
        console.log(e);

        showErrorNotification();
    });
}

/**
 * @param {*} event
 */
 export function confirmToggleVisibility(event) {

    let button = event.target;
    let entryid = 0;

    // We assume we delete a question.
    if (button.tagName.toLowerCase() !== 'a') {
        button = button.parentElement;
    }

    // No difference at the moment between deleting question or category, but there could be.
    if (button.classList.contains('local_wb_faq_toggle_entry_visibility')) {
        if (button.dataset.id) {
            entryid = button.dataset.id;
        }
    } else if (button.classList.contains('local_wb_faq_toggle_category_visibility')) {
        if (button.dataset.id) {
            entryid = button.dataset.id;
        }
    } else {
        return;
    }

    getStrings([
        {key: 'confirmtogglevisibilitytitle', component: 'local_wb_faq'},
        {key: 'confirmtogglevisibilitybody', component: 'local_wb_faq'},
        {key: 'confirmtogglevisibility', component: 'local_wb_faq'}
    ]
    ).then(strings => {

        ModalFactory.create({type: ModalFactory.types.SAVE_CANCEL}).then(modal => {

            modal.setTitle(strings[0]);
                modal.setBody(strings[1]);
                modal.setSaveButtonText(strings[2]);
                modal.getRoot().on(ModalEvents.save, function() {

                    // Looking for the question.
                    let entry = button.closest('li.accordion-item');
                    let elementid = 0;

                    // We need to be looking for the category, not a question.
                    if (!entry) {
                        entry = button.closest('[data-action="goto"]');
                        elementid = entry.dataset.targetid;
                    } else {
                        elementid = entry.dataset.id;
                    }

                    // This is to verify that we've actually found the right dom element.
                    if (elementid == entryid) {
                        // Todo: We should react only on a success response from delete.
                        toggleVisibility(entryid);

                        let ielement = button.querySelector('i.toggle-visibility');

                        if (ielement) {
                            if (ielement.classList.contains('fa-eye-slash')) {
                                ielement.classList.replace('fa-eye-slash', 'fa-eye');
                            } else {
                                ielement.classList.replace('fa-eye', 'fa-eye-slash');
                            }
                            showSuccessNotification();
                            window.location.reload();
                        } else {
                            window.location.reload();
                        }



                    }
                });

                modal.show();
                return modal;
        }).catch(e => {
            // eslint-disable-next-line no-console
            console.log(e);

            showErrorNotification();
        });
        return true;
    }).catch(e => {
        // eslint-disable-next-line no-console
        console.log(e);

        showErrorNotification();
    });
}