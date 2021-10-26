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

import defaultOptions from "./local/default-options";
import ConsentBox from "./local/consent-box";
import CookieHelper from "./local/cookie-helper";
import Observable from "./local/observable";

export default class Guestconsent extends Observable {
    constructor(options = {}) {
        // Since there must only be one instance (one consent box),
        // we will indicate user that it is not OK to create many
        // instances. Because of the option Object that may vary,
        // we cannot just return original instance, users would be
        // confused.
        if (Guestconsent._instance) {
            return Guestconsent._instance;
        }
        super(Guestconsent._emitter);
        Guestconsent._instance = this;


        this.options = Object.assign(defaultOptions, options);
        this._consentBox = new ConsentBox(this.options);
        this._cookie = new CookieHelper(this.options.cookie);

        this._consentBox.on("accept-all", () => {
            this._consentBox.close();
            this._cookie.status = "accepted";
            this._cookie.acceptedCategories = Object.keys(this.options.categories);
            this._cookie.save();
            this.emit("accept-all");
            this.emitConsentChanged();
        });

        this._consentBox.on("accept-selected", () => {
            this._consentBox.close();
            this._cookie.status = "accepted";
            this._cookie.acceptedCategories = this._consentBox.selectedCategories();
            this._cookie.save();
            this.emit("accept-cookies", this._consentBox.selectedCategories());
            this.emitConsentChanged();
        });

        this._consentBox.on("reject", () => {
            this._consentBox.close();
            this._cookie.status = "rejected";
            this._cookie.acceptedCategories = [];
            this._cookie.save();
            this.emit("reject-cookies");
            this.emitConsentChanged();
        });

        if (!this._cookie.status) {
            this._consentBox.open();
        }
    }

    get status() {
        return this._cookie.status;
    }

    get acceptedCategories() {
        return this._cookie.acceptedCategories;
    }

    open() {
        this._consentBox.open();
    }

    emit(event) {
        super.emit(event, this);
    }

    emitConsentChanged() {
        this.emit("user_cookie_consent_changed", {
            'consent' : this._cookie.status,
            'acceptedCategories':  this._cookie.acceptedCategories
        });
    }
}

// Static level properties, since class level static properties are still a
// proposal, we use Object.defineProperties.
Object.defineProperties(Guestconsent, {
    open: {
        value() {
            if (!this._instance) {
                throw new Error("You must initialize a CookieConsent instance before opening.");
            }

            this._instance.open();
        }
    },
    status: {
        get() {
            return this._instance ? this._instance.status : CookieHelper.DEFAULT_STATUS;
        }
    },
    acceptedCategories: {
        get() {
            return this._instance ? this._instance.acceptedCategories : CookieHelper.DEFAULT_ACCEPTED_CATEGORIES;
        }
    },
});
