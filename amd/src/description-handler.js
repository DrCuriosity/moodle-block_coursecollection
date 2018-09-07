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
 * coursecollection description show/hide handler
 *
 * @package    block_coursecollection
 * @copyright  David Thompson <david.thompson@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/url', 'core/str'], function($, url, str) {

    var expandedUrl = url.imageUrl('t/expanded');
    var collapsedUrl = url.imageUrl('t/collapsed')

    return {
        init: function() {
            $('#coursecollection .description').hide();
            $('#coursecollection .name').prepend($('<img alt="" src="' + collapsedUrl + '"/>'));

            $('#coursecollection .name img').click(function(e){

                $desc = $(this).parent().next();
                $desc.toggle();
                $(this).attr('src', $(this).attr('src') == collapsedUrl ? expandedUrl : collapsedUrl);
            })
        }
    };
});
