
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

export const searchInput = (listContainer, elementToHide, elementToSearch) => {
    let input, filter, li, a, i, txtValue;
    input = document.querySelector(listContainer);
    filter = input.value.toUpperCase();
    li = document.querySelectorAll(elementToHide);
    for (i = 0; i < li.length; i++) {
        a = li[i].querySelector(elementToSearch);
        txtValue = a.textContent || a.innerText;
        console.log(txtValue);
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
};

export const searchJSON = (listContainer, json) => {
    let arr = [];
    // Select Container
    let container = document.getElementById('wb_faq_searchbox');
    // Empty Container
    container.innerHTML = "";
    let searchVal = document.querySelector(listContainer).value.toUpperCase();
    if (searchVal.length > 3) {
        let i = 0;
        Object.values(json).forEach(e=>{
             if((e.content && e.content.toUpperCase().indexOf(searchVal) > -1) || (e.title && e.title.toUpperCase().indexOf(searchVal) > -1)) {
                arr[i] = e;
                if (e.type == 0) {
                    arr[i].iscategory = true;
                }
                i++;
            }
        });
        render(arr, container);
    }
};

export const init = (searchInputID, listContainer, elementToHide, elementToSearch, json = null) => {
    /*document.getElementById(searchInputID).addEventListener('keyup', function () {
        searchInput(listContainer, elementToHide, elementToSearch);
    }); */
    document.getElementById(searchInputID).addEventListener('keyup', function () {
        searchJSON(listContainer, json);
    });
};


export const render = (data, container) => {
    // Render
    Templates.renderForPromise('local_wb_faq/searchbox', data).then(({html}) => {
        container.innerHTML = "";
        container.insertAdjacentHTML('afterbegin', html);
    });
}