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
 * Cookie consent policy
 *
 * Derived from https://github.com/klaxit/cookie-consent
 * version 0.3.4
 *
 * @module    local_gprd_plus/cookie
 * @class     Cookie
 * @package   local_gprd_plus
 * @copyright 2021 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Observable from "./observable";

/**
 * Class managing the consent box.
 */
export default class ConsentBox extends Observable {
    constructor(options = {}) {
        super();

        this.options = options;
        this._setUp();
    }

    open() {
        this.container.classList.add("displayed");
    }

    close() {
        this.container.classList.remove("displayed");
    }

    selectedCategories() {
        const categoriesElem = this._firstByClass("cc-categories");
        const selectedCategories = [];
        for (let catElem in categoriesElem.querySelectorAll("[data-category]")) {
            const catKey = catElem.dataset.category;
            if (catElem.getAttribute('aria-checked') === 'true') {
                selectedCategories.push(catKey);
            }
        }
        return selectedCategories;
    }

    _setUp() {
        // Build container & content
        this.container = document.getElementById(this.options.containerId);

        this.container.querySelectorAll('.cc-categories.cc-checkboxes [role="checkbox"]').forEach((element) => {
            element.addEventListener("click", this._categoryClicked.bind(this));
        });
        this._setupButtons();
    }

    _categoryClicked(event) {
        const targetElement = event.target || event.srcElement;
        const targetCatKey = targetElement.dataset.category;

        const descElem = this.container.querySelector(".cc-category-description");
        descElem.innerHTML = this.categories[targetCatKey].description;
    }

    _setupButtons() {
        // Settings buttons
        this.container.querySelectorAll(".cc-btn-settings")
            .forEach((elem) => {
                elem.addEventListener("click", this._toggleSettings.bind(this));
            });

        // Accept buttons
        this.container.querySelectorAll(".cc-btn-accept-all")
            .forEach((elem) => {
                elem.addEventListener("click", () => this.emit("accept-all"));
            });
        this.container.querySelectorAll(".cc-btn-accept-selected")
            .forEach((elem) => {
                elem.addEventListener("click", () => this.emit("accept-selected"));
            });

        // Reject buttons
        this.container.querySelectorAll(".cc-btn-reject")
            .forEach((elem) => {
                elem.addEventListener("click", () => this.emit("reject"));
            });
    }

    _toggleSettings() {
        const landingClassList = this._firstByClass("cc-section-landing").classList;
        const settingsClassList = this._firstByClass("cc-section-settings").classList;

        if (landingClassList.contains("cc-hidden")) {
            landingClassList.remove("cc-hidden");
            settingsClassList.add("cc-hidden");
        } else {
            landingClassList.add("cc-hidden");
            settingsClassList.remove("cc-hidden");
        }
    }

    /**
     * Get the first element matching this class
     * @param className
     * @returns {Element}
     * @private
     */
    _firstByClass(className) {
        const elem = this.container.querySelector(`.${className}:first-child`);
        if (!elem) {
            throw new Error("Cannot find elements for class " + className + ".");
        }
        return elem;
    }

}
