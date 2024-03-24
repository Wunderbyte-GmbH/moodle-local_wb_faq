<?php
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
 * local wb_faq
 *
 * @package     local_wb_faq
 * @author      Thomas Winkler
 * @copyright   2022 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_wb_faq;

use cache_helper;
use context_system;
use stdClass;

/**
 * Class jwt
 * @author      Georg Maißer
 * @copyright   2024 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class jwt {

    public $header = [
        'typ' => 'JWT',
        'alg' => 'HS256',
    ];

    /**
     * Return token.
     * @param array $header
     * @param array $payload
     * @return string
     */
    public function return_token(array $payload) {

        // Create token header as a JSON string.
        $header = json_encode($this->header);

        $payload['iss'] = get_config('local_wb_faq', 'jwtapp');
        $payload['aud'] = 'KOI';
        $payload['iat'] = time();
        $payload['exp'] = strtotime('now + 5 min');

        // Create token payload as a JSON string.
        $payload = json_encode($payload);

        // Encode Header to Base64Url String.
        $base64urlheader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Encode Payload to Base64Url String.
        $base64urlpayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Create Signature Hash.
        $signature = hash_hmac(
            'sha256',
            $base64urlheader . "." . $base64urlpayload,
            get_config('local_wb_faq', 'jwtsecret'),
            true);

        // Encode Signature to Base64Url String.
        $base64urlsignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // Create JWT.
        $jwt = $base64urlheader . "." . $base64urlpayload . "." . $base64urlsignature;

        return $jwt;
    }
}
