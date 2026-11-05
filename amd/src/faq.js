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
 * Loads categories, questions, search results and pages of a FAQ instance without reloading the page.
 *
 * The links in the templates are real links, this module takes them over when it is active.
 *
 * @module     local_wb_faq/faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';
import Pending from 'core/pending';
import Templates from 'core/templates';

/** Names of the url parameters, see classes/local/page_url.php. */
const PARAMS = {category: 'faqcat', entry: 'faqentry', page: 'faqpage', query: 'faqq'};

const SEARCH_DELAY = 300;

/** Only one instance can own the url of the page. */
let historyOwner = null;

/** Controllers of the instances on this page, by uid. */
const instances = new Map();

/**
 * A FAQ instance on the page.
 */
class Faq {
    /**
     * Constructor.
     *
     * @param {HTMLElement} region
     */
    constructor(region) {
        this.region = region;
        this.content = region.querySelector('[data-region="faq-content"]');
        this.status = region.querySelector('[data-region="faq-status"]');
        this.searchInput = region.querySelector('[data-region="faq-search-input"]');
        this.searchHint = region.querySelector('[data-region="faq-search-hint"]');
        this.requestCount = 0;
        this.searchTimer = null;

        const data = region.dataset;
        this.config = {
            uid: data.uid,
            root: Number(data.root),
            allowedit: data.allowedit === '1',
            loginrequired: data.nologin !== '1',
            pageurl: data.pageurl,
            minlength: Number(data.minlength),
        };
        this.state = {
            category: Number(data.categoryid),
            entry: Number(data.entryid),
            page: Number(data.page),
            query: data.query,
        };
        this.usesHistory = data.history === '1' && historyOwner === null;
        if (this.usesHistory) {
            historyOwner = this;
        }

        this.addListeners();

        if (this.usesHistory) {
            window.history.replaceState(this.state, '', window.location.href);
            this.loadStateFromUrl();
        }
    }

    /**
     * Adds the event listeners.
     */
    addListeners() {
        this.region.addEventListener('click', e => this.handleClick(e));

        const form = this.region.querySelector('[data-region="faq-search-form"]');
        if (form) {
            form.addEventListener('submit', e => {
                e.preventDefault();
                window.clearTimeout(this.searchTimer);
                this.search();
            });
            this.searchInput.addEventListener('input', () => {
                window.clearTimeout(this.searchTimer);
                this.searchTimer = window.setTimeout(() => this.search(), SEARCH_DELAY);
            });
        }

        if (this.usesHistory) {
            window.addEventListener('popstate', e => {
                const state = e.state ?? this.readUrl();
                this.setSearchValue(state.query);
                this.load(state, false);
            });
        }
    }

    /**
     * Handles clicks on the links of the FAQ.
     *
     * @param {MouseEvent} e
     */
    handleClick(e) {
        const element = e.target.closest('[data-action]');
        if (!element || e.ctrlKey || e.metaKey || e.shiftKey) {
            return;
        }

        switch (element.dataset.action) {
            case 'goto':
                e.preventDefault();
                this.setSearchValue('');
                this.load({category: Number(element.dataset.categoryid), entry: 0, page: 0, query: ''});
                break;
            case 'page':
                e.preventDefault();
                this.load({...this.state, entry: 0, page: Number(element.dataset.page)});
                break;
            case 'toggle-entry':
                e.preventDefault();
                this.toggleEntry(element);
                break;
        }
    }

    /**
     * Executes the search for the content of the search field.
     */
    search() {
        const query = this.searchInput.value.trim();
        if (query === this.state.query) {
            return;
        }

        const tooshort = query.length > 0 && query.length < this.config.minlength;
        this.searchHint.classList.toggle('d-none', !tooshort);
        if (tooshort) {
            return;
        }

        // While typing, only the first request creates a new step in the browser history.
        const pushHistory = this.state.query === '';
        const category = this.state.category;
        this.load({category, entry: 0, page: 0, query}, pushHistory);
    }

    /**
     * Sets the content of the search field.
     *
     * @param {string} value
     */
    setSearchValue(value) {
        if (this.searchInput) {
            this.searchInput.value = value;
            this.searchHint.classList.add('d-none');
        }
    }

    /**
     * Calls a webservice of the plugin.
     *
     * @param {string} methodname
     * @param {object} args
     * @returns {Promise}
     */
    call(methodname, args) {
        return Ajax.call([{methodname, args}], true, this.config.loginrequired)[0];
    }

    /**
     * Loads the fragment of a state and displays it.
     *
     * @param {object} state
     * @param {boolean} pushHistory Whether this is a new step in the browser history.
     * @returns {Promise}
     */
    load(state, pushHistory = true) {
        const pending = new Pending('local_wb_faq/faq:load');
        const request = ++this.requestCount;
        const isSearch = state.query !== '';

        this.content.setAttribute('aria-busy', 'true');
        this.region.classList.add('wb-faq-loading');

        const common = {rootid: this.config.root, page: state.page, uid: this.config.uid, pageurl: this.config.pageurl};
        const fragment = isSearch
            ? this.call('local_wb_faq_search', {...common, query: state.query})
            : this.call('local_wb_faq_get_category', {
                ...common,
                categoryid: state.category,
                entryid: state.entry,
                allowedit: this.config.allowedit,
            });

        // The webservice returns a jQuery promise, which has no finally().
        return Promise.resolve(fragment).then(data => {
            const template = isSearch ? 'local_wb_faq/search_results' : 'local_wb_faq/faq';
            return Templates.renderForPromise(template, data).then(({html, js}) => {
                // An answer to an older request must not replace the answer to a newer one.
                if (request !== this.requestCount) {
                    return;
                }
                Templates.replaceNodeContents(this.content, html, js);
                this.applyState(state, data, isSearch, pushHistory);
            });
        }).catch(error => {
            Notification.exception(error);
        }).finally(() => {
            if (request === this.requestCount) {
                this.content.setAttribute('aria-busy', 'false');
                this.region.classList.remove('wb-faq-loading');
            }
            pending.resolve();
        });
    }

    /**
     * Stores the loaded state and updates url and screen reader status.
     *
     * @param {object} state
     * @param {object} data The answer of the webservice.
     * @param {boolean} isSearch
     * @param {boolean} pushHistory
     */
    applyState(state, data, isSearch, pushHistory) {
        // The server knows the category and page of a question, and moves invalid pages into the valid range.
        this.state = {
            category: isSearch ? state.category : data.categoryid,
            entry: isSearch ? 0 : data.entryid,
            page: data.page,
            query: state.query,
        };
        if (this.usesHistory) {
            this.writeUrl(pushHistory);
        }
        if (this.status) {
            this.status.textContent = isSearch ? data.total : data.title;
        }
        this.region.dispatchEvent(new CustomEvent('local_wb_faq:loaded', {bubbles: true}));
    }

    /**
     * Reloads the current fragment, for example after a change.
     *
     * @returns {Promise}
     */
    reload() {
        return this.load(this.state, false);
    }

    /**
     * Opens or closes a question. The answer is loaded the first time it gets opened.
     *
     * @param {HTMLElement} toggle
     */
    toggleEntry(toggle) {
        const item = toggle.closest('[data-region="faq-entry"]');
        const panel = item.querySelector('[data-region="faq-entry-content"]');
        const id = Number(item.dataset.id);
        const opening = toggle.getAttribute('aria-expanded') !== 'true';

        const show = () => {
            toggle.setAttribute('aria-expanded', opening ? 'true' : 'false');
            toggle.classList.toggle('collapsed', !opening);
            panel.classList.toggle('show', opening);
            this.state = {...this.state, entry: opening ? id : 0};
            if (this.usesHistory && this.state.query === '') {
                this.writeUrl(opening);
            }
        };

        if (!opening || item.dataset.loaded === '1') {
            show();
            return;
        }

        const pending = new Pending('local_wb_faq/faq:toggleentry');
        Promise.resolve(this.call('local_wb_faq_get_entry', {id, rootid: this.config.root})).then(({content}) => {
            const body = panel.querySelector('[data-region="faq-entry-body"]');
            // The wrapper is needed, as text without a leading tag would be taken for a selector.
            return Templates.replaceNodeContents(body, `<div>${content}</div>`, '');
        }).then(() => {
            item.dataset.loaded = '1';
            show();
            return null;
        }).catch(Notification.exception).finally(() => pending.resolve());
    }

    /**
     * Returns the state described by the url.
     *
     * @returns {object}
     */
    readUrl() {
        const params = new URLSearchParams(window.location.search);

        return {
            category: Number(params.get(PARAMS.category) ?? this.config.root),
            entry: Number(params.get(PARAMS.entry) ?? 0),
            page: Number(params.get(PARAMS.page) ?? 0),
            query: params.get(PARAMS.query) ?? '',
        };
    }

    /**
     * Loads the state of the url, if the server did not render it already.
     * This is needed for a FAQ embedded into another page, which always gets rendered at its root.
     */
    loadStateFromUrl() {
        const url = this.readUrl();
        const samePage = url.entry !== 0 || url.page === this.state.page;
        const sameCategory = url.entry !== 0 || url.query !== '' || url.category === this.state.category;
        const same = url.query === this.state.query && url.entry === this.state.entry && samePage && sameCategory;
        if (!same) {
            this.setSearchValue(url.query);
            this.load(url, false);
        }
    }

    /**
     * Writes the current state to the url.
     *
     * @param {boolean} push Whether this is a new step in the browser history.
     */
    writeUrl(push) {
        const url = new URL(window.location.href);
        Object.values(PARAMS).forEach(name => url.searchParams.delete(name));

        const {category, entry, page, query} = this.state;
        if (query !== '') {
            url.searchParams.set(PARAMS.query, query);
        } else if (category !== this.config.root) {
            url.searchParams.set(PARAMS.category, category);
        }
        if (entry) {
            url.searchParams.set(PARAMS.entry, entry);
        }
        if (page) {
            url.searchParams.set(PARAMS.page, page);
        }

        if (push) {
            window.history.pushState(this.state, '', url);
        } else {
            window.history.replaceState(this.state, '', url);
        }
    }
}

/**
 * Starts a FAQ instance.
 *
 * @param {string} uid
 */
export const init = uid => {
    const region = document.querySelector(`[data-region="wb-faq"][data-uid="${uid}"]`);
    if (region && !instances.has(uid)) {
        instances.set(uid, new Faq(region));
    }
};

/**
 * Reloads the displayed fragment of a FAQ instance.
 *
 * @param {string} uid
 * @returns {Promise}
 */
export const reload = uid => instances.get(uid).reload();
