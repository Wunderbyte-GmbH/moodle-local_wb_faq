
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

export const searchJSON = (json) => {
    input = document.querySelector(listContainer);
    filter = input.value.toUpperCase();
    
};

export const init = (searchInputID, listContainer, elementToHide, elementToSearch) => {
    document.getElementById(searchInputID).addEventListener('keyup', function () {
        searchInput(listContainer, elementToHide, elementToSearch);
    });
    document.getElementById(searchInputID).addEventListener('keyup', function () {
        searchJSON(listContainer, elementToHide, elementToSearch);
    });
};
