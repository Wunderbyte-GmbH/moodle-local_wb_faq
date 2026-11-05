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
 * Drag and drop of categories and questions.
 *
 * @module     local_wb_faq/reordering
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';
import Notification from 'core/notification';
import SortableList from 'core/sortable_list';
import jQuery from 'jquery';

/**
 * Returns the ids in their current order. A Set, as the copy following the mouse can still be in the list.
 *
 * @param {HTMLElement} list
 * @returns {number[]}
 */
const getIdOrder = list => {
    const items = list.querySelectorAll(':scope > li[data-id]');

    return [...new Set(Array.from(items, item => Number(item.dataset.id)))];
};

/**
 * Makes the lists of a FAQ instance sortable.
 *
 * @param {string} uid
 */
export const init = uid => {
    const region = document.querySelector(`[data-region="wb-faq"][data-uid="${uid}"]`);

    region.querySelectorAll('[data-region="faq-sortable"]').forEach(list => {
        new SortableList(list);

        jQuery(list).on(SortableList.EVENTS.DROP, (event, info) => {
            if (info.positionChanged) {
                fetchMany([{
                    methodname: 'local_wb_faq_set_wbfaq_order',
                    args: {ids: getIdOrder(list)},
                }])[0].catch(Notification.exception);
            }
        });
    });
};
