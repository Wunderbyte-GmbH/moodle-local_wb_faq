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


var allPlayers = Array.from(document.querySelectorAll(".list-group-item"));
var checked = {};
var categories = [];
/**
 * Initial function
 * @param {string} filtercontainer
 * @param {string} singledatacontainer
 */
export const init = (filtercontainer, singledatacontainer) => {
  var allCheckboxes = document.querySelectorAll("input[type=checkbox]");

  document.querySelectorAll(".form-group").forEach(function (e) {
    categories.push(e.getAttribute("name"));
    getChecked(e.getAttribute("name"));
  });

  Array.prototype.forEach.call(allCheckboxes, function (el) {
    el.addEventListener("change", toggleCheckbox);
  });
};

/**
 * Eventhandler
 *
 * @param {*} e - event
 */
export const toggleCheckbox = (e) => {
  getChecked(e.target.name);
  setVisibility();
};

/**
 * Check which Checkboxes are selected inside a group.
 *
 * @param {*} name of Element group
 */
export const getChecked = (name) => {
  checked[name] = Array.from(
    document.querySelectorAll("input[name=" + name + "]:checked")
  ).map(function (el) {
    return el.value;
  });
};

/**
 * Compares checked boxes with classes of Elements and shows or hides them.
 */
export const setVisibility = () => {
  /* eslint-disable no-console */
  console.log(categories);
  allPlayers.forEach(function (el) {
    let display = true;
    categories.forEach(function (c) {
      /* eslint-disable no-console */
      console.log(el.classList);
      let intersection = checked[c].length
        ? Array.from(Object.values(el.dataset)).filter((x) =>
            checked[c].includes(x)
          ).length
        : true;
      if (!intersection) {
        display = false;
        return;
      }
    });
    if (display) {
      el.style.display = "block";
    } else {
      el.style.display = "none";
    }
  });
};
