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
export default {
    cookie: {
        // Name of the cookie storing the consent state
        name: "cookie_consent",

        // If null, will take `location.hostname` by default
        domain: null,

        // Duration of the consent
        expiryDays: 365,

        // If true, the cookies will only be allowed over https
        secure: false,

        // See https://developer.mozilla.org/fr/docs/Web/HTTP/Headers/Set-Cookie/SameSite
        sameSite: "Lax",
    }
};
