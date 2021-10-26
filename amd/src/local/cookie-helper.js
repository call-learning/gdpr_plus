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

const DEFAULT_STATUS = null;
const DEFAULT_ACCEPTED_CATEGORIES = [];
/**
 * Cookie utility class
 */
export default class CookieHelper {
    constructor(cookieOptions) {
        this.cookieOptions = cookieOptions;
        this.load();
    }

    save() {
        const serialized = JSON.stringify({
            status: this.status,
            acceptedCategories: this.acceptedCategories
        });

        let cookieStr = this.cookieOptions.name + "=" + serialized;

        const expDate = new Date();
        const expDays = this.cookieOptions.expiryDays;
        const expHours = (typeof expDays !== "number" ? 365 : expDays) * 24;
        expDate.setHours(expDate.getHours() + expHours);
        cookieStr += "; expires=" + expDate.toUTCString();

        cookieStr += "; path=/";
        cookieStr += (this.cookieOptions.domain ? "; domain=" + this.cookieOptions.domain : "");
        cookieStr += (this.cookieOptions.secure ? "; secure" : "");
        cookieStr += (this.cookieOptions.sameSite ? "; SameSite=" + this.cookieOptions.sameSite : "");

        document.cookie = cookieStr;
    }

    load() {
        const existingConsent = this._getCookie(this.cookieOptions.name);
        if (existingConsent) {
            const parsed = JSON.parse(existingConsent);
            this.status = parsed.status;
            this.acceptedCategories = parsed.acceptedCategories;
        } else {
            this.status = DEFAULT_STATUS;
            this.acceptedCategories = DEFAULT_ACCEPTED_CATEGORIES;
        }
    }

    _getCookie(cookieName) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${cookieName}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
    }
}

// Make default values public.
Object.defineProperties(CookieHelper, {
    DEFAULT_STATUS: {value: DEFAULT_STATUS, writable: false},
    DEFAULT_ACCEPTED_CATEGORIES: {value: DEFAULT_ACCEPTED_CATEGORIES, writable: false}
});
