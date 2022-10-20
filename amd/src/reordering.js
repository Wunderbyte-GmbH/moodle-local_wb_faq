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
 * Javascript for sorting columns in question bank view.
 *
 * @copyright  2022 Wunderbyte GmbH
 * @author     Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 import {call as fetchMany} from 'core/ajax';
 import SortableList from 'core/sortable_list';
 import jQuery from 'jquery';

 /**
  * Sets up sortable list in the column sort order page.
  * @param {Element} listRoot
  * @param {String} identifier
  */
 const initSortableLists = (listRoot, identifier) => {
     new SortableList(listRoot);

     jQuery(identifier + ' > *').on(SortableList.EVENTS.DROP, function(evt, info) {
        if (info.positionChanged) {
            const ids = getIdOrder(listRoot);
            reorder(ids);
            listRoot.querySelectorAll('.li').forEach(item => item.classList.remove('active'));
        }
     });

     jQuery(identifier + ' > *').on(SortableList.EVENTS.DRAGSTART, (event) => {
         event.currentTarget.classList.add('active');
     });
 };

 /**
  * Call external function set_order - inserts the updated column in the config_plugins table.
  *
  * @param {Array} ids String that contains column order.
  * @returns {Promise}
  */
 const reorder = ids => fetchMany([{
     methodname: 'local_wb_faq_set_wbfaq_order',
     args: {ids},
 }])[0];

 /**
  * Gets the newly reordered columns to display in the question bank view.
  * @param {Element} listRoot
  * @returns {Array}
  */
 const getIdOrder = listRoot => {
     const columns = Array.from(listRoot.querySelectorAll('li[data-id]'))
         .map(column => column.dataset.id);
    columns.pop();
    return columns;
 };

 /**
  * Initialize module
  * @param {String} identifier unique class or id with # . for columns.
  */
 export const init = identifier => {
     const listRoot = document.querySelector(`${identifier}`);
     initSortableLists(listRoot, identifier);
 };
