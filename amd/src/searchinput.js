
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
import {increaseCounter} from "local_wb_faq/faqnavbar";

var searcharray = [];

export const searchInput = (inputClass, elementToHide, elementToSearch, uid) => {

    // eslint-disable-next-line no-console
    console.log('searchinput uid: ', uid);

    let input, filter, li, a, i, txtValue;
    input = document.querySelector(inputClass);
    filter = input.value.toUpperCase();
    li = document.querySelectorAll(elementToHide);
    for (i = 0; i < li.length; i++) {
        a = li[i].querySelector(elementToSearch);
        txtValue = a.textContent || a.innerText;

        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
};

export const searchJSON = (listContainer, inputClass, json) => {
    let arr = [];
    // Select Container
    // eslint-disable-next-line no-console
    console.log(listContainer, inputClass, Object.values(json));
    let container = document.querySelector(listContainer);
    let searchVal = document.querySelector(inputClass).value.toUpperCase();

    // Make sure we have parsed json.
    if (typeof json === 'string') {
        json = JSON.parse(json);
    }

    // eslint-disable-next-line no-console
    console.log(searchVal);
    if (searchVal.length > 3) {
        let i = 0;
        Object.values(json).forEach(e=>{
             if ((e.content && e.content.toUpperCase().indexOf(searchVal) > -1)
                || (e.title && e.title.toUpperCase().indexOf(searchVal) > -1)) {
                arr[i] = e;
                if (e.type == 0) {
                    arr[i].iscategory = true;
                }
                i++;
            }
        });

        for (let i = 0; i < arr.length; i++) {

            if (typeof searcharray[i] === 'undefined') {
                render(arr, container);
                break;
            }

            if (arr[i].id != searcharray[i].id) {

                render(arr, container);
                break;
            }
        }
        searcharray = arr;

        if (searcharray.length < 1) {
            getString('noresult', 'local_wb_faq').then(value => {

                // eslint-disable-next-line no-console
                console.log(value);
                container.innerHTML = value;
                return;
            }).catch(e => {
                // eslint-disable-next-line no-console
                console.log(e);
            });
            // If we didn't find anything, we just increase the counter.
            // User might need to search a few times.
            increaseCounter();
        } else {
            // If we had a valid input, we allow to continue to the input form.
            increaseCounter(true);
        }

    } else if (searchVal.length == 0) {

        container.innerHTML = '';
        searcharray = [];

    } else {

        getString('stringtooshort', 'local_wb_faq').then(value => {

            // eslint-disable-next-line no-console
            console.log(value);
            container.innerHTML = value;
            return;
        }).catch(e => {
            // eslint-disable-next-line no-console
            console.log(e);
        });

        searcharray = [];

    }
};

export const init = (searchInputID, listContainer, elementToHide, elementToSearch, json = null) => {
    // eslint-disable-next-line no-console
    console.log("initSearch", searchInputID, listContainer);

    const searchInputElement = document.querySelector(searchInputID);

    if (searchInputElement) {
        searchInputElement.addEventListener('keyup', function() {
            searchJSON(listContainer, searchInputID, json);
        });
    }
};


export const render = (data, container) => {
    // Render
    // eslint-disable-next-line no-console
    console.log(container);

    Templates.renderForPromise('local_wb_faq/searchbox', data).then(({html}) => {
        container.innerHTML = "";
        container.insertAdjacentHTML('afterbegin', html);
        return;
    }).catch(e => {
        // eslint-disable-next-line no-console
        console.log(e);
    });
};